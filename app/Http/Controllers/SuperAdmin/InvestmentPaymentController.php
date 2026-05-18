<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function payouts()
    {
        $closedOpportunities = InvestmentOpportunity::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->with(['investors' => function ($q) {
                $q->wherePivot('status', 'approved')
                  ->wherePivot('payment_status', 'confirmed')
                  ->withPivot(['amount', 'payment_status', 'approved_at']);
            }])
            ->get()
            ->filter(fn($op) => $op->investors->count() > 0);

        $payouts = collect();
        $now = now();

        foreach ($closedOpportunities as $opportunity) {
            $totalInvested = 0;
            foreach ($opportunity->investors as $investor) {
                $totalInvested += $investor->pivot->amount;
            }

            foreach ($opportunity->investors as $investor) {
                $monthsSinceClose = $opportunity->closed_at->diffInMonths($now);
                $maxMonths = min(max($monthsSinceClose, 1), $opportunity->term_months);

                if ($maxMonths <= 0) continue;

                $monthlyAmount = round($investor->pivot->amount * ($opportunity->monthly_return_rate / 100), 2);
                $totalReturn = round($monthlyAmount * $opportunity->term_months, 2);
                $percentageOfTotal = $totalInvested > 0 ? round(($investor->pivot->amount / $totalInvested) * 100, 1) : 0;

                for ($month = 1; $month <= $maxMonths; $month++) {
                    $dueDate = $opportunity->closed_at->copy()->addMonths($month);
                    $isOverdue = $dueDate->lt($now);

                    $payouts->push((object)[
                        'opportunity' => $opportunity,
                        'investor' => $investor,
                        'month' => $month,
                        'total_months' => $opportunity->term_months,
                        'monthly_amount' => $monthlyAmount,
                        'total_return' => $totalReturn,
                        'percentage_of_total' => $percentageOfTotal,
                        'due_date' => $dueDate,
                        'is_overdue' => $isOverdue,
                        'currency' => $opportunity->currency,
                    ]);
                }
            }
        }

        $cashboxes = Cashbox::orderBy('name')->get();

        $perPage = 5;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $payouts = new LengthAwarePaginator(
            $payouts->forPage($page, $perPage)->values(),
            $payouts->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return view('superadmin.investments.payouts', compact('payouts', 'cashboxes'));
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
