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

        $query = Loan::with(['user.documents'])->latest();

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
            'amortization_system' => 'required|in:frances,americano',
            'interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        $rate = $request->interest_rate / 100;
        $totalAmount = $loan->amount;
        $monthlyPayment = 0;

        if ($loan->type === 'cuotas') {
            $n = $loan->months;
            if ($request->amortization_system === 'frances') {
                // French: Fixed installments
                // Formula: M = P * [i(1+i)^n] / [(1+i)^n - 1]
                $monthlyPayment = $loan->amount * ($rate * pow(1 + $rate, $n)) / (pow(1 + $rate, $n) - 1);
                $totalAmount = $monthlyPayment * $n;
            } else {
                // American: Fixed interest payments, principal at the end
                $interestPerMonth = $loan->amount * $rate;
                $monthlyPayment = $interestPerMonth; // Interest only
                $totalAmount = $loan->amount + ($interestPerMonth * $n);
            }
        } else {
            // Pago único
            $interest = $loan->amount * $rate;
            $totalAmount = $loan->amount + $interest;
            $monthlyPayment = $totalAmount;
        }

        $loan->update([
            'amortization_system' => $request->amortization_system,
            'interest_rate' => $request->interest_rate,
            'total_amount' => $totalAmount,
            'monthly_payment' => $monthlyPayment,
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
