<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\VoucherCleanupService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

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

        $query = Loan::with(['user.documents', 'user.clientProfile', 'user.bankAccounts'])->latest();

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
            'counter_accepted' => Loan::where('status', 'counter_accepted')->count(),
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
            'months' => 'nullable|integer|min:2|max:24',
            'payment_days' => 'nullable|in:15,30',
            'payment_frequency' => 'nullable|in:diario,semanal,quincenal,mensual',
            'disbursement_date' => 'required|date|after_or_equal:today',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'currency' => 'required|in:PEN,USD',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);

        if ($cashbox->currency !== $request->currency) {
            return back()->withErrors(['cashbox_id' => 'La caja seleccionada no corresponde a la moneda elegida.']);
        }
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
            'currency' => $request->currency,
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
                'currency' => $loan->currency,
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
        $updateData['cashbox_id'] = $request->cashbox_id;

        \DB::transaction(function () use ($loan, $updateData) {
            $loan->update($updateData);
        });

        return back()->with('success', 'Contra-oferta enviada correctamente. Se registrará el egreso cuando el cliente acepte.');
    }

    public function approve(Request $request, Loan $loan)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
            'disbursement_voucher' => 'required|image|max:5120',
            'disbursement_operation_number' => 'required|string|max:30',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $amount = $loan->amount;

        if ($cashbox->balance < $amount) {
            return back()->withErrors(['cashbox_id' => 'Saldo insuficiente en la caja seleccionada.']);
        }

        // Process voucher image and convert to WebP
        $voucherPath = null;
        if ($request->hasFile('disbursement_voucher')) {
            $file = $request->file('disbursement_voucher');
            $filename = 'voucher_' . $loan->code . '_' . time() . '.webp';
            
            // Read the image
            $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
            
            // Convert to WebP with 85% quality
            $fullPath = storage_path('app/public/vouchers/' . $filename);
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            imagewebp($image, $fullPath, 85);
            imagedestroy($image);
            
            $voucherPath = 'vouchers/' . $filename;
        }

        \DB::transaction(function () use ($loan, $cashbox, $amount, $voucherPath, $request) {
            $loan->update([
                'status' => 'approved',
                'disbursement_voucher' => $voucherPath,
                'disbursement_operation_number' => $request->disbursement_operation_number,
            ]);

            // Register movement
            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => "Egreso por aprobación de préstamo {$loan->code} a {$loan->user->name}",
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
                            $instCapital = round($remainingBalance, 2);
                            $instInterest = round($periodicPayment - $instCapital, 2);
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

    public function disburse(Request $request, Loan $loan)
    {
        if ($loan->status !== 'counter_accepted') {
            abort(403);
        }

        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
            'disbursement_voucher' => 'required|image|max:5120',
            'disbursement_operation_number' => 'required|string|max:30',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $amount = $loan->amount;

        if ($cashbox->balance < $amount) {
            return back()->withErrors(['cashbox_id' => 'Saldo insuficiente en la caja seleccionada.']);
        }

        $voucherPath = null;
        if ($request->hasFile('disbursement_voucher')) {
            $file = $request->file('disbursement_voucher');
            $filename = 'voucher_' . $loan->code . '_' . time() . '.webp';
            $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
            $fullPath = storage_path('app/public/vouchers/' . $filename);
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            imagewebp($image, $fullPath, 85);
            imagedestroy($image);
            $voucherPath = 'vouchers/' . $filename;
        }

        \DB::transaction(function () use ($loan, $cashbox, $amount, $voucherPath, $request) {
            $loan->update([
                'status' => 'approved',
                'cashbox_id' => $cashbox->id,
                'disbursement_voucher' => $voucherPath,
                'disbursement_operation_number' => $request->disbursement_operation_number,
            ]);

            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => "Egreso por desembolso de contra-oferta aceptada {$loan->code} a {$loan->user->name}",
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'date' => now(),
            ]);

            $cashbox->decrement('balance', $amount);

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
                            $instCapital = round($remainingBalance, 2);
                            $instInterest = round($periodicPayment - $instCapital, 2);
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

        return back()->with('success', 'Desembolso realizado, egreso registrado y cronograma generado correctamente.');
    }

    public function toggleDefault(Loan $loan)
    {
        $loan->update(['in_default' => !$loan->in_default]);
        $status = $loan->in_default ? 'activado' : 'desactivado';
        return back()->with('success', "Estado de mora {$status} para el préstamo {$loan->code}.");
    }

    public function installmentsIndex(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $instId = $request->get('installment_id');
        $highlightInterest = $request->get('highlight_interest', false);
        $movementDate = $request->get('movement_date');
        $paidDate = null;
        $search = $request->get('search');
        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();

        $totalOverdueCount = \App\Models\LoanInstallment::whereHas('loan', fn($q) => $q->whereIn('status', ['approved', 'paid']))
            ->whereNotIn('status', ['approved', 'paid'])
            ->where('due_date', '<', now()->startOfDay())
            ->count();

        $query = \App\Models\LoanInstallment::with(['loan.user', 'loan.user.documents', 'interestPayments'])
            ->whereHas('loan', function ($q) {
                $q->whereIn('status', ['approved', 'paid']);
            });

        if ($instId) {
            $query->where('id', $instId);
            if (!$highlightInterest && !in_array($filter, ['paid', 'overdue', 'pending_validation'])) {
                $filter = 'all';
            }
        } elseif ($filter === 'all') {
            $borrowersQuery = \App\Models\User::whereHas('loans', function ($q) {
                $q->whereIn('status', ['approved', 'paid'])
                  ->whereHas('installments', function ($iq) {
                      $iq->where('status', 'pending');
                  });
            })->with(['documents']);

            if ($search) {
                $borrowersQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
                });
            }

            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $borrowers = $borrowersQuery->orderBy('name', 'asc')->paginate($perPage)->withQueryString();

            $borrowersWithLoans = [];
            foreach ($borrowers as $user) {
                $loans = \App\Models\Loan::where('user_id', $user->id)
                    ->whereIn('status', ['approved', 'paid'])
                    ->get();
                $totalDebt = 0;
                $activeCount = 0;
                $overdueCount = 0;
                $pendingCount = 0;
                foreach ($loans as $l) {
                    $pending = $l->installments()->where('status', 'pending')->sum('amount');
                    $lateFees = $l->installments()->get()->sum('late_fee');
                    $totalDebt += $pending + $lateFees;
                    if ($l->status === 'approved') $activeCount++;
                    $overdueCount += $l->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
                    $pendingCount += $l->installments()->where('status', 'pending')->count();
                }
                $selfie = $user->documents?->selfie;
                $photo = $selfie ? \Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                $borrowersWithLoans[] = [
                    'user' => $user,
                    'photo' => $photo,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'loans_count' => $loans->count(),
                    'active_count' => $activeCount,
                    'overdue_count' => $overdueCount,
                    'pending_count' => $pendingCount,
                    'total_debt' => $totalDebt,
                ];
            }

            return view('superadmin.loans.installments', [
                'borrowers' => $borrowers,
                'borrowersWithLoans' => $borrowersWithLoans,
                'filter' => $filter,
                'cashboxes' => $cashboxes,
                'search' => $search,
                'totalOverdueCount' => $totalOverdueCount,
            ]);
        } elseif ($filter === 'grouped') {
            $loansPaginator = \App\Models\Loan::with(['user', 'user.documents'])
                ->whereIn('status', ['approved', 'paid'])
                ->latest()
                ->paginate(10)
                ->withQueryString();

            $groupedInstallments = [];
            foreach ($loansPaginator as $loan) {
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
                'loansPaginator' => $loansPaginator,
                'filter' => $filter,
                'cashboxes' => $cashboxes,
                'totalOverdueCount' => $totalOverdueCount,
            ]);
        } elseif ($filter === 'pending_validation') {
            $query->where('status', 'processing');
            // For each user/loan combination, only show the most recent installment
            $allProcessing = $query->orderBy('updated_at', 'desc')->get();
            $latestPerUserLoan = [];
            foreach ($allProcessing as $inst) {
                $key = $inst->loan->user_id . '-' . $inst->loan_id;
                if (!isset($latestPerUserLoan[$key])) {
                    $latestPerUserLoan[$key] = $inst;
                }
            }
            $deduped = collect($latestPerUserLoan)->values();
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $sliced = $deduped->slice(($page - 1) * $perPage, $perPage);
            $installments = new LengthAwarePaginator(
                $sliced,
                $deduped->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $installments->withQueryString();
            return view('superadmin.loans.installments', compact('installments', 'filter', 'cashboxes', 'instId', 'highlightInterest', 'movementDate', 'paidDate', 'totalOverdueCount'));
        } elseif ($filter === 'overdue') {
            $overdueInstallments = \App\Models\LoanInstallment::with(['loan.user', 'loan.user.documents'])
                ->whereHas('loan', fn($q) => $q->whereIn('status', ['approved', 'paid']))
                ->whereNotIn('status', ['approved', 'paid'])
                ->where('due_date', '<', now()->startOfDay())
                ->orderBy('due_date', 'asc')
                ->get();

            $debtors = [];
            foreach ($overdueInstallments as $inst) {
                $user = $inst->loan->user;
                $userId = $user->id;
                if (!isset($debtors[$userId])) {
                    $debtors[$userId] = [
                        'user' => $user,
                        'first_due_date' => $inst->due_date,
                        'installments_count' => 0,
                        'total_debt' => 0,
                    ];
                }
                if ($inst->due_date->lt($debtors[$userId]['first_due_date'])) {
                    $debtors[$userId]['first_due_date'] = $inst->due_date;
                }
                $debtors[$userId]['installments_count']++;
                $debtors[$userId]['total_debt'] += ($inst->amount + ($inst->late_fee ?? 0));
            }

            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $debtorList = collect(array_values($debtors));
            $sliced = $debtorList->slice(($page - 1) * $perPage, $perPage);
            $overdueDebtors = new LengthAwarePaginator(
                $sliced,
                $debtorList->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $overdueDebtors->withQueryString();

            return view('superadmin.loans.installments', [
                'overdueDebtors' => $overdueDebtors,
                'filter' => $filter,
                'cashboxes' => $cashboxes,
                'totalOverdueCount' => $totalOverdueCount,
            ]);
        } elseif ($filter === 'paid') {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                  ->orWhere('interest_payments_count', '>', 0);
            });
        }

        $installments = $query->with('interestPayments')->orderBy('due_date', 'asc')
            ->get();

        $paidDate = null;
        if ($filter === 'paid') {
            $pd = $request->get('paid_date');
            if ($pd) {
                $paidDate = $pd === 'today' ? today()->toDateString() : $pd;
            }
        }

        // Expand installments with interest payments into separate rows
        $expandedInstallments = collect();
        foreach ($installments as $inst) {
            if ($inst->interest_payments_count > 0 && ($inst->status === 'approved' || $filter === 'paid')) {
                // Load actual interest payments - for approved installments OR when viewing "Pagadas"
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

                if ($inst->status === 'approved' || $filter !== 'paid') {
                    $originalInst = clone $inst;
                    $originalInst->is_interest_payment = false;
                    $expandedInstallments->push($originalInst);
                }
            } else {
                // For non-approved installments OR installments without interest payments,
                // show as single row without expansion
                $inst->is_interest_payment = false;
                $expandedInstallments->push($inst);
            }
        }

        if ($paidDate) {
            $expandedInstallments = $expandedInstallments->filter(function ($row) use ($paidDate) {
                if (isset($row->is_interest_payment) && $row->is_interest_payment && isset($row->interest_payment) && $row->interest_payment) {
                    return $row->interest_payment->paid_at?->toDateString() === $paidDate;
                }
                return $row->paid_at?->toDateString() === $paidDate;
            })->values();
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

        return view('superadmin.loans.installments', compact('installments', 'filter', 'cashboxes', 'instId', 'highlightInterest', 'movementDate', 'paidDate', 'totalOverdueCount'));
    }

    public function userLoans(\App\Models\User $user)
    {
        $loans = \App\Models\Loan::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($loans as $loan) {
            $cur = $loan->currency === 'USD' ? '$' : 'S/';
            $paid = $loan->installments()->where('status', 'approved')->sum('amount');
            $pending = $loan->installments()->where('status', 'pending')->sum('amount');
            $overdue = $loan->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
            $lateFees = $loan->installments()->get()->sum('late_fee');
            $data[] = [
                'code' => $loan->code,
                'url' => route('superadmin.installments.group', $loan) . '?source=modal',
                'status' => $loan->status === 'approved' ? 'Activo' : 'Pagado',
                'overdue' => $overdue,
                'cur' => $cur,
                'amount' => (float) $loan->amount,
                'paid' => (float) $paid,
                'debt' => (float) ($pending + $lateFees),
            ];
        }

        return response()->json([
            'user' => $user->name,
            'loans' => $data,
        ]);
    }

    public function debtorDetail(\App\Models\User $user)
    {
        $overdueInstallments = \App\Models\LoanInstallment::with(['loan'])
            ->whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereIn('status', ['approved', 'paid']);
            })
            ->where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->get();

        $totalDebt = 0;
        $totalLateFee = 0;
        foreach ($overdueInstallments as $inst) {
            $totalDebt += $inst->amount;
            $totalLateFee += ($inst->late_fee ?? 0);
        }

        $selfie = $user->documents?->selfie;
        $photo = $selfie ? \Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
        $fullName = $user->person_type === 'juridica' ? ($user->business_name ?? $user->name) : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '') ?: $user->name);

        return view('superadmin.loans.debtor-detail', [
            'user' => $user,
            'photo' => $photo,
            'fullName' => $fullName,
            'pendingInstallments' => $overdueInstallments,
            'totalDebt' => $totalDebt,
            'totalLateFee' => $totalLateFee,
        ]);
    }

    public function installmentsGroup(Request $request, \App\Models\Loan $loan)
    {
        $page = (int) $request->get('page', 1);

        $installments = $loan->installments()
            ->orderBy('installment_number', 'asc')
            ->paginate(5, ['*'], 'page', $page);

        if ($request->ajax()) {
            return view('superadmin.loans.partials.installments-group-rows', [
                'loan' => $loan,
                'userInstallments' => $installments,
            ]);
        }

        if ($request->get('source') === 'modal') {
            return view('superadmin.loans.loan-detail-premium', [
                'loan' => $loan,
                'userInstallments' => $installments,
            ]);
        }

        return view('superadmin.loans.loan-detail', [
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
        $loan = $installment->loan;
        $isInterestOnly = $installment->requested_type === 'interest';
        $isMoraInterest = $installment->requested_type === 'mora_interest';
        $isMoraFull = $installment->requested_type === 'mora_full';
        $isTotal = $installment->requested_type === 'total';
        $isPartial = $installment->requested_type === 'partial';

        if ($isMoraInterest) {
            $overdueInstallments = $loan->overdue_installments;
            $totalLateFees = $overdueInstallments->sum('late_fee');
            $amount = $totalLateFees + $installment->interest;
        } elseif ($isMoraFull) {
            $overdueInstallments = $loan->overdue_installments;
            $totalLateFees = $overdueInstallments->sum('late_fee');
            $amount = $totalLateFees + $installment->amount;
        } elseif ($isInterestOnly) {
            $amount = $installment->interest + ($installment->late_fee ?? 0);
        } elseif ($isTotal) {
            $paidCapital = $loan->installments()->where('status', 'approved')->sum('capital');
            $pendingCapital = max(0, $loan->amount - $paidCapital);
            $amount = $pendingCapital + $installment->interest;
        } elseif ($isPartial) {
            $amount = $installment->amount + ($installment->late_fee ?? 0) + ($installment->amortization_extra ?? 0);
        } else {
            $amount = $installment->amount + ($installment->late_fee ?? 0);
        }

        \DB::transaction(function () use ($installment, $cashbox, $amount, $isInterestOnly, $isMoraInterest, $isMoraFull, $isTotal, $isPartial) {
            $loan = $installment->loan;

            if ($isInterestOnly) {
                $currentNumber = $installment->installment_number;
                $freq = $loan->payment_frequency;

                $savedVoucher = $installment->payment_voucher;
                $savedMethod  = $installment->payment_method;
                $savedBank    = $installment->bank_name;
                $savedOp      = $installment->operation_number;
                $savedLateFee = $installment->late_fee;
                $savedInterest = $installment->interest;

                $movement = $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por SOLO INTERÉS de cuota #{$currentNumber} de préstamo {$loan->code} de {$loan->user->name}",
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

                // Persist voucher & payment details in interest_payments table
                $interestPayment = \App\Models\InterestPayment::create([
                    'loan_installment_id' => $installment->id,
                    'payment_voucher'     => $savedVoucher,
                    'payment_method'      => $savedMethod,
                    'bank_name'           => $savedBank,
                    'operation_number'    => $savedOp,
                    'amount'              => $savedInterest,
                    'late_fee'            => $savedLateFee,
                    'paid_at'             => now(),
                ]);

                $movement->update(['interest_payment_id' => $interestPayment->id]);

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

            } elseif ($isMoraInterest) {
                $overdueInsts = $loan->overdue_installments;
                $totalLateFees = $overdueInsts->sum('late_fee');

                $movement = $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por MORA + INTERÉS de cuota #{$installment->installment_number} de préstamo {$loan->code} de {$loan->user->name}",
                    'reference_type' => 'loan_installment',
                    'reference_id' => $installment->id,
                    'date' => now(),
                ]);

                $cashbox->increment('balance', $amount);

                $interestPayment = \App\Models\InterestPayment::create([
                    'loan_installment_id' => $installment->id,
                    'payment_voucher'     => $installment->payment_voucher,
                    'payment_method'      => $installment->payment_method,
                    'bank_name'           => $installment->bank_name,
                    'operation_number'    => $installment->operation_number,
                    'amount'              => $installment->interest,
                    'late_fee'            => $totalLateFees,
                    'paid_at'             => now(),
                ]);

                $movement->update(['interest_payment_id' => $interestPayment->id]);

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

                $loan->update(['in_default' => false]);

            } elseif ($isMoraFull) {
                $overdueInsts = $loan->overdue_installments;
                $totalLateFees = $overdueInsts->sum('late_fee');

                $installment->update([
                    'status' => 'approved',
                    'paid_at' => now(),
                    'charged_late_fee' => $totalLateFees,
                ]);

                $existingMovement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->whereNull('interest_payment_id')
                    ->first();

                if (!$existingMovement) {
                    $cashbox->movements()->create([
                        'user_id' => auth()->id(),
                        'type' => 'income',
                        'amount' => $amount,
                        'description' => "Ingreso por MORA + CUOTA COMPLETA #{$installment->installment_number} de préstamo {$loan->code} de {$loan->user->name}",
                        'reference_type' => 'loan_installment',
                        'reference_id' => $installment->id,
                        'date' => now(),
                    ]);

                    $cashbox->increment('balance', $amount);
                }

                $loan->update(['in_default' => false]);

                $remaining = $loan->installments()->where('status', '!=', 'approved')->count();
                if ($remaining === 0) {
                    $loan->update(['status' => 'paid']);
                }

            } elseif ($isTotal) {
                $pendingInsts = $loan->installments()
                    ->whereIn('status', ['pending', 'rejected', 'processing'])
                    ->get();

                $now = now();
                foreach ($pendingInsts as $inst) {
                    $inst->update([
                        'status' => 'approved',
                        'paid_at' => $now,
                        'charged_late_fee' => 0,
                    ]);
                }

                $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por PAGO TOTAL de préstamo {$loan->code} de {$loan->user->name}",
                    'reference_type' => 'loan_installment',
                    'reference_id' => $installment->id,
                    'date' => now(),
                ]);

                $cashbox->increment('balance', $amount);
                $loan->update(['status' => 'paid']);

            } elseif ($isPartial) {
                $extraCapital = $installment->amortization_extra ?? 0;

                $installment->update([
                    'status' => 'approved',
                    'paid_at' => now(),
                    'charged_late_fee' => $installment->late_fee,
                    'capital' => $installment->capital + $extraCapital,
                    'amount' => $installment->amount + $extraCapital,
                ]);

                $cashbox->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => 'income',
                    'amount' => $amount,
                    'description' => "Ingreso por AMORTIZACIÓN PARCIAL de cuota #{$installment->installment_number} de préstamo {$loan->code} de {$loan->user->name}",
                    'reference_type' => 'loan_installment',
                    'reference_id' => $installment->id,
                    'date' => now(),
                ]);

                $cashbox->increment('balance', $amount);

                $paidCapital = $loan->installments()->where('status', 'approved')->sum('capital');
                $newRemainingBalance = max(0, $loan->amount - $paidCapital);

                $pendingInsts = $loan->installments()
                    ->whereIn('status', ['pending', 'rejected', 'processing'])
                    ->orderBy('installment_number', 'asc')
                    ->get();

                if ($pendingInsts->count() > 0 && $newRemainingBalance > 0) {
                    $rate = $loan->interest_rate / 100;
                    $freq = $loan->payment_frequency;
                    $installmentsPerMonth = match ($freq) {
                        'diario' => 30,
                        'semanal' => 4,
                        'quincenal' => 2,
                        'mensual' => 1,
                        default => 1,
                    };
                    $periodicRate = $rate / $installmentsPerMonth;

                    if ($installment->amortization_type === 'reduce_amount') {
                        $nRemaining = $pendingInsts->count();
                        if ($periodicRate > 0 && $nRemaining > 0) {
                            $newPayment = ($newRemainingBalance * $periodicRate) / (1 - pow(1 + $periodicRate, -$nRemaining));
                        } else {
                            $newPayment = $newRemainingBalance / max(1, $nRemaining);
                        }

                        $remainingBal = $newRemainingBalance;
                        foreach ($pendingInsts as $idx => $inst) {
                            $i = $idx + 1;
                            $instInterest = $remainingBal * $periodicRate;
                            $instCapital = $newPayment - $instInterest;

                            if ($i === $nRemaining) {
                                $instCapital = round($remainingBal, 2);
                                $instInterest = round($newPayment - $instCapital, 2);
                            }

                            $remainingBal -= $instCapital;

                            $inst->update([
                                'amount' => round($instInterest + $instCapital, 2),
                                'capital' => round($instCapital, 2),
                                'interest' => round($instInterest, 2),
                            ]);
                        }
                    } elseif ($installment->amortization_type === 'reduce_term') {
                        $currentPayment = $loan->monthly_payment;

                        if ($periodicRate > 0 && $currentPayment > 0) {
                            $nNeeded = ceil(log($currentPayment / ($currentPayment - $newRemainingBalance * $periodicRate)) / log(1 + $periodicRate));
                        } else {
                            $nNeeded = ceil($newRemainingBalance / max(0.01, $currentPayment));
                        }

                        $nNeeded = max(1, (int) $nNeeded);
                        $nCurrent = $pendingInsts->count();

                        $remainingBal = $newRemainingBalance;
                        $keepInsts = $pendingInsts->take($nNeeded);
                        $removeInsts = $pendingInsts->slice($nNeeded);

                        foreach ($keepInsts as $idx => $inst) {
                            $i = $idx + 1;
                            $instInterest = $remainingBal * $periodicRate;
                            $instCapital = $currentPayment - $instInterest;

                            if ($i === $nNeeded) {
                                $instCapital = round($remainingBal, 2);
                                $instInterest = round($currentPayment - $instCapital, 2);
                            }

                            $remainingBal -= $instCapital;

                            $inst->update([
                                'amount' => round($instInterest + $instCapital, 2),
                                'capital' => round($instCapital, 2),
                                'interest' => round($instInterest, 2),
                            ]);
                        }

                        foreach ($removeInsts as $inst) {
                            $inst->delete();
                        }
                    }
                }

                $remaining = $loan->installments()->where('status', '!=', 'approved')->count();
                if ($remaining === 0) {
                    $loan->update(['status' => 'paid']);
                }

            } else {
                $installment->update([
                    'status' => 'approved',
                    'paid_at' => now(),
                    'charged_late_fee' => $installment->late_fee,
                ]);

                $existingMovement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->whereNull('interest_payment_id')
                    ->first();

                if (!$existingMovement) {
                    $cashbox->movements()->create([
                        'user_id' => auth()->id(),
                        'type' => 'income',
                        'amount' => $amount,
                        'description' => "Ingreso por pago de cuota #{$installment->installment_number} de préstamo {$loan->code} de {$loan->user->name}",
                        'reference_type' => 'loan_installment',
                        'reference_id' => $installment->id,
                        'date' => now(),
                    ]);

                    $cashbox->increment('balance', $amount);
                }

                // Check if all installments are paid
                $remaining = $loan->installments()->where('status', '!=', 'approved')->count();
                if ($remaining === 0) {
                    $loan->update(['status' => 'paid']);
                }
            }
        });

        $successMsg = 'Pago aprobado e ingreso registrado correctamente.';
        if ($isMoraInterest) {
            $successMsg = 'Pago de mora + interés aprobado. Estado de mora desactivado.';
        } elseif ($isMoraFull) {
            $successMsg = 'Pago de mora + cuota completa aprobado. Estado de mora desactivado.';
        } elseif ($isInterestOnly) {
            $successMsg = 'Pago de interés aprobado y fechas postergadas.';
        } elseif ($isTotal) {
            $successMsg = 'Pago total aprobado. El préstamo ha sido marcado como pagado.';
        } elseif ($isPartial) {
            $successMsg = 'Amortización parcial aprobada. Las cuotas restantes han sido recalculadas.';
        }

        return back()->with('success', $successMsg);
    }

    public function rejectInstallment(Request $request, \App\Models\LoanInstallment $installment)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        \DB::transaction(function () use ($request, $installment) {
            // Delete voucher from storage
            if ($installment->payment_voucher) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($installment->payment_voucher);
            }

            // Find and delete the associated cashbox movement if exists
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

            $installment->update([
                'status' => 'rejected',
                'payment_voucher' => null,
                'admin_notes' => $request->reason,
                'paid_at' => null
            ]);
        });

        return back()->with('success', 'Pago rechazado. El ingreso en caja ha sido eliminado y el cliente deberá subirlo nuevamente.');
    }

    public function unapproveInstallment(\App\Models\LoanInstallment $installment)
    {
        $loan = $installment->loan;
        $isInterestPayment = false;
        $interestPaymentId = request('interest_payment_id');
        $interestPayment = null;

        \Log::info('Unapprove installment', [
            'installment_id' => $installment->id,
            'installment_status' => $installment->status,
            'interest_payments_count' => $installment->interest_payments_count,
            'interest_payment_id_param' => $interestPaymentId,
        ]);

        // LÓGICA SIMPLIFICADA: Determinar qué tipo de pago es y si puede revertirse

        $isTotalPayment = $installment->requested_type === 'total';
        $isPartialPayment = $installment->requested_type === 'partial';

        if ($installment->status === 'approved') {
            // CASO A: Revertir cuota aprobada
            // Verificar que sea el ÚLTIMO pago del préstamo (comparando fechas)

            // 1. Obtener último interés pagado
            $lastInterestPayment = null;
            $lastInterestDate = null;

            $installmentsWithInterests = $loan->installments()
                ->where('interest_payments_count', '>', 0)
                ->with('interestPayments')
                ->get();

            foreach ($installmentsWithInterests as $instWithInterest) {
                foreach ($instWithInterest->interestPayments as $ip) {
                    if ($ip->paid_at && (!$lastInterestDate || $ip->paid_at > $lastInterestDate)) {
                        $lastInterestPayment = $ip;
                        $lastInterestDate = $ip->paid_at;
                    }
                }
            }

            // 2. Comparar fechas para determinar el último pago real
            $canRevertThis = true;

            if ($lastInterestPayment && $lastInterestDate) {
                // Si hay intereses, verificar que esta cuota sea posterior al último interés
                if ($lastInterestDate > $installment->paid_at) {
                    $canRevertThis = false;
                }
            }

            if ($isTotalPayment) {
                // Para pago total: verificar que no haya cuotas aprobadas DESPUÉS del batch
                $hasLaterApproved = $loan->installments()
                    ->where('status', 'approved')
                    ->where('paid_at', '>', $installment->paid_at)
                    ->exists();

                if ($hasLaterApproved) {
                    $canRevertThis = false;
                }
            } else {
                // Para cuotas normales: verificar que sea efectivamente la última cuota aprobada
                $lastApprovedInstallment = $loan->installments()
                    ->where('status', 'approved')
                    ->orderBy('paid_at', 'desc')
                    ->first();

                if ($lastApprovedInstallment && $lastApprovedInstallment->id !== $installment->id) {
                    $canRevertThis = false;
                }
            }

            if (!$canRevertThis) {
                return back()->withErrors(['unapprove' => 'Solo puedes revertir el último pago del préstamo.']);
            }

        } elseif ($installment->status === 'pending' && $installment->interest_payments_count > 0 && $interestPaymentId) {
            // CASO B: Revertir pago de interés
            $interestPayment = \App\Models\InterestPayment::find($interestPaymentId);

            if (!$interestPayment || $interestPayment->loan_installment_id !== $installment->id) {
                return back()->withErrors(['unapprove' => 'Pago de interés no encontrado.']);
            }

            // Verificar que sea el ÚLTIMO interés del préstamo completo
            $lastInterestPaymentGlobal = null;
            $lastInterestDateGlobal = null;

            $allInstallmentsWithInterests = $loan->installments()
                ->where('interest_payments_count', '>', 0)
                ->with('interestPayments')
                ->get();

            foreach ($allInstallmentsWithInterests as $instWithInterest) {
                foreach ($instWithInterest->interestPayments as $ip) {
                    if ($ip->paid_at && (!$lastInterestDateGlobal || $ip->paid_at > $lastInterestDateGlobal)) {
                        $lastInterestPaymentGlobal = $ip;
                        $lastInterestDateGlobal = $ip->paid_at;
                    }
                }
            }

            // Verificar que este interés sea el último
            if (!$lastInterestPaymentGlobal || $lastInterestPaymentGlobal->id !== $interestPayment->id) {
                return back()->withErrors(['unapprove' => 'Solo puedes revertir el último pago de interés del préstamo.']);
            }

            // Verificar que no haya cuotas aprobadas después de este interés
            $lastApprovedAfterInterest = $loan->installments()
                ->where('status', 'approved')
                ->where('paid_at', '>', $interestPayment->paid_at)
                ->exists();

            if ($lastApprovedAfterInterest) {
                return back()->withErrors(['unapprove' => 'No puedes revertir este pago porque hay una cuota aprobada posteriormente.']);
            }

            $isInterestPayment = true;

        } else {
            return back()->withErrors(['unapprove' => 'No se puede revertir este pago.']);
        }

        \DB::transaction(function () use ($installment, $loan, $isInterestPayment, $interestPayment, $isTotalPayment, $isPartialPayment) {
            if ($isInterestPayment && $interestPayment) {
                // REVERSIÓN DE PAGO DE INTERÉS → volver a "Por validar"

                // 1. Eliminar movimiento de caja asociado a este interés
                $movement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->where('interest_payment_id', $interestPayment->id)
                    ->first();

                if ($movement) {
                    $cashbox = $movement->cashbox;
                    if ($cashbox) {
                        $cashbox->decrement('balance', $movement->amount);
                    }
                    $movement->delete();
                }

                // 2. Eliminar la última cuota (la que se creó al aprobar el interés)
                $lastInst = $loan->installments()->orderBy('installment_number', 'desc')->first();
                if ($lastInst && $lastInst->status === 'pending') {
                    $lastInst->delete();
                }

                // 3. Renumber: decrementar installment_number para la cuota actual y posteriores
                $currentNumber = $installment->installment_number;
                $loan->installments()
                    ->where('installment_number', '>', $currentNumber)
                    ->decrement('installment_number');

                // 4. Shift fechas hacia atrás (-1 período) para todas las cuotas pending/rejected/processing
                $freq = $loan->payment_frequency;
                $installmentsToShift = $loan->installments()
                    ->whereIn('status', ['pending', 'rejected', 'processing'])
                    ->get();

                foreach ($installmentsToShift as $inst) {
                    $date = $inst->due_date->copy();
                    if ($freq === 'diario')
                        $date->subDay();
                    elseif ($freq === 'semanal')
                        $date->subWeek();
                    elseif ($freq === 'quincenal')
                        $date->subDays(15);
                    else
                        $date->subMonth();

                    $inst->update(['due_date' => $date]);
                }

                // 5. Restaurar cuota a processing con datos del interés revertido
                $newCount = max(0, $installment->interest_payments_count - 1);
                $installment->update([
                    'status' => 'processing',
                    'payment_voucher' => $interestPayment->payment_voucher,
                    'payment_method' => $interestPayment->payment_method,
                    'bank_name' => $interestPayment->bank_name,
                    'operation_number' => $interestPayment->operation_number,
                    'requested_type' => 'interest',
                    'paid_at' => null,
                    'interest_payments_count' => $newCount,
                ]);

                // 6. Eliminar registro del interés (sin borrar voucher, se movió a la cuota)
                $interestPayment->delete();

            } elseif ($isTotalPayment) {
                // REVERSIÓN DE PAGO TOTAL → revertir todas las cuotas del batch
                $batchPaidAt = $installment->paid_at;
                $batchInstallments = $loan->installments()
                    ->where('status', 'approved')
                    ->where('paid_at', $batchPaidAt)
                    ->get();

                foreach ($batchInstallments as $inst) {
                    if ($inst->id === $installment->id) {
                        // La cuota que inició el pago total vuelve a processing con sus datos
                        $inst->update([
                            'status' => 'processing',
                            'paid_at' => null,
                        ]);
                    } else {
                        // Las demás cuotas del batch vuelven a pending
                        $inst->update([
                            'status' => 'pending',
                            'paid_at' => null,
                        ]);
                    }
                }

                if ($loan->status === 'paid') {
                    $loan->update(['status' => 'approved']);
                }

                $movement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->whereNull('interest_payment_id')
                    ->first();

                if ($movement) {
                    $cashbox = $movement->cashbox;
                    if ($cashbox) {
                        $cashbox->decrement('balance', $movement->amount);
                    }
                    $movement->delete();
                }
            } elseif ($isPartialPayment) {
                $originalCapital = $installment->capital - ($installment->amortization_extra ?? 0);
                $originalAmount = $installment->amount - ($installment->amortization_extra ?? 0);

                $installment->update([
                    'status' => 'processing',
                    'requested_type' => 'partial',
                    'paid_at' => null,
                    'capital' => $originalCapital,
                    'amount' => $originalAmount,
                ]);

                if ($loan->status === 'paid') {
                    $loan->update(['status' => 'approved']);
                }

                $movement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->whereNull('interest_payment_id')
                    ->first();

                if ($movement) {
                    $cashbox = $movement->cashbox;
                    if ($cashbox) {
                        $cashbox->decrement('balance', $movement->amount);
                    }
                    $movement->delete();
                }

                $rate = $loan->interest_rate / 100;
                $freq = $loan->payment_frequency;
                $installmentsPerMonth = match ($freq) {
                    'diario' => 30,
                    'semanal' => 4,
                    'quincenal' => 2,
                    'mensual' => 1,
                    default => 1,
                };
                $periodicRate = $rate / $installmentsPerMonth;
                $nTotal = $loan->installments()->count();
                $periodicPayment = $loan->monthly_payment;

                $paidCapital = $loan->installments()->where('status', 'approved')->sum('capital');
                $remainingBalance = max(0, $loan->amount - $paidCapital);

                $pendingInsts = $loan->installments()
                    ->whereIn('status', ['pending', 'rejected', 'processing'])
                    ->orderBy('installment_number', 'asc')
                    ->get();

                if ($pendingInsts->count() > 0 && $remainingBalance > 0) {
                    $nRemaining = $pendingInsts->count();
                    if ($periodicRate > 0 && $nRemaining > 0) {
                        $newPayment = ($remainingBalance * $periodicRate) / (1 - pow(1 + $periodicRate, -$nRemaining));
                    } else {
                        $newPayment = $remainingBalance / max(1, $nRemaining);
                    }

                    $remBal = $remainingBalance;
                    foreach ($pendingInsts as $idx => $inst) {
                        $i = $idx + 1;
                        $instInterest = $remBal * $periodicRate;
                        $instCapital = $newPayment - $instInterest;

                        if ($i === $nRemaining) {
                            $instCapital = round($remBal, 2);
                            $instInterest = round($newPayment - $instCapital, 2);
                        }

                        $remBal -= $instCapital;

                        $inst->update([
                            'amount' => round($instInterest + $instCapital, 2),
                            'capital' => round($instCapital, 2),
                            'interest' => round($instInterest, 2),
                        ]);
                    }
                }

            } else {
                // REVERSIÓN DE CUOTA APROBADA → volver a "Por validar"
                $installment->update([
                    'status' => 'processing',
                    'requested_type' => 'full',
                    'paid_at' => null,
                ]);

                if ($loan->status === 'paid') {
                    $loan->update(['status' => 'approved']);
                }

                $movement = \App\Models\CashboxMovement::where('reference_type', 'loan_installment')
                    ->where('reference_id', $installment->id)
                    ->whereNull('interest_payment_id')
                    ->first();

                if ($movement) {
                    $cashbox = $movement->cashbox;
                    if ($cashbox) {
                        $cashbox->decrement('balance', $movement->amount);
                    }
                    $movement->delete();
                }
            }
        });

        if ($isInterestPayment) {
            return back()->with('success', 'Pago de interés revertido. El pago vuelve a estar en revisión.');
        } else {
            return back()->with('success', 'Aprobación revertida. El ingreso en caja ha sido eliminado y el pago vuelve a estar en revisión.');
        }
    }

    public function previewVoucherCleanup(Request $request, VoucherCleanupService $cleanup)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        return response()->json(
            $cleanup->preview(
                Carbon::parse($data['from_date']),
                Carbon::parse($data['to_date'])
            )
        );
    }

    public function cleanupVouchers(Request $request, VoucherCleanupService $cleanup)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $from = Carbon::parse($data['from_date']);
        $to = Carbon::parse($data['to_date']);

        $result = $cleanup->cleanup($from, $to);

        return redirect()
            ->route('superadmin.installments.index', ['filter' => 'paid'])
            ->with('success', "Se eliminaron {$result['deleted_count']} comprobante(s) y se liberaron {$result['freed_human']}.");
    }

    public function referralPayouts(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = \App\Models\Referral::with(['referrer', 'referred'])
            ->whereHas('referred.loans', function ($q) {
                $q->where('status', 'paid');
            });

        if ($filter === 'pending') {
            $query->whereDoesntHave('payouts', function ($q) {
                $q->where('status', 'paid');
            });
        } elseif ($filter === 'paid') {
            $query->whereHas('payouts', function ($q) {
                $q->where('status', 'paid');
            });
        } elseif ($filter === 'withdrawals') {
            $query->whereHas('payouts', function ($q) {
                $q->where('status', 'pending');
            });
        }

        $referrals = $query->latest()->paginate(15)->withQueryString();

        foreach ($referrals as $referral) {
            $paidLoan = $referral->referred->loans()->where('status', 'paid')->latest()->first();
            $referral->paid_loan = $paidLoan;
            if ($paidLoan) {
                $paidLoan->installments_count = $paidLoan->installments()->count();
                $paidLoan->total_interest = round($paidLoan->total_amount - $paidLoan->amount, 2);
            }
            $referral->payout = $referral->payouts()->where('loan_id', $paidLoan?->id)->first();
            $referral->referrer_has_loans = $referral->referrer->loans()->whereIn('status', ['approved', 'disbursed', 'active', 'paid'])->exists();
            $referral->referrer_has_investments = $referral->referrer->investorApplication !== null
                && \DB::table('investor_opportunity')->where('user_id', $referral->referrer_id)->where('status', 'approved')->exists();
        }

        $counts = [
            'pending' => \App\Models\Referral::whereHas('referred.loans', function ($q) {
                $q->where('status', 'paid');
            })->whereDoesntHave('payouts', function ($q) {
                $q->where('status', 'paid');
            })->count(),
            'paid' => \App\Models\Referral::whereHas('payouts', function ($q) {
                $q->where('status', 'paid');
            })->count(),
            'withdrawals' => \App\Models\Referral::whereHas('referred.loans', function ($q) {
                $q->where('status', 'paid');
            })->whereHas('payouts', function ($q) {
                $q->where('status', 'pending');
            })->count() + \App\Models\ReferralWithdrawal::where('status', 'pending')->count(),
        ];

        $withdrawals = $filter === 'withdrawals'
            ? \App\Models\ReferralWithdrawal::with(['user', 'bankAccount'])->where('status', 'pending')->latest()->paginate(15)->withQueryString()
            : null;

        $paidWithdrawals = $filter === 'paid'
            ? \App\Models\ReferralWithdrawal::with(['user', 'bankAccount'])->whereIn('status', ['paid', 'confirmed'])->latest()->paginate(15)->withQueryString()
            : null;

        return view('superadmin.referrals.payouts', compact('referrals', 'filter', 'counts', 'withdrawals', 'paidWithdrawals'));
    }

    public function storeReferralPayout(Request $request, \App\Models\Referral $referral)
    {
        $paidLoan = $referral->referred->loans()->where('status', 'paid')->latest()->first();

        if (!$paidLoan) {
            return back()->withErrors(['referral' => 'El referido no tiene préstamos pagados.']);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $paidLoan->total_amount,
            'notes' => 'nullable|string|max:500',
        ]);

        $referral->payouts()->updateOrCreate(
            ['loan_id' => $paidLoan->id],
            [
                'amount' => $data['amount'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Monto de referido registrado correctamente.');
    }

    public function confirmReferralPayout(Request $request, \App\Models\ReferralPayout $payout)
    {
        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pago a referidor confirmado correctamente.');
    }

    public function confirmWithdrawal(Request $request, \App\Models\ReferralWithdrawal $withdrawal)
    {
        $data = $request->validate([
            'voucher' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $update = [
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => $data['notes'] ?? null,
        ];

        if ($request->hasFile('voucher')) {
            $file = $request->file('voucher');
            $ext = strtolower($file->getClientOriginalExtension());
            $srcPath = $file->getRealPath();

            $image = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($srcPath),
                'png' => @imagecreatefrompng($srcPath),
                'webp' => @imagecreatefromwebp($srcPath),
                default => null,
            };

            if ($image) {
                $filename = uniqid('withdrawal_', true) . '.webp';
                $relativePath = 'withdrawals/' . $filename;
                $fullPath = storage_path('app/public/' . $relativePath);

                if (!is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                imagewebp($image, $fullPath, 85);
                imagedestroy($image);

                $update['voucher_path'] = $relativePath;
            }
        }

        $withdrawal->update($update);

        return back()->with('success', 'Retiro de referidos confirmado correctamente.');
    }

    public function userDetail(Request $request)
    {
        $user = \App\Models\User::with(['documents', 'loans.installments'])->findOrFail($request->get('user_id'));

        $loans = $user->loans;
        $activeLoans = $loans->whereIn('status', ['approved', 'disbursed', 'active']);
        $paidLoans = $loans->where('status', 'paid');
        $pendingLoans = $loans->where('status', 'pending');
        $rejectedLoans = $loans->where('status', 'rejected');
        $totalLoans = $loans->count();

        $overdueCount = 0;
        $upcomingCount = 0;
        $totalDebt = 0;

        foreach ($activeLoans as $loan) {
            foreach ($loan->installments as $inst) {
                if ($inst->status !== 'approved') {
                    $amount = (float) $inst->amount + (float) ($inst->late_fee ?? 0);
                    if ($inst->due_date < now()->startOfDay()) {
                        $overdueCount++;
                        $totalDebt += $amount;
                    } else {
                        $upcomingCount++;
                    }
                }
            }
        }

        $hasDebt = $overdueCount > 0;

        $selfie = $user->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($user->documents->selfie) : null;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'initials' => strtoupper(substr($user->name, 0, 1)),
            'selfie' => $selfie,
            'total_loans' => $totalLoans,
            'active_loans' => $activeLoans->count(),
            'paid_loans' => $paidLoans->count(),
            'pending_loans' => $pendingLoans->count(),
            'rejected_loans' => $rejectedLoans->count(),
            'has_debt' => $hasDebt,
            'total_debt' => $totalDebt,
            'overdue_count' => $overdueCount,
            'upcoming_count' => $upcomingCount,
        ]);
    }
}
