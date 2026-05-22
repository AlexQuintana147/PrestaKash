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

        return view('client.invest.opportunities', compact('opportunities'));
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

        return view('client.invest.payments', compact('investments', 'payments', 'pendingPayouts', 'pendingPayoutsByOpportunity', 'confirmedPayoutsByOpportunity'));
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

                $schedule->push((object)[
                    'month' => $i,
                    'date' => $startDate ? $startDate->copy()->addMonths($i) : null,
                    'payment' => round($interest + $principal, 2),
                    'interest' => $interest,
                    'principal' => $principal,
                    'balance' => max($balance, 0),
                ]);
            }
        } else {
            $monthlyInterest = round($amount * $monthlyRate, 2);
            for ($i = 1; $i <= $termMonths; $i++) {
                $isLast = $i === $termMonths;
                $payment = $isLast ? $monthlyInterest + $amount : $monthlyInterest;
                $totalExpectedReturn += $payment;

                $schedule->push((object)[
                    'month' => $i,
                    'date' => $startDate ? $startDate->copy()->addMonths($i) : null,
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

        $totalReceived = $payments->sum('amount');

        $payouts = \App\Models\InvestorPayout::where('investment_opportunity_id', $opportunity->id)
            ->where('investor_user_id', $user->id)
            ->orderBy('month_number')
            ->get()
            ->keyBy('month_number');

        return view('client.invest.payment-detail', compact(
            'opportunity', 'investor', 'amount', 'amortizationType', 'schedule',
            'payments', 'totalReceived', 'totalExpectedReturn', 'isClosed', 'startDate',
            'payouts'
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

            \App\Models\CashboxMovement::create([
                'user_id' => auth()->id(),
                'cashbox_id' => 1,
                'type' => 'expense',
                'amount' => $payout->amount,
                'description' => "Pago aceptado por inversor - Cuota #{$scheduleMonth} - {$opportunity->title}",
                'reference_type' => 'investment',
                'reference_id' => $opportunity->id,
                'investor_user_id' => $user->id,
                'date' => $scheduleDate ?? now(),
            ]);
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
