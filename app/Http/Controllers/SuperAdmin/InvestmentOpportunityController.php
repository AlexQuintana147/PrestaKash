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

        return view('superadmin.investments.index', compact('opportunities', 'counts', 'filter', 'pendingRequests'));
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
                'deadline' => 'nullable|date|after:today',
            ],
            [],
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
            ->withPivot(['amount', 'status', 'admin_notes', 'created_at'])
            ->orderByPivot('amount', 'desc')
            ->paginate(5, ['*'], 'investors_page')
            ->withQueryString();

        $investment->setRelation('investors', $investors);

        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();

        return view('superadmin.investments.show', compact('investment', 'investors', 'cashboxes'));
    }

    public function update(Request $request, InvestmentOpportunity $investment)
    {
        $request->merge([
            'target_amount' => str_replace(',', '', $request->target_amount),
            'min_investment' => str_replace(',', '', $request->min_investment),
        ]);

        $validated = $request->validate(
            [
                'title' => 'sometimes|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'description' => 'sometimes|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'target_amount' => 'sometimes|numeric|min:0.01',
                'min_investment' => 'sometimes|numeric|min:0.01',
                'annual_return_rate' => 'sometimes|numeric|min:0|max:100',
                'term_months' => 'sometimes|integer|min:1',
                'risks' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+$/',
                'status' => 'sometimes|string|in:open,funded,closed,cancelled',
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

        $investment->update($validated);

        return back()->with('success', 'Oportunidad actualizada.');
    }

    public function approveInvestor(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = \App\Models\Cashbox::findOrFail($request->cashbox_id);
        $investor = $investment->investors()->where('user_id', $userId)->firstOrFail();
        $amount = (float) $investor->pivot->amount;

        if ($cashbox->currency !== $investment->currency) {
            return back()->withErrors(['cashbox_id' => 'La caja seleccionada no corresponde a la moneda de la oportunidad.']);
        }

        \DB::transaction(function () use ($investment, $userId, $cashbox, $amount, $investor) {
            $investment->investors()->updateExistingPivot($userId, [
                'status' => 'approved',
            ]);

            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'income',
                'amount' => $amount,
                'description' => "Ingreso por aprobación de inversión en {$investment->title} de {$investor->name}",
                'reference_type' => 'investment',
                'reference_id' => $investment->id,
                'investor_user_id' => $userId,
                'date' => now(),
            ]);

            $cashbox->increment('balance', $amount);
        });

        return back()->with('success', 'Inversor aprobado y movimiento de caja registrado.');
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
        $investment->investors()->updateExistingPivot($userId, [
            'status' => 'pending',
            'admin_notes' => null,
        ]);

        return back()->with('success', 'Inversor desaprobado. Vuelve a estado pendiente.');
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
