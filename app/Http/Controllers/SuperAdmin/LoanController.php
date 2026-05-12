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
            'payment_frequency' => 'nullable|in:diario,semanal,quincenal,mensual',
        ]);

        $amount = (float) $request->amount;
        $rate = $request->interest_rate / 100;
        $system = $request->amortization_system;
        $freq = ($loan->type === 'cuotas' && $system === 'frances') ? ($request->payment_frequency ?? 'mensual') : 'mensual';

        $totalAmount = $amount;
        $periodicPayment = 0;

        if ($loan->type === 'cuotas') {
            $months = $loan->months;
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
            'amortization_system' => $system,
            'payment_frequency' => $freq,
            'interest_rate' => $request->interest_rate,
            'total_amount' => $totalAmount,
            'monthly_payment' => $periodicPayment,
        ]);

        return back()->with('success', 'Contra-oferta enviada correctamente.');
    }

    public function approve(Loan $loan)
    {
        $loan->update(['status' => 'approved']);
        return back()->with('success', 'Préstamo aprobado correctamente.');
    }

    public function reject(Loan $loan)
    {
        $loan->update(['status' => 'rejected']);
        return back()->with('success', 'Préstamo rechazado.');
    }
}
