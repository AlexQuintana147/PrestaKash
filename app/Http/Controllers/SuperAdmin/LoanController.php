<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');
        $loanId = $request->get('loan_id');
        $search = $request->get('search');
        $currency = $request->get('currency');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = Loan::with(['user.documents', 'user.clientProfile'])->latest();

        if ($loanId) {
            $query->where('id', $loanId);
            $filter = 'all';
        } else {
            if ($filter !== 'all') {
                $query->where('status', $filter);
            }

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($currency) {
                $query->where('currency', $currency);
            }

            if ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            }
        }

        $loans = $query->paginate(10)->withQueryString();

        $counts = [
            'pending' => Loan::where('status', 'pending')->count(),
            'counter_offered' => Loan::where('status', 'counter_offered')->count(),
            'approved' => Loan::where('status', 'approved')->count(),
            'rejected' => Loan::where('status', 'rejected')->count(),
            'paid' => Loan::where('status', 'paid')->count(),
            'all' => Loan::count(),
        ];

        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();

        return view('superadmin.loans.index', compact('loans', 'filter', 'counts', 'cashboxes'));
    }

    public function counterOffer(Request $request, Loan $loan)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'type' => 'required|in:cuotas,unico',
            'amortization_system' => 'required_if:type,cuotas|in:frances,americano',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'months' => 'nullable|integer|min:3|max:24',
            'payment_days' => 'nullable|in:15,30',
            'payment_frequency' => 'nullable|in:diario,semanal,quincenal,mensual',
            'disbursement_date' => 'required|date|after_or_equal:today',
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $amount = (float) $request->amount;

        if ($cashbox->balance < $amount) {
            return back()->withErrors(['cashbox_id' => 'Saldo insuficiente en la caja seleccionada.']);
        }

        $rate = $request->interest_rate / 100;
        $type = $request->type;
        $system = $type === 'cuotas' ? $request->amortization_system : 'frances';
        $months = $type === 'cuotas' ? ($request->months ?? $loan->months ?? 3) : null;
        $paymentDays = $type === 'unico' ? ($request->payment_days ?? $loan->payment_days ?? 15) : null;
        $freq = ($type === 'cuotas' && $system === 'frances') ? ($request->payment_frequency ?? 'mensual') : 'mensual';

        $totalAmount = $amount;
        $periodicPayment = 0;

        $updateData = [
            'amount' => $amount,
            'type' => $type,
            'months' => $months,
            'payment_days' => $paymentDays,
            'amortization_system' => $system,
            'payment_frequency' => $freq,
            'interest_rate' => $request->interest_rate,
            'disbursement_date' => $request->disbursement_date,
            'status' => 'counter_offered',
        ];

        if (!$loan->original_data) {
            $updateData['original_data'] = [
                'amount' => $loan->amount,
                'type' => $loan->type,
                'interest_rate' => $loan->interest_rate,
                'months' => $loan->months,
                'payment_days' => $loan->payment_days,
                'monthly_payment' => $loan->monthly_payment,
                'payment_frequency' => $loan->payment_frequency,
                'amortization_system' => $loan->amortization_system,
                'disbursement_date' => $loan->disbursement_date,
            ];
        }

        if ($type === 'cuotas') {
            if ($system === 'frances') {
                $installmentsPerMonth = match ($freq) {
                    'diario' => 30,
                    'semanal' => 4,
                    'quincenal' => 2,
                    'mensual' => 1,
                    default => 1,
                };

                $nTotal = $months * $installmentsPerMonth;
                $periodicRate = $rate / $installmentsPerMonth;

                if ($amount > 0) {
                    $periodicPayment = ($amount * $periodicRate) / (1 - pow(1 + $periodicRate, -$nTotal));
                    $totalAmount = $periodicPayment * $nTotal;
                } else {
                    $periodicPayment = 0;
                    $totalAmount = 0;
                }
            } else {
                // American: Fixed interest payments, principal at the end
                $interestPerMonth = $amount * $rate;
                $periodicPayment = $interestPerMonth; // Interest only
                $totalAmount = $amount + ($interestPerMonth * $months);
            }
        } else {
            // Pago único
            $interest = $amount * $rate;
            $totalAmount = $amount + $interest;
            $periodicPayment = $totalAmount;
        }

        $updateData['total_amount'] = $totalAmount;
        $updateData['monthly_payment'] = $periodicPayment;

        \DB::transaction(function () use ($loan, $updateData, $cashbox, $amount) {
            $loan->update($updateData);

            // Register movement
            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => "Egreso por contra-oferta de préstamo #{$loan->id} a {$loan->user->name}",
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'date' => now(),
            ]);

            $cashbox->decrement('balance', $amount);
        });

        return back()->with('success', 'Contra-oferta enviada y egreso registrado correctamente.');
    }

    public function approve(Request $request, Loan $loan)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $amount = $loan->amount;

        if ($cashbox->balance < $amount) {
            return back()->withErrors(['cashbox_id' => 'Saldo insuficiente en la caja seleccionada.']);
        }

        \DB::transaction(function () use ($loan, $cashbox, $amount) {
            $loan->update(['status' => 'approved']);

            // Register movement
            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => "Egreso por aprobación de préstamo #{$loan->id} a {$loan->user->name}",
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'date' => now(),
            ]);

            $cashbox->decrement('balance', $amount);

            // Delete existing installments if any
            $loan->installments()->delete();

            $rate = $loan->interest_rate / 100;
            $system = $loan->amortization_system;
            $type = $loan->type;
            $freq = $loan->payment_frequency;
            $months = $loan->months;

            if ($type === 'cuotas') {
                $installmentsPerMonth = match ($freq) {
                    'diario' => 30,
                    'semanal' => 4,
                    'quincenal' => 2,
                    'mensual' => 1,
                    default => 1,
                };

                $nTotal = $months * $installmentsPerMonth;
                $periodicRate = $rate / $installmentsPerMonth;
                $periodicPayment = $loan->monthly_payment;

                $remainingBalance = $amount;
                $currentDate = now();

                for ($i = 1; $i <= $nTotal; $i++) {
                    if ($freq === 'diario')
                        $currentDate->addDay();
                    elseif ($freq === 'semanal')
                        $currentDate->addWeek();
                    elseif ($freq === 'quincenal')
                        $currentDate->addDays(15);
                    else
                        $currentDate->addMonth();

                    $instInterest = 0;
                    $instCapital = 0;

                    if ($system === 'frances') {
                        $instInterest = $remainingBalance * $periodicRate;
                        $instCapital = $periodicPayment - $instInterest;

                        if ($i === $nTotal) {
                            $instCapital = $remainingBalance;
                        }

                        $remainingBalance -= $instCapital;
                    } else {
                        $instInterest = $amount * $periodicRate;
                        $instCapital = ($i === $nTotal) ? $amount : 0;
                    }

                    $loan->installments()->create([
                        'installment_number' => $i,
                        'amount' => $instInterest + $instCapital,
                        'capital' => $instCapital,
                        'interest' => $instInterest,
                        'due_date' => $currentDate->format('Y-m-d'),
                        'status' => 'pending',
                    ]);
                }
            } else {
                $interest = $amount * $rate;
                $loan->installments()->create([
                    'installment_number' => 1,
                    'amount' => $amount + $interest,
                    'capital' => $amount,
                    'interest' => $interest,
                    'due_date' => now()->addDays($loan->payment_days)->format('Y-m-d'),
                    'status' => 'pending',
                ]);
            }
        });

        return back()->with('success', 'Préstamo aprobado, egreso registrado e historial generado correctamente.');
    }

    public function reject(Loan $loan)
    {
        $loan->update(['status' => 'rejected']);
        return back()->with('success', 'Préstamo rechazado.');
    }

    public function installmentsIndex(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $instId = $request->get('installment_id');
        $highlightInterest = $request->get('highlight_interest', false);
        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();

        $query = \App\Models\LoanInstallment::with(['loan.user', 'loan.user.documents', 'interestPayments'])
            ->whereHas('loan', function ($q) {
                $q->whereIn('status', ['approved', 'paid']);
            });

        if ($instId) {
            $query->where('id', $instId);
            $filter = 'all';
        } elseif ($filter === 'grouped') {
            $loans = \App\Models\Loan::with(['user', 'user.documents'])
                ->whereIn('status', ['approved', 'paid'])
                ->latest()
                ->get();

            $groupedInstallments = [];
            foreach ($loans as $loan) {
                $pageName = 'p_' . $loan->id;
                $installments = $loan->installments()
                    ->orderBy('due_date', 'asc')
                    ->paginate(5, ['*'], $pageName)
                    ->withQueryString();

                if ($installments->total() > 0) {
                    $groupedInstallments[] = [
                        'loan' => $loan,
                        'installments' => $installments
                    ];
                }
            }

            return view('superadmin.loans.installments', [
                'groupedInstallments' => $groupedInstallments,
                'filter' => $filter,
                'cashboxes' => $cashboxes
            ]);
        } elseif ($filter === 'pending_validation') {
            $query->where('status', 'processing');
            // For each user/loan combination, only show the most recent installment
            $installments = $query->orderBy('updated_at', 'desc')->get();
            $latestPerUserLoan = [];
            foreach ($installments as $inst) {
                $key = $inst->loan->user_id . '-' . $inst->loan_id;
                if (!isset($latestPerUserLoan[$key])) {
                    $latestPerUserLoan[$key] = $inst;
                }
            }
            $installments = collect($latestPerUserLoan)->values();
            // Skip the expansion logic and pagination for pending_validation
            return view('superadmin.loans.installments', compact('installments', 'filter', 'cashboxes', 'instId', 'highlightInterest'));
        } elseif ($filter === 'paid') {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                  ->orWhere('interest_payments_count', '>', 0);
            });
        }

        $installments = $query->orderBy('due_date', 'asc')
            ->get();

        // Expand installments with interest payments into separate rows
        $expandedInstallments = collect();
        foreach ($installments as $inst) {
            if ($inst->interest_payments_count > 0) {
                // Load actual interest payments
                $interestPayments = $inst->interestPayments;
                // If interest_payments table has data, use it
                if ($interestPayments->isNotEmpty()) {
                    foreach ($interestPayments as $index => $interestPayment) {
                        $virtualInst = clone $inst;
                        $virtualInst->is_interest_payment = true;
                        $virtualInst->interest_payment_number = $index + 1;
                        $virtualInst->total_interest_payments = $inst->interest_payments_count;
                        $virtualInst->interest_payment = $interestPayment;
                        $expandedInstallments->push($virtualInst);
                    }
                } else {
                    // Fallback: create virtual rows without interest_payment object (for backward compatibility)
                    for ($i = 1; $i <= $inst->interest_payments_count; $i++) {
                        $virtualInst = clone $inst;
                        $virtualInst->is_interest_payment = true;
                        $virtualInst->interest_payment_number = $i;
                        $virtualInst->total_interest_payments = $inst->interest_payments_count;
                        $virtualInst->interest_payment = null;
                        $expandedInstallments->push($virtualInst);
                    }
                }
            } else {
                $inst->is_interest_payment = false;
                $expandedInstallments->push($inst);
            }
        }

        // Paginate the expanded collection
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $sliced = $expandedInstallments->slice(($page - 1) * $perPage, $perPage);
        $installments = new LengthAwarePaginator(
            $sliced,
            $expandedInstallments->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $installments->withQueryString();

        return view('superadmin.loans.installments', compact('installments', 'filter', 'cashboxes', 'instId', 'highlightInterest'));
    }

    public function installmentsGroup(Request $request, \App\Models\Loan $loan)
    {
        $page = (int) $request->get('page', 1);

        $installments = $loan->installments()
            ->orderBy('due_date', 'asc')
            ->paginate(5, ['*'], 'page', $page);

        return view('superadmin.loans.partials.installments-group-rows', [
            'loan' => $loan,
            'userInstallments' => $installments,
        ]);
    }

    public function updateLateFee(Request $request, \App\Models\Loan $loan)
    {
        $request->validate([
            'late_fee_enabled' => 'nullable|boolean',
            'grace_days' => 'required|integer|min:0|max:30',
            'late_fee_rate' => 'required|numeric|min:0|max:99.99',
            'late_fee_daily_amount' => 'required|numeric|min:0',
        ]);

        $loan->update([
            'late_fee_enabled' => $request->has('late_fee_enabled'),
            'grace_days' => $request->grace_days,
            'late_fee_rate' => $request->late_fee_rate,
            'late_fee_daily_amount' => $request->late_fee_daily_amount,
            'late_fee_type' => 'diario',
        ]);

        return back()->with('success', 'Configuración de mora actualizada.');
    }

    public function updateOperationNumber(Request $request, \App\Models\LoanInstallment $installment)
    {
        $request->validate([
            'operation_number' => 'nullable|alpha_num|max:25',
        ]);

        $installment->update([
            'operation_number' => $request->operation_number,
        ]);

        return back()->with('success', 'Número de operación actualizado correctamente.');
    }

    public function approveInstallment(Request $request, \App\Models\LoanInstallment $installment)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $isInterestOnly = $installment->requested_type === 'interest';
        $amount = $isInterestOnly
            ? $installment->interest + ($installment->late_fee ?? 0)
            : $installment->amount + ($installment->late_fee ?? 0);

        \DB::transaction(function () use ($installment, $cashbox, $amount, $isInterestOnly) {
            $loan = $installment->loan;

            if ($isInterestOnly) {
                $currentNumber = $installment->installment_number;
                $freq = $loan->payment_frequency;

                // Register movement
                $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por SOLO INTERÉS de cuota #{$currentNumber} de préstamo #{$loan->id} de {$loan->user->name}",
                    'reference_type' => 'loan_installment',
                    'reference_id' => $installment->id,
                    'date' => now(),
                ]);

                $cashbox->increment('balance', $amount);

                // Shift dates for all PENDING or REJECTED installments of this loan
                $installmentsToShift = $loan->installments()
                    ->whereIn('status', ['pending', 'rejected', 'processing'])
                    ->get();

                foreach ($installmentsToShift as $inst) {
                    $date = $inst->due_date->copy();
                    if ($freq === 'diario')
                        $date->addDay();
                    elseif ($freq === 'semanal')
                        $date->addWeek();
                    elseif ($freq === 'quincenal')
                        $date->addDays(15);
                    else
                        $date->addMonth();

                    $inst->update(['due_date' => $date]);
                }

                // Renumber current and subsequent installments (+1)
                $loan->installments()
                    ->where('installment_number', '>=', $currentNumber)
                    ->increment('installment_number');

                // Create new installment at the end
                $lastInst = $loan->installments()->orderBy('installment_number', 'desc')->first();
                $newDate = $lastInst->due_date->copy();
                if ($freq === 'diario')
                    $newDate->addDay();
                elseif ($freq === 'semanal')
                    $newDate->addWeek();
                elseif ($freq === 'quincenal')
                    $newDate->addDays(15);
                else
                    $newDate->addMonth();

                $loan->installments()->create([
                    'installment_number' => $lastInst->installment_number + 1,
                    'amount' => $lastInst->amount,
                    'capital' => $lastInst->capital,
                    'interest' => $lastInst->interest,
                    'due_date' => $newDate,
                    'status' => 'pending',
                ]);

                // Refresh to get the incremented installment_number
                $installment->refresh();

                // Reset current installment and mark interest payment
                $installment->update([
                    'status' => 'pending',
                    'payment_voucher' => null,
                    'payment_method' => null,
                    'bank_name' => null,
                    'operation_number' => null,
                    'requested_type' => 'full',
                    'paid_at' => null,
                    'interest_payments_count' => $installment->interest_payments_count + 1,
                ]);

            } else {
                $installment->update([
                    'status' => 'approved',
                    'paid_at' => now()
                ]);

                // Register movement
                $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por pago de cuota #{$installment->installment_number} de préstamo #{$loan->id} de {$loan->user->name}",
                    'reference_type' => 'loan_installment',
                    'reference_id' => $installment->id,
                    'date' => now(),
                ]);

                $cashbox->increment('balance', $amount);

                // Check if all installments are paid
                $remaining = $loan->installments()->where('status', '!=', 'approved')->count();
                if ($remaining === 0) {
                    $loan->update(['status' => 'paid']);
                }
            }
        });

        return back()->with('success', $isInterestOnly ? 'Pago de interés aprobado y fechas postergadas.' : 'Pago aprobado e ingreso registrado correctamente.');
    }

    public function rejectInstallment(Request $request, \App\Models\LoanInstallment $installment)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        // Delete voucher from storage
        if ($installment->payment_voucher) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($installment->payment_voucher);
        }

        $installment->update([
            'status' => 'rejected',
            'payment_voucher' => null,
            'admin_notes' => $request->reason,
            'paid_at' => null
        ]);

        return back()->with('success', 'Pago rechazado. El cliente deberá subirlo nuevamente.');
    }

    public function unapproveInstallment(\App\Models\LoanInstallment $installment)
    {
        // Solo se puede revertir la última cuota aprobada del préstamo
        $lastApproved = $installment->loan->installments()
            ->where('status', 'approved')
            ->orderBy('installment_number', 'desc')
            ->first();

        if (!$lastApproved || $lastApproved->id !== $installment->id) {
            return back()->withErrors(['unapprove' => 'Solo puedes revertir el último pago aprobado del préstamo.']);
        }

        \DB::transaction(function () use ($installment) {
            $installment->update(['status' => 'processing']);

            $loan = $installment->loan;
            if ($loan->status === 'paid') {
                $loan->update(['status' => 'approved']);
            }

            // Find and delete the associated cashbox movement
            $movement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                ->where('reference_id', $installment->id)
                ->first();

            if ($movement) {
                $cashbox = $movement->cashbox;
                if ($cashbox) {
                    $cashbox->decrement('balance', $movement->amount);
                }
                $movement->delete();
            }
        });

        return back()->with('success', 'Aprobación revertida. El ingreso en caja ha sido eliminado y el pago vuelve a estar en revisión.');
    }
}
