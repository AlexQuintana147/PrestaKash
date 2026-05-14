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
            'amortization_system' => 'required|in:frances,americano',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'months' => 'nullable|integer|min:3|max:24',
            'payment_frequency' => 'nullable|in:diario,semanal,quincenal,mensual',
            'disbursement_date' => 'required|date|after_or_equal:today',
        ]);

        $amount = (float) $request->amount;
        $rate = $request->interest_rate / 100;
        $system = $request->amortization_system;
        $months = $loan->type === 'cuotas' ? ($request->months ?? $loan->months) : null;
        $freq = ($loan->type === 'cuotas' && $system === 'frances') ? ($request->payment_frequency ?? 'mensual') : 'mensual';

        $totalAmount = $amount;
        $periodicPayment = 0;

        if ($loan->type === 'cuotas') {
            if ($system === 'frances') {
                $installmentsPerMonth = 1;
                if ($freq === 'diario')
                    $installmentsPerMonth = 30;
                elseif ($freq === 'semanal')
                    $installmentsPerMonth = 4;
                elseif ($freq === 'quincenal')
                    $installmentsPerMonth = 2;

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

        $loan->update([
            'amount' => $amount,
            'months' => $months,
            'amortization_system' => $system,
            'payment_frequency' => $freq,
            'interest_rate' => $request->interest_rate,
            'disbursement_date' => $request->disbursement_date,
            'total_amount' => $totalAmount,
            'monthly_payment' => $periodicPayment,
        ]);

        return back()->with('success', 'Contra-oferta enviada correctamente.');
    }

    public function approve(Loan $loan)
    {
        $loan->update(['status' => 'approved']);

        // Delete existing installments if any (e.g. from previous attempts)
        $loan->installments()->delete();

        // Generate installments
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
            $periodicPayment = $loan->monthly_payment;

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

                // For American system, last installment includes principal
                $instAmount = $periodicPayment;
                if ($system === 'americano' && $i === $nTotal) {
                    $instAmount += $amount;
                }

                $loan->installments()->create([
                    'installment_number' => $i,
                    'amount' => $instAmount,
                    'due_date' => $currentDate->format('Y-m-d'),
                    'status' => 'pending',
                ]);
            }
        } else {
            // Pago único
            $loan->installments()->create([
                'installment_number' => 1,
                'amount' => $loan->total_amount,
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
}
