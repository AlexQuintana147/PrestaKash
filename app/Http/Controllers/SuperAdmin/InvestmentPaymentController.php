<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;

class InvestmentPaymentController extends Controller
{
    public function index()
    {
        $payments = InvestmentOpportunity::whereHas('investors', function ($q) {
            $q->wherePivot('status', 'approved')
              ->wherePivot('payment_status', 'pending');
        })
            ->with(['investors' => function ($q) {
                $q->wherePivot('status', 'approved')
                  ->wherePivot('payment_status', 'pending')
                  ->withPivot(['amount', 'payment_method', 'voucher_path', 'operation_number', 'payment_submitted_at', 'approved_at', 'payment_status']);
            }])
            ->latest()
            ->get();

        $cashboxes = Cashbox::orderBy('name')->get();

        return view('superadmin.investments.payments', compact('payments', 'cashboxes'));
    }

    public function confirm(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = Cashbox::findOrFail($request->cashbox_id);
        $investor = $investment->investors()->where('user_id', $userId)->firstOrFail();
        $amount = (float) $investor->pivot->amount;

        if ($cashbox->currency !== $investment->currency) {
            return back()->withErrors(['cashbox_id' => 'La caja no corresponde a la moneda de la oportunidad.']);
        }

        \DB::transaction(function () use ($investment, $userId, $cashbox, $amount, $investor) {
            $investment->investors()->updateExistingPivot($userId, [
                'payment_status' => 'confirmed',
            ]);

            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'income',
                'amount' => $amount,
                'description' => "Ingreso por inversión en {$investment->title} de {$investor->name}",
                'reference_type' => 'investment',
                'reference_id' => $investment->id,
                'investor_user_id' => $userId,
                'date' => now(),
            ]);

            $cashbox->increment('balance', $amount);
        });

        return back()->with('success', 'Pago confirmado y registrado en caja.');
    }

    public function reject(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $investment->investors()->updateExistingPivot($userId, [
            'payment_status' => 'rejected',
        ]);

        return back()->with('success', 'Pago rechazado.');
    }
}
