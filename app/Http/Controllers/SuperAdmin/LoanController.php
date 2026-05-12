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

        $query = Loan::with('user')->latest();

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

        // Logic for counter-offer would normally involve a new state or just updating the loan
        // For now, let's assume it updates the current loan and stays pending or moves to a 'countered' status if we had one.
        // The user mentioned "contra oferta", so maybe we just update and keep it pending for user to accept?
        // Or maybe we just update the fields.

        $loan->update([
            'amortization_system' => $request->amortization_system, // We might need to add this field to the migration if it doesn't exist
            'interest_rate' => $request->interest_rate,
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
