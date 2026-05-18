<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;

class InvestmentOpportunityController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'open');
        $search = $request->get('search');

        $query = InvestmentOpportunity::with(['creator', 'approvedInvestors'])
            ->withCount(['investors as pending_investors_count' => function ($q) {
                $q->where('investor_opportunity.status', 'pending');
            }])
            ->withCount(['investors as pending_vouchers_count' => function ($q) {
                $q->where('investor_opportunity.payment_status', 'pending')
                  ->whereNotNull('investor_opportunity.voucher_path');
            }])
            ->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $opportunities = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => InvestmentOpportunity::count(),
            'open' => InvestmentOpportunity::where('status', 'open')->count(),
            'funded' => InvestmentOpportunity::where('status', 'funded')->count(),
            'closed' => InvestmentOpportunity::where('status', 'closed')->count(),
        ];

        $pendingRequests = InvestmentOpportunity::whereHas('investors', function ($q) {
            $q->where('investor_opportunity.status', 'pending');
        })->count();

        $pendingVouchers = \DB::table('investor_opportunity')
            ->where('payment_status', 'pending')
            ->whereNotNull('voucher_path')
            ->count();

        return view('superadmin.investments.index', compact('opportunities', 'counts', 'filter', 'pendingRequests', 'pendingVouchers'));
    }

    public function create()
    {
        return view('superadmin.investments.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'target_amount' => str_replace(',', '', $request->target_amount),
            'min_investment' => str_replace(',', '', $request->min_investment),
        ]);

        $validated = $request->validate(
            [
                'title' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'description' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'target_amount' => 'required|numeric|min:0.01',
                'min_investment' => 'required|numeric|min:0.01',
                'currency' => 'required|string|in:PEN,USD',
                'annual_return_rate' => 'required|numeric|min:0|max:100',
                'term_months' => 'required|integer|min:1',
                'risks' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'bank_name' => 'required|array|min:1',
                'bank_name.*' => 'required|string|max:255',
                'account_number' => 'required|array|min:1',
                'account_number.*' => 'required|string|max:30|regex:/^\d+$/',
                'cci' => 'required|array|min:1',
                'cci.*' => 'required|string|max:30|regex:/^\d+$/',
                'deadline' => 'nullable|date|after:today',
            ],
            [
                'bank_name.required' => 'Debes agregar al menos una cuenta bancaria.',
                'bank_name.min' => 'Debes agregar al menos una cuenta bancaria.',
                'bank_name.*.required' => 'El nombre del banco es obligatorio.',
                'account_number.required' => 'Debes agregar al menos un número de cuenta.',
                'account_number.min' => 'Debes agregar al menos un número de cuenta.',
                'account_number.*.required' => 'El número de cuenta es obligatorio.',
                'account_number.*.regex' => 'El número de cuenta solo debe contener números.',
                'account_number.*.max' => 'El número de cuenta no puede exceder 30 dígitos.',
                'cci.required' => 'Debes agregar al menos un CCI.',
                'cci.min' => 'Debes agregar al menos un CCI.',
                'cci.*.required' => 'El CCI es obligatorio.',
                'cci.*.regex' => 'El CCI solo debe contener números.',
                'cci.*.max' => 'El CCI no puede exceder 30 dígitos.',
            ],
            [
                'title' => 'título',
                'description' => 'descripción',
                'target_amount' => 'monto objetivo',
                'min_investment' => 'inversión mínima',
                'currency' => 'moneda',
                'annual_return_rate' => 'tasa de retorno',
                'term_months' => 'plazo',
                'risks' => 'riesgos',
                'deadline' => 'fecha límite',
            ]
        );

        $bankAccounts = [];
        foreach ($validated['bank_name'] as $idx => $bankName) {
            $bankAccounts[] = [
                'bank' => $bankName,
                'account_number' => $validated['account_number'][$idx],
                'cci' => $validated['cci'][$idx],
            ];
        }

        $validated['bank_accounts'] = $bankAccounts;
        unset($validated['bank_name'], $validated['account_number'], $validated['cci']);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        InvestmentOpportunity::create($validated);

        return redirect()->route('superadmin.investments.index')->with('success', 'Oportunidad de inversión creada con éxito.');
    }

    public function show(Request $request, InvestmentOpportunity $investment)
    {
        $investment->load(['creator']);

        $investors = $investment->investors()
            ->with(['documents'])
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at', 'payment_status', 'voucher_path', 'payment_submitted_at', 'operation_number'])
            ->orderByPivot('amount', 'desc')
            ->paginate(5, ['*'], 'investors_page')
            ->withQueryString();

        $investment->setRelation('investors', $investors);

        return view('superadmin.investments.show', compact('investment', 'investors'));
    }

    public function update(Request $request, InvestmentOpportunity $investment)
    {
        if ($request->filled('target_amount')) {
            $request->merge(['target_amount' => str_replace(',', '', $request->target_amount)]);
        }
        if ($request->filled('min_investment')) {
            $request->merge(['min_investment' => str_replace(',', '', $request->min_investment)]);
        }

        $validated = $request->validate(
            [
                'title' => 'nullable|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'description' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'target_amount' => 'nullable|numeric|min:0.01',
                'min_investment' => 'nullable|numeric|min:0.01',
                'annual_return_rate' => 'nullable|numeric|min:0|max:100',
                'term_months' => 'nullable|integer|min:1',
                'risks' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'status' => 'nullable|string|in:open,funded,closed,cancelled',
                'deadline' => 'nullable|date',
            ],
            [],
            [
                'title' => 'título',
                'description' => 'descripción',
                'target_amount' => 'monto objetivo',
                'min_investment' => 'inversión mínima',
                'annual_return_rate' => 'tasa de retorno',
                'term_months' => 'plazo',
                'risks' => 'riesgos',
                'status' => 'estado',
                'deadline' => 'fecha límite',
            ]
        );

        $investment->update(collect($validated)->filter(fn($v) => $v !== '' && $v !== null)->toArray());

        if (isset($validated['status']) && in_array($validated['status'], ['closed', 'cancelled'])) {
            $pendingIds = $investment->investors()->wherePivot('status', 'pending')->pluck('users.id');
            $investment->investors()->detach($pendingIds);
        }

        return back()->with('success', 'Oportunidad actualizada.');
    }

    public function approveInvestor(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $investor = $investment->investors()->where('user_id', $userId)->firstOrFail();

        $investment->investors()->updateExistingPivot($userId, [
            'status' => 'approved',
            'approved_at' => now(),
            'payment_status' => 'pending',
        ]);

        return back()->with('success', 'Inversor aprobado. El cliente debe subir su comprobante de pago.');
    }

    public function rejectInvestor(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $investment->investors()->updateExistingPivot($userId, [
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);

        return back()->with('success', 'Inversor rechazado.');
    }

    public function disapproveInvestor(Request $request, InvestmentOpportunity $investment, $userId)
    {
        \DB::transaction(function () use ($investment, $userId) {
            $movement = \App\Models\CashboxMovement::where('reference_type', 'investment')
                ->where('reference_id', $investment->id)
                ->where('investor_user_id', $userId)
                ->first();

            if ($movement) {
                $cashbox = $movement->cashbox;
                $cashbox->decrement('balance', $movement->amount);
                $movement->delete();
            }

            $investment->investors()->updateExistingPivot($userId, [
                'status' => 'pending',
                'admin_notes' => null,
                'approved_at' => null,
                'payment_method' => null,
                'voucher_path' => null,
                'operation_number' => null,
                'payment_submitted_at' => null,
                'payment_status' => null,
            ]);
        });

        return back()->with('success', 'Inversor desaprobado.');
    }

    public function reviewVoucher(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'operation_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9]+$/',
        ], [
            'operation_number.regex' => 'El número de operación solo puede contener letras y números.',
        ]);

        $pivot = $investment->investors()->where('users.id', $userId)->first()?->pivot;

        if (!$pivot || !$pivot->voucher_path) {
            return back()->with('error', 'No se encontró el comprobante.');
        }

        $status = $validated['action'] === 'approve' ? 'confirmed' : 'rejected';

        $investment->investors()->updateExistingPivot($userId, [
            'payment_status' => $status,
            'operation_number' => $validated['operation_number'] ?? null,
        ]);

        $actionText = $status === 'confirmed' ? 'aprobado' : 'rechazado';
        return back()->with("success", "Comprobante {$actionText} correctamente.");
    }

    public function disapprovePayment(InvestmentOpportunity $investment, $userId)
    {
        $pivot = $investment->investors()->where('users.id', $userId)->first()?->pivot;

        if (!$pivot || $pivot->payment_status !== 'confirmed') {
            return back()->with('error', 'No se encontró un pago aprobado para desaprobar.');
        }

        $investment->investors()->updateExistingPivot($userId, [
            'payment_status' => 'pending',
            'payment_submitted_at' => null,
        ]);

        return back()->with('success', 'Pago desaprobado. El inversor puede subir un nuevo comprobante.');
    }

    public function destroy(InvestmentOpportunity $investment)
    {
        if ($investment->approvedInvestors()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una oportunidad con inversores aprobados.');
        }

        $investment->delete();

        return redirect()->route('superadmin.investments.index')->with('success', 'Oportunidad eliminada.');
    }
}
