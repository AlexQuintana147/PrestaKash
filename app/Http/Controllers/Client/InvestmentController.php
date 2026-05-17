<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $opportunities = InvestmentOpportunity::where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('deadline')->orWhere('deadline', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $myInvestments = $user->investmentOpportunities()
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at'])
            ->latest('pivot_created_at')
            ->get();

        return view('client.invest.control', compact('opportunities', 'myInvestments'));
    }

    public function show(InvestmentOpportunity $opportunity)
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $opportunity->load(['approvedInvestors']);
        $existingRequest = $opportunity->investors()->where('user_id', $user->id)->first();

        return view('client.invest.show', compact('opportunity', 'existingRequest'));
    }

    public function apply(Request $request, InvestmentOpportunity $opportunity)
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        if (!$opportunity->isOpen()) {
            return back()->with('error', 'Esta oportunidad ya no está disponible.');
        }

        $existing = $opportunity->investors()->where('user_id', $user->id)->first();
        if ($existing) {
            return back()->with('error', 'Ya solicitaste participar en esta oportunidad.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $opportunity->min_investment . '|max:' . $opportunity->remaining_amount,
        ]);

        $opportunity->investors()->attach($user->id, [
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return redirect()->route('client.investments.index')->with('success', 'Solicitud enviada. El administrador revisará tu participación.');
    }
}
