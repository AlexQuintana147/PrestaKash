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

        $myInvestments = $user->investmentOpportunities()
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at'])
            ->latest('pivot_created_at')
            ->paginate(10, ['*'], 'my_page')
            ->withQueryString();

        $paymentInvestments = $user->investmentOpportunities()
            ->wherePivot('status', 'approved')
            ->wherePivot('payment_status', 'confirmed')
            ->withPivot(['amount', 'status', 'payment_status', 'approved_at'])
            ->latest('pivot_approved_at')
            ->get();

        $paymentInvestmentIds = $paymentInvestments->pluck('id')->toArray();

        $payments = collect();
        if ($paymentInvestmentIds) {
            $payments = \App\Models\CashboxMovement::where('investor_user_id', $user->id)
                ->where('reference_type', 'investment')
                ->whereIn('reference_id', $paymentInvestmentIds)
                ->with(['cashbox:id,name,currency', 'investment:id,code,title,currency'])
                ->orderByDesc('date')
                ->get()
                ->groupBy('reference_id');
        }

        return view('client.invest.control', compact('opportunities', 'myInvestments', 'paymentInvestments', 'payments'));
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

        return view('client.invest.payments', compact('investments', 'payments'));
    }
}
