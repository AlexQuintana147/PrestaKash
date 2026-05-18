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
            ->paginate(10)
            ->withQueryString();

        $myInvestments = $user->investmentOpportunities()
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at'])
            ->latest('pivot_created_at')
            ->paginate(10, ['*'], 'my_page')
            ->withQueryString();

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
        if ($existing && $existing->pivot->status !== 'rejected') {
            return back()->with('error', 'Ya solicitaste participar en esta oportunidad.');
        }

        if ($existing && $existing->pivot->status === 'rejected') {
            $opportunity->investors()->detach($user->id);
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

    public function uploadVoucher(Request $request, InvestmentOpportunity $opportunity, $userId)
    {
        $user = Auth::user();

        if ($user->id != $userId) {
            abort(403);
        }

        $existing = $opportunity->investors()->where('user_id', $user->id)->first();
        if (!$existing || $existing->pivot->status !== 'approved') {
            return back()->with('error', 'No tienes una solicitud aprobada para esta oportunidad.');
        }

        if ($existing->pivot->payment_submitted_at) {
            return back()->with('error', 'Ya enviaste tu comprobante de pago.');
        }

        $approvedAt = $existing->pivot->approved_at ? \Carbon\Carbon::parse($existing->pivot->approved_at) : null;
        if ($approvedAt && $approvedAt->addDay()->isPast()) {
            return back()->with('error', 'El plazo para subir el comprobante ha vencido.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:transfer,cash',
            'operation_number' => 'required_if:payment_method,transfer|nullable|string|max:50',
            'voucher' => 'required_if:payment_method,transfer|nullable|image|max:5120',
        ]);

        $voucherPath = null;
        if ($request->hasFile('voucher') && $request->file('voucher')->isValid()) {
            $voucherPath = $request->file('voucher')->store('vouchers', 'public');
        }

        $opportunity->investors()->updateExistingPivot($user->id, [
            'payment_method' => $validated['payment_method'],
            'operation_number' => $validated['operation_number'] ?? null,
            'voucher_path' => $voucherPath,
            'payment_submitted_at' => now(),
            'payment_status' => 'pending',
        ]);

        return back()->with('success', 'Comprobante enviado. El administrador verificará tu pago.');
    }
}
