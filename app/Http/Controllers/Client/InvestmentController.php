<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
            ->whereDoesntHave('investors', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $rejectedOpportunityId = $user->investment_rejection_opportunity_id;

        return view('client.invest.opportunities', compact('opportunities', 'rejectedOpportunityId'));
    }

    public function myInvestments()
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $myInvestments = $user->investmentOpportunities()
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at'])
            ->latest('pivot_created_at')
            ->paginate(10)
            ->withQueryString();

        return view('client.invest.my-investments', compact('myInvestments'));
    }

    public function show(InvestmentOpportunity $opportunity)
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $opportunity->load(['approvedInvestors']);
        $existingRequest = $opportunity->investors()->where('user_id', $user->id)->first();

        $rejectionReason = $user->investment_rejection_reason;

        return view('client.invest.show', compact('opportunity', 'existingRequest', 'rejectionReason'));
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

        if ($user->investment_rejection_reason) {
            $user->investment_rejection_reason = null;
            $user->investment_rejection_opportunity_id = null;
            $user->save();
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $opportunity->min_investment . '|max:' . $opportunity->remaining_amount,
            'amortization_type' => 'required|string|in:frances,americano',
        ]);

        $opportunity->investors()->attach($user->id, [
            'amount' => $validated['amount'],
            'amortization_type' => $validated['amortization_type'],
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

        if ($existing->pivot->payment_submitted_at && $existing->pivot->payment_status !== 'rejected') {
            return back()->with('error', 'Ya enviaste tu comprobante de pago.');
        }

        $approvedAt = $existing->pivot->approved_at ? \Carbon\Carbon::parse($existing->pivot->approved_at) : null;
        if ($approvedAt && $approvedAt->addDay()->isPast()) {
            return back()->with('error', 'El plazo para subir el comprobante ha vencido.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:transfer,cash',
            'operation_number' => 'required_if:payment_method,transfer|nullable|string|max:50',
            'voucher' => 'required|image|max:5120',
        ]);

        $voucherPath = null;
        try {
            if ($request->hasFile('voucher') && $request->file('voucher')->isValid()) {
                $file = $request->file('voucher');
                \Log::info('[VOUCHER UPLOAD] Archivo recibido', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);

                $manager = new ImageManager(new Driver());
                $image = $manager->read($file);

                \Log::info('[VOUCHER UPLOAD] Imagen leida', [
                    'width' => $image->width(),
                    'height' => $image->height(),
                ]);

                if ($image->width() > 1200) {
                    $image->scaleDown(width: 1200);
                }

                $fileName = 'voucher_' . uniqid() . '_' . time() . '.webp';
                $fullPath = storage_path('app/public/vouchers/' . $fileName);

                $encoded = $image->toWebp(80);
                file_put_contents($fullPath, $encoded);
                $voucherPath = 'vouchers/' . $fileName;

                \Log::info('[VOUCHER UPLOAD] Guardado OK', ['path' => $voucherPath]);
            }
        } catch (\Exception $e) {
            \Log::error('[VOUCHER UPLOAD] Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al procesar la imagen: ' . $e->getMessage());
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

    public function payments()
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $investments = $user->investmentOpportunities()
            ->wherePivot('status', 'approved')
            ->wherePivot('payment_status', 'confirmed')
            ->withPivot(['amount', 'status', 'payment_status', 'approved_at'])
            ->latest('pivot_approved_at')
            ->get();

        $investmentIds = $investments->pluck('id')->toArray();

        $payments = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
            ->where('reference_type', 'investment')
            ->whereIn('reference_id', $investmentIds)
            ->with(['cashbox:id,name,currency', 'investment:id,code,title,currency'])
            ->orderByDesc('date')
            ->get()
            ->groupBy('reference_id');

        $pendingPayouts = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
            ->where('status', 'pending')
            ->with(['opportunity:id,code,title,currency'])
            ->orderByDesc('created_at')
            ->get();

        $pendingPayoutsByOpportunity = $pendingPayouts->groupBy('investment_opportunity_id')->map->count();

        $paidAmountsByInvestment = [];
        foreach ($investmentIds as $invId) {
            $cbDirect = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
                ->where('reference_type', 'investment')
                ->where('reference_id', $invId)
                ->where('type', 'expense')
                ->sum('amount');

            $payoutTotal = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
                ->where('investment_opportunity_id', $invId)
                ->whereIn('status', ['confirmed', 'paid'])
                ->sum('amount');

            $paidPayoutIds = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
                ->where('investment_opportunity_id', $invId)
                ->whereIn('status', ['confirmed', 'paid'])
                ->pluck('id');

            $cbFromPayouts = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
                ->where('reference_type', 'investment_payout')
                ->whereIn('reference_id', $paidPayoutIds)
                ->where('type', 'expense')
                ->sum('amount');

            $paidAmountsByInvestment[$invId] = max($cbDirect, $payoutTotal, $cbFromPayouts);
        }

        $confirmedPayoutsByOpportunity = collect($paidAmountsByInvestment);

        $paidQuotasByOpportunity = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
            ->whereIn('investment_opportunity_id', $investmentIds)
            ->whereIn('status', ['confirmed', 'paid'])
            ->selectRaw('investment_opportunity_id, COUNT(*) as paid_count')
            ->groupBy('investment_opportunity_id')
            ->pluck('paid_count', 'investment_opportunity_id');

        $interestOnlyByOpportunity = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
            ->whereIn('investment_opportunity_id', $investmentIds)
            ->whereIn('status', ['confirmed', 'paid'])
            ->where('payment_type', 'interest_only')
            ->selectRaw('investment_opportunity_id, COUNT(*) as io_count')
            ->groupBy('investment_opportunity_id')
            ->pluck('io_count', 'investment_opportunity_id');

        return view('client.invest.payments', compact('investments', 'payments', 'pendingPayouts', 'pendingPayoutsByOpportunity', 'confirmedPayoutsByOpportunity', 'paidQuotasByOpportunity', 'interestOnlyByOpportunity'));
    }

    public function paymentDetail(InvestmentOpportunity $opportunity)
    {
        $user = Auth::user();

        if (!$user->is_investor) {
            return redirect()->route('client.invertir');
        }

        $investor = $opportunity->investors()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'approved')
            ->wherePivot('payment_status', 'confirmed')
            ->withPivot(['amount', 'amortization_type', 'status', 'payment_status', 'approved_at'])
            ->first();

        if (!$investor) {
            return redirect()->route('client.investments.index')->with('error', 'No tienes acceso a esta inversión.');
        }

        $amount = $investor->pivot->amount;
        $amortizationType = $investor->pivot->amortization_type;
        $monthlyRate = $opportunity->monthly_return_rate / 100;
        $termMonths = $opportunity->term_months;
        $isClosed = in_array($opportunity->status, ['closed', 'cancelled']);
        $isFunded = $opportunity->status === 'funded';
        $startDate = $opportunity->closed_at ? \Carbon\Carbon::parse($opportunity->closed_at) : null;

        $payouts = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $user->id)
            ->get()
            ->keyBy('month_number');

        $interestOnlyMonths = $payouts->filter(fn($p) => in_array($p->status, ['confirmed', 'paid']) && $p->payment_type === 'interest_only')
            ->keys()
            ->toArray();

        $extraMonths = count($interestOnlyMonths);
        $effectiveTermMonths = $termMonths + $extraMonths;

        $schedule = collect();
        $totalExpectedReturn = 0;

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

                $totalExpectedReturn += round($interest + $principal, 2);

                $schedule->push((object)[
                    'month' => $i,
                    'date' => $startDate ? $startDate->copy()->addMonths($i + $dateOffset) : null,
                    'payment' => round($interest + $principal, 2),
                    'interest' => $interest,
                    'principal' => $principal,
                    'balance' => max($balance, 0),
                ]);
            }
        } else {
            $monthlyInterest = round($amount * $monthlyRate, 2);
            $dateOffset = 0;

            for ($i = 1; $i <= $effectiveTermMonths; $i++) {
                $isLast = $i === $effectiveTermMonths;
                $payment = $isLast ? $monthlyInterest + $amount : $monthlyInterest;
                $totalExpectedReturn += $payment;

                if (in_array($i, $interestOnlyMonths)) {
                    $dateOffset++;
                }

                $schedule->push((object)[
                    'month' => $i,
                    'date' => $startDate ? $startDate->copy()->addMonths($i + $dateOffset) : null,
                    'payment' => $payment,
                    'interest' => $monthlyInterest,
                    'principal' => $isLast ? $amount : 0,
                    'balance' => $isLast ? 0 : $amount,
                ]);
            }
        }

        $payments = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
            ->where('reference_type', 'investment')
            ->where('reference_id', $opportunity->id)
            ->where('type', 'expense')
            ->with(['cashbox:id,name,currency'])
            ->orderByDesc('date')
            ->get();

        $cashboxReceived = $payments->sum('amount');

        $payoutReceived = $payouts->whereIn('status', ['confirmed', 'paid'])->sum('amount');

        $payoutCashboxReceived = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
            ->where('reference_type', 'investment_payout')
            ->whereIn('reference_id', $payouts->whereIn('status', ['confirmed', 'paid'])->pluck('id'))
            ->where('type', 'expense')
            ->sum('amount');

        $totalReceived = max($cashboxReceived, $payoutReceived, $payoutCashboxReceived);

        $paidQuotas = $payments->count();
        $payoutPaidQuotas = $payouts->whereIn('status', ['confirmed', 'paid'])->count();
        $totalPaidQuotas = max($paidQuotas, $payoutPaidQuotas);

        $pendingPayouts = $payouts->where('status', 'pending');

        $nextPayoutDate = null;
        $nextMonth = $totalPaidQuotas + 1;
        if ($nextMonth <= $effectiveTermMonths) {
            $nextScheduleItem = $schedule->firstWhere('month', $nextMonth);
            if ($nextScheduleItem && $nextScheduleItem->date) {
                $nextPayoutDate = $nextScheduleItem->date;
            }
        }

        $totalInterestEarned = 0;
        foreach ($schedule as $s) {
            if ($s->month <= $totalPaidQuotas) {
                $totalInterestEarned += $s->interest;
            }
        }

        return view('client.invest.payment-detail', compact(
            'opportunity', 'investor', 'amount', 'amortizationType', 'schedule',
            'payments', 'totalReceived', 'totalExpectedReturn', 'isClosed', 'isFunded', 'startDate',
            'payouts', 'totalPaidQuotas', 'pendingPayouts', 'nextPayoutDate',
            'totalInterestEarned'
        ));
    }

    public function acceptPayout(\App\Models\InvestorPayout $payout)
    {
        $user = Auth::user();

        if ($payout->investor_user_id !== $user->id) {
            abort(403);
        }

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        if ($payout->voucher_path && \Storage::disk('public')->exists($payout->voucher_path)) {
            \Storage::disk('public')->delete($payout->voucher_path);
        }

        \DB::transaction(function () use ($payout, $user) {
            $payout->update([
                'status' => 'confirmed',
                'paid_at' => now(),
                'voucher_path' => null,
            ]);

            $opportunity = $payout->opportunity;
            $scheduleMonth = $payout->month_number;
            $scheduleDate = null;

            $startDate = $opportunity->closed_at ? \Carbon\Carbon::parse($opportunity->closed_at) : null;
            if ($startDate) {
                $scheduleDate = $startDate->copy()->addMonths($scheduleMonth);
            }

            $cashboxId = $payout->cashbox_id;
            if (!$cashboxId) {
                $cashboxId = \App\Models\Cashbox::where('currency', $opportunity->currency)->first()?->id ?? 1;
            }

            $cashbox = \App\Models\Cashbox::findOrFail($cashboxId);

            if ($cashbox->balance < $payout->amount) {
                return back()->withErrors(['error' => 'Saldo insuficiente en la caja para realizar este pago.']);
            }

            \App\Models\CashboxMovement::create([
                'user_id' => auth()->id(),
                'cashbox_id' => $cashboxId,
                'type' => 'expense',
                'amount' => $payout->amount,
                'description' => "Pago a inversor {$user->name} - Cuota #{$scheduleMonth} - {$opportunity->title}",
                'reference_type' => 'investment_payout',
                'reference_id' => $payout->id,
                'investor_user_id' => $user->id,
                'date' => $scheduleDate ?? now(),
            ]);

            $cashbox->safeDecrement('balance', $payout->amount);
        });

        return back()->with('success', 'Pago aceptado correctamente.');
    }

    public function rejectPayout(Request $request, \App\Models\InvestorPayout $payout)
    {
        $user = Auth::user();

        if ($payout->investor_user_id !== $user->id) {
            abort(403);
        }

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
