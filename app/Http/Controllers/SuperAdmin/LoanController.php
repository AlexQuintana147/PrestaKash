<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = Loan::with(['user.documents', 'user.clientProfile'])->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $loans = $query->paginate(15)->withQueryString();

        $counts = [
            'pending' => Loan::where('status', 'pending')->count(),
            'counter_offered' => Loan::where('status', 'counter_offered')->count(),
            'approved' => Loan::where('status', 'approved')->count(),
            'rejected' => Loan::where('status', 'rejected')->count(),
            'paid' => Loan::where('status', 'paid')->count(),
            'all' => Loan::count(),
        ];

        return view('superadmin.loans.index', compact('loans', 'filter', 'counts'));
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
        ]);

        $amount = (float) $request->amount;
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

        $loan->update($updateData);

        return back()->with('success', 'Contra-oferta enviada correctamente.');
    }

    public function approve(Loan $loan)
    {
        $loan->update(['status' => 'approved']);

        // Delete existing installments if any
        $loan->installments()->delete();

        $amount = $loan->amount;
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

                    // Adjust for rounding on last installment
                    if ($i === $nTotal) {
                        $instCapital = $remainingBalance;
                    }

                    $remainingBalance -= $instCapital;
                } else {
                    // American
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
            // Pago único
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

        return back()->with('success', 'Préstamo aprobado e historial generado correctamente.');
    }

    public function reject(Loan $loan)
    {
        $loan->update(['status' => 'rejected']);
        return back()->with('success', 'Préstamo rechazado.');
    }

    public function installmentsIndex(Request $request)
    {
        $filter = $request->get('filter', 'all');

        if ($filter === 'grouped') {
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
                'filter' => $filter
            ]);
        }

        $query = \App\Models\LoanInstallment::with(['loan.user', 'loan.user.documents'])
            ->whereHas('loan', function ($q) {
                $q->whereIn('status', ['approved', 'paid']);
            });

        if ($filter === 'pending_validation') {
            $query->where('status', 'processing');
        }

        if ($filter === 'paid') {
            $query->where('status', 'approved');
        }

        $installments = $query->orderBy('due_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.loans.installments', compact('installments', 'filter'));
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

    public function approveInstallment(\App\Models\LoanInstallment $installment)
    {
        $installment->update([
            'status' => 'approved',
            'paid_at' => now()
        ]);

        // Check if all installments are paid
        $loan = $installment->loan;
        $remaining = $loan->installments()->where('status', '!=', 'approved')->count();
        if ($remaining === 0) {
            $loan->update(['status' => 'paid']);
        }

        return back()->with('success', 'Pago aprobado correctamente.');
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

        $installment->update(['status' => 'processing']);

        $loan = $installment->loan;
        if ($loan->status === 'paid') {
            $loan->update(['status' => 'approved']);
        }

        return back()->with('success', 'Aprobación revertida. El pago vuelve a estar en revisión.');
    }
}
