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
                  ->withPivot(['amount', 'amortization_type', 'payment_status', 'approved_at']);
            }])
            ->get()
            ->filter(fn($op) => $op->investors->count() > 0);

        $opportunitiesData = collect();
        $now = now();

        foreach ($closedOpportunities as $opportunity) {
            $investorsData = collect();

            foreach ($opportunity->investors as $investor) {
                $amount = $investor->pivot->amount;
                $amortizationType = $investor->pivot->amortization_type;
                $monthlyRate = $opportunity->monthly_return_rate / 100;
                $termMonths = $opportunity->term_months;
                $startDate = $opportunity->closed_at ? \Carbon\Carbon::parse($opportunity->closed_at) : null;

                $schedule = collect();
                $totalExpectedReturn = 0;

                if ($amortizationType === 'frances') {
                    $monthlyPayment = $monthlyRate > 0
                        ? $amount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1)
                        : $amount / $termMonths;

                    $balance = $amount;
                    for ($i = 1; $i <= $termMonths; $i++) {
                        $interest = round($balance * $monthlyRate, 2);
                        $principal = round($monthlyPayment - $interest, 2);
                        $balance = round($balance - $principal, 2);
                        if ($i === $termMonths) {
                            $principal = round($principal + $balance, 2);
                            $balance = 0;
                        }
                        $totalExpectedReturn += round($interest + $principal, 2);

                        $dueDate = $startDate ? $startDate->copy()->addMonths($i) : null;
                        $schedule->push((object)[
                            'month' => $i,
                            'date' => $dueDate,
                            'payment' => round($interest + $principal, 2),
                            'interest' => $interest,
                            'principal' => $principal,
                            'balance' => max($balance, 0),
                            'is_overdue' => $dueDate ? $dueDate->lt($now) : false,
                        ]);
                    }
                } else {
                    $monthlyInterest = round($amount * $monthlyRate, 2);
                    for ($i = 1; $i <= $termMonths; $i++) {
                        $isLast = $i === $termMonths;
                        $payment = $isLast ? $monthlyInterest + $amount : $monthlyInterest;
                        $totalExpectedReturn += $payment;

                        $dueDate = $startDate ? $startDate->copy()->addMonths($i) : null;
                        $schedule->push((object)[
                            'month' => $i,
                            'date' => $dueDate,
                            'payment' => $payment,
                            'interest' => $monthlyInterest,
                            'principal' => $isLast ? $amount : 0,
                            'balance' => $isLast ? 0 : $amount,
                            'is_overdue' => $dueDate ? $dueDate->lt($now) : false,
                        ]);
                    }
                }

                $payments = \App\Models\CashboxMovement::where('investor_user_id', $investor->id)
                    ->where('reference_type', 'investment')
                    ->where('reference_id', $opportunity->id)
                    ->where('type', 'expense')
                    ->orderByDesc('date')
                    ->get();

                $paidMonths = $payments->map(fn($p) => $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m') : null)->filter()->values()->toArray();

                foreach ($schedule as $item) {
                    $item->is_paid = $item->date ? in_array($item->date->format('Y-m'), $paidMonths) : false;
                }

                $totalReceived = $payments->sum('amount');
                $overdueCount = $schedule->where('is_overdue', true)->where('is_paid', false)->count();
                $overdueAmount = $schedule->where('is_overdue', true)->where('is_paid', false)->sum('payment');
                $pendingCount = $schedule->where('is_paid', false)->count();

                $investorsData->push((object)[
                    'investor' => $investor,
                    'amount' => $amount,
                    'amortization_type' => $amortizationType,
                    'schedule' => $schedule,
                    'payments' => $payments,
                    'total_received' => $totalReceived,
                    'total_expected_return' => $totalExpectedReturn,
                    'overdue_count' => $overdueCount,
                    'overdue_amount' => $overdueAmount,
                    'pending_count' => $pendingCount,
                ]);
            }

            $opportunityOverdue = $investorsData->sum('overdue_count');
            $opportunityPendingAmount = $investorsData->sum(fn($d) => $d->schedule->where('is_paid', false)->sum('payment'));

            $opportunitiesData->push((object)[
                'opportunity' => $opportunity,
                'investors' => $investorsData,
                'overdue_count' => $opportunityOverdue,
                'pending_amount' => $opportunityPendingAmount,
            ]);
        }

        $cashboxes = Cashbox::orderBy('name')->get();

        return view('superadmin.investments.payouts', compact('opportunitiesData', 'cashboxes'));
    }

    public function scheduleDetail(InvestmentOpportunity $opportunity, $investor)
    {
        $investorModel = \App\Models\User::findOrFail($investor);
        $pivot = $opportunity->investors()->where('user_id', $investor)->firstOrFail();
        $amount = $pivot->pivot->amount;
        $amortizationType = $pivot->pivot->amortization_type;
        $monthlyRate = $opportunity->monthly_return_rate / 100;
        $termMonths = $opportunity->term_months;
        $startDate = $opportunity->closed_at ? \Carbon\Carbon::parse($opportunity->closed_at) : null;
        $now = now();

        $schedule = [];

        if ($amortizationType === 'frances') {
            $monthlyPayment = $monthlyRate > 0
                ? $amount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1)
                : $amount / $termMonths;

            $balance = $amount;
            for ($i = 1; $i <= $termMonths; $i++) {
                $interest = round($balance * $monthlyRate, 2);
                $principal = round($monthlyPayment - $interest, 2);
                $balance = round($balance - $principal, 2);
                if ($i === $termMonths) {
                    $principal = round($principal + $balance, 2);
                    $balance = 0;
                }

                $dueDate = $startDate ? $startDate->copy()->addMonths($i) : null;
                $schedule[] = [
                    'month' => $i,
                    'date' => $dueDate ? $dueDate->format('d/m/Y') : null,
                    'payment' => number_format(round($interest + $principal, 2), 2),
                    'interest' => number_format($interest, 2),
                    'principal' => number_format($principal, 2),
                    'balance' => number_format(max($balance, 0), 2),
                    'is_overdue' => $dueDate ? $dueDate->lt($now) : false,
                    'is_paid' => false,
                ];
            }
        } else {
            $monthlyInterest = round($amount * $monthlyRate, 2);
            for ($i = 1; $i <= $termMonths; $i++) {
                $isLast = $i === $termMonths;
                $payment = $isLast ? $monthlyInterest + $amount : $monthlyInterest;

                $dueDate = $startDate ? $startDate->copy()->addMonths($i) : null;
                $schedule[] = [
                    'month' => $i,
                    'date' => $dueDate ? $dueDate->format('d/m/Y') : null,
                    'payment' => number_format($payment, 2),
                    'interest' => number_format($monthlyInterest, 2),
                    'principal' => number_format($isLast ? $amount : 0, 2),
                    'balance' => number_format($isLast ? 0 : $amount, 2),
                    'is_overdue' => $dueDate ? $dueDate->lt($now) : false,
                    'is_paid' => false,
                ];
            }
        }

        $payments = \App\Models\CashboxMovement::where('investor_user_id', $investor)
            ->where('reference_type', 'investment')
            ->where('reference_id', $opportunity->id)
            ->where('type', 'expense')
            ->get();

        $paidMonths = $payments->map(fn($p) => $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m') : null)->filter()->values()->toArray();

        foreach ($schedule as &$item) {
            $dateObj = $startDate ? $startDate->copy()->addMonths($item['month']) : null;
            $item['is_paid'] = $dateObj ? in_array($dateObj->format('Y-m'), $paidMonths) : false;
        }

        return response()->json($schedule);
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
