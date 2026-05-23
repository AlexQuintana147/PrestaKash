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
        $closedOpportunities = InvestmentOpportunity::whereIn('status', ['funded', 'closed'])
            ->with(['investors' => function ($q) {
                $q->wherePivot('status', 'approved')
                  ->wherePivot('payment_status', 'confirmed')
                  ->withPivot(['amount', 'amortization_type', 'payment_status', 'approved_at'])
                  ->with(['bankAccounts']);
            }])
            ->get()
            ->filter(fn($op) => $op->investors->count() > 0);

        foreach ($closedOpportunities as $opp) {
            if (!$opp->closed_at) {
                $opp->update(['closed_at' => now()]);
                $opp->refresh();
            }
        }

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

                $payouts = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
                    ->where('investor_user_id', $investor->id)
                    ->orderBy('month_number')
                    ->get()
                    ->keyBy('month_number');

                foreach ($schedule as $item) {
                    $item->is_paid = $item->date ? in_array($item->date->format('Y-m'), $paidMonths) : false;
                    $item->payout = $payouts->get($item->month);
                    if ($item->payout && $item->payout->status === 'confirmed') {
                        $item->is_paid = true;
                    }
                }

                $totalReceived = $payments->sum('amount');
                $confirmedPayoutsTotal = $payouts->where('status', 'confirmed')->sum('amount');
                if ($confirmedPayoutsTotal > 0 && $totalReceived == 0) {
                    $totalReceived = $confirmedPayoutsTotal;
                } elseif ($confirmedPayoutsTotal > $totalReceived) {
                    $totalReceived = $confirmedPayoutsTotal;
                }
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
        $investorModel = \App\Models\User::with('bankAccounts')->findOrFail($investor);
        $pivot = $opportunity->investors()->where('user_id', $investor)->firstOrFail();
        $amount = $pivot->pivot->amount;
        $amortizationType = $pivot->pivot->amortization_type;
        $monthlyRate = $opportunity->monthly_return_rate / 100;
        $termMonths = $opportunity->term_months;
        $startDate = $opportunity->closed_at ? \Carbon\Carbon::parse($opportunity->closed_at) : null;
        $now = now();

        $payouts = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $investor)
            ->get()
            ->keyBy('month_number');

        $interestOnlyMonths = $payouts->filter(fn($p) => in_array($p->status, ['confirmed', 'paid']) && $p->payment_type === 'interest_only')
            ->keys()
            ->toArray();

        $extraMonths = count($interestOnlyMonths);
        $effectiveTermMonths = $termMonths + $extraMonths;

        $schedule = [];

        if ($amortizationType === 'frances') {
            $monthlyPayment = $monthlyRate > 0
                ? $amount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1)
                : $amount / $termMonths;

            $balance = $amount;
            $dateOffset = 0;

            for ($i = 1; $i <= $effectiveTermMonths; $i++) {
                $interest = round($balance * $monthlyRate, 2);
                $principal = round($monthlyPayment - $interest, 2);
                $balance = round($balance - $principal, 2);

                if (in_array($i, $interestOnlyMonths)) {
                    $principal = 0;
                    $balance = round($balance + round($monthlyPayment - $interest, 2), 2);
                }

                if ($i === $effectiveTermMonths) {
                    $principal = round($principal + $balance, 2);
                    $balance = 0;
                }

                if (in_array($i, $interestOnlyMonths)) {
                    $dateOffset++;
                }

                $dueDate = $startDate ? $startDate->copy()->addMonths($i + $dateOffset) : null;
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
            $dateOffset = 0;

            for ($i = 1; $i <= $effectiveTermMonths; $i++) {
                $isLast = $i === $effectiveTermMonths;
                $payment = $isLast ? $monthlyInterest + $amount : $monthlyInterest;

                if (in_array($i, $interestOnlyMonths)) {
                    $dateOffset++;
                }

                $dueDate = $startDate ? $startDate->copy()->addMonths($i + $dateOffset) : null;
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

        $confirmedMonthNumbers = $payouts->filter(fn($p) => in_array($p->status, ['confirmed', 'paid']))->keys()->toArray();

        foreach ($schedule as &$item) {
            $payout = $payouts->get($item['month']);
            $item['is_paid'] = in_array($item['month'], $confirmedMonthNumbers);
            $item['payout_status'] = $payout ? $payout->status : null;
            $item['payout_id'] = $payout ? $payout->id : null;
            $item['payout_type'] = $payout ? $payout->payment_type : null;
            $item['paid_at'] = ($payout && $payout->paid_at) ? $payout->paid_at->format('d/m/Y') : null;
        }
        unset($item);

        usort($schedule, fn($a, $b) => $a['month'] - $b['month']);
        $schedule = array_values($schedule);

        $nextPayableMonth = null;
        foreach ($schedule as $item) {
            if (!$item['is_paid'] && $item['payout_status'] !== 'pending') {
                $nextPayableMonth = $item['month'];
                break;
            }
        }

        return response()->json([
            'schedule' => $schedule,
            'investor' => [
                'id' => $investorModel->id,
                'name' => $investorModel->name,
                'bank_accounts' => $investorModel->bankAccounts->map(fn($ba) => [
                    'id' => $ba->id,
                    'bank_name' => $ba->bank_name,
                    'account_type' => $ba->account_type,
                    'account_number' => $ba->account_number,
                    'currency' => $ba->currency,
                    'cci' => $ba->cci,
                ]),
            ],
            'amortization_type' => $amortizationType,
            'opportunity_id' => $opportunity->id,
            'opportunity_code' => $opportunity->code,
            'next_payable_month' => $nextPayableMonth,
        ]);
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

    public function storePayout(Request $request)
    {
        $validated = $request->validate([
            'opportunity_id' => 'required|exists:investment_opportunities,id',
            'investor_id' => 'required|exists:users,id',
            'month_number' => 'required|integer|min:1',
            'payment_type' => 'required|in:full,interest_only',
            'payment_method' => 'required|in:transfer,cash',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'voucher' => 'nullable|image|max:5120',
            'operation_number' => 'nullable|string|max:50',
        ]);

        $opportunity = InvestmentOpportunity::findOrFail($validated['opportunity_id']);
        $pivot = $opportunity->investors()->where('user_id', $validated['investor_id'])->firstOrFail();

        if (!in_array($pivot->pivot->status, ['approved']) || $pivot->pivot->payment_status !== 'confirmed') {
            return back()->with('error', 'El inversor no está aprobado o no ha confirmado su inversión.');
        }

        $existingPayout = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $validated['investor_id'])
            ->where('month_number', $validated['month_number'])
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($existingPayout) {
            return back()->with('error', 'Ya existe un pago registrado para esta cuota.');
        }

        $previousUnpaid = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $validated['investor_id'])
            ->where('month_number', '<', $validated['month_number'])
            ->where('status', '!=', 'confirmed')
            ->doesntExist();

        $previousPaid = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $validated['investor_id'])
            ->where('month_number', '<', $validated['month_number'])
            ->where('status', 'confirmed')
            ->count();

        if ($previousPaid < $validated['month_number'] - 1) {
            return back()->with('error', 'Debes pagar las cuotas anteriores en orden.');
        }

        $amount = (float) $pivot->pivot->amount;
        $amortizationType = $pivot->pivot->amortization_type;
        $monthlyRate = $opportunity->monthly_return_rate / 100;
        $termMonths = $opportunity->term_months;

        $interestOnlyCount = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $validated['investor_id'])
            ->where('status', 'confirmed')
            ->where('payment_type', 'interest_only')
            ->count();

        $effectiveTermMonths = $termMonths + $interestOnlyCount;

        $interestAmount = 0;
        $principalAmount = 0;
        $totalAmount = 0;

        if ($amortizationType === 'frances') {
            $monthlyPayment = $monthlyRate > 0
                ? $amount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1)
                : $amount / $termMonths;

            $confirmedPayouts = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
                ->where('investor_user_id', $validated['investor_id'])
                ->where('status', 'confirmed')
                ->orderBy('month_number')
                ->get();

            $interestOnlyMonthNumbers = $confirmedPayouts->where('payment_type', 'interest_only')->pluck('month_number')->toArray();

            $balance = $amount;

            for ($i = 1; $i <= $validated['month_number']; $i++) {
                $interest = round($balance * $monthlyRate, 2);
                $principal = round($monthlyPayment - $interest, 2);
                $balance = round($balance - $principal, 2);

                if (in_array($i, $interestOnlyMonthNumbers)) {
                    $principal = 0;
                    $balance = round($balance + round($monthlyPayment - $interest, 2), 2);
                }

                if ($i === $effectiveTermMonths) {
                    $principal = round($principal + $balance, 2);
                    $balance = 0;
                }

                if ($i == $validated['month_number']) {
                    $interestAmount = $interest;
                    $principalAmount = $principal;
                    break;
                }
            }

            if ($validated['payment_type'] === 'interest_only') {
                $totalAmount = $interestAmount;
                $principalAmount = 0;
            } else {
                $totalAmount = round($interestAmount + $principalAmount, 2);
            }
        } else {
            $monthlyInterest = round($amount * $monthlyRate, 2);
            $isLast = $validated['month_number'] === $effectiveTermMonths;

            $interestAmount = $monthlyInterest;
            $principalAmount = $isLast ? $amount : 0;

            if ($validated['payment_type'] === 'interest_only') {
                $totalAmount = $interestAmount;
                $principalAmount = 0;
            } else {
                $totalAmount = $isLast ? $monthlyInterest + $amount : $monthlyInterest;
            }
        }

        $voucherPath = null;
        if ($request->hasFile('voucher') && $request->file('voucher')->isValid()) {
            $imageService = new \App\Services\ImageService();
            $voucherPath = $imageService->storeAsWebp($request->file('voucher'), 'vouchers');
        }

        \App\Models\InvestorPayout::create([
            'investment_opportunity_id' => $opportunity->id,
            'investor_user_id' => $validated['investor_id'],
            'cashbox_id' => $validated['cashbox_id'],
            'month_number' => $validated['month_number'],
            'amount' => $totalAmount,
            'interest_amount' => $interestAmount,
            'principal_amount' => $principalAmount,
            'payment_type' => $validated['payment_type'],
            'payment_method' => $validated['payment_method'],
            'voucher_path' => $voucherPath,
            'operation_number' => $validated['operation_number'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pago registrado. El inversor debe confirmar la recepción.');
    }

    public function confirmPayout(Request $request, \App\Models\InvestorPayout $payout)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        $cashbox = Cashbox::findOrFail($request->cashbox_id);
        $opportunity = $payout->opportunity;
        $investor = $payout->investor;

        if ($cashbox->currency !== $opportunity->currency) {
            return back()->withErrors(['cashbox_id' => 'La caja no corresponde a la moneda de la oportunidad.']);
        }

        \DB::transaction(function () use ($payout, $cashbox, $opportunity, $investor) {
            $payout->update([
                'status' => 'confirmed',
                'paid_at' => now(),
                'cashbox_id' => $cashbox->id,
            ]);

            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $payout->amount,
                'description' => "Pago a inversor {$investor->name} - Cuota #{$payout->month_number} - {$opportunity->title}",
                'reference_type' => 'investment_payout',
                'reference_id' => $payout->id,
                'investor_user_id' => $investor->id,
                'date' => now(),
            ]);

            $cashbox->decrement('balance', $payout->amount);
        });

        return back()->with('success', 'Pago confirmado y egreso registrado en caja.');
    }

    public function rejectPayout(Request $request, \App\Models\InvestorPayout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        if ($payout->voucher_path && \Storage::disk('public')->exists($payout->voucher_path)) {
            \Storage::disk('public')->delete($payout->voucher_path);
        }

        $payout->update([
            'status' => 'rejected',
            'voucher_path' => null,
        ]);

        return back()->with('success', 'Pago rechazado.');
    }
}
