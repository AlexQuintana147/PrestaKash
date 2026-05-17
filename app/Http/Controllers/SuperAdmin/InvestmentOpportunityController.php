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

        $query = InvestmentOpportunity::with(['creator', 'approvedInvestors'])->latest();

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1',
            'min_investment' => 'required|numeric|min:1',
            'currency' => 'required|string|in:PEN,USD',
            'annual_return_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1',
            'risks' => 'nullable|string',
            'deadline' => 'nullable|date|after:today',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        InvestmentOpportunity::create($validated);

        return redirect()->route('superadmin.investments.index')->with('success', 'Oportunidad de inversión creada con éxito.');
    }

    public function show(InvestmentOpportunity $investment)
    {
        $investment->load(['creator', 'investors' => function ($q) {
            $q->withPivot(['amount', 'status', 'admin_notes', 'created_at']);
        }]);

        return view('superadmin.investments.show', compact('investment'));
    }

    public function update(Request $request, InvestmentOpportunity $investment)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'target_amount' => 'sometimes|numeric|min:1',
            'min_investment' => 'sometimes|numeric|min:1',
            'annual_return_rate' => 'sometimes|numeric|min:0|max:100',
            'term_months' => 'sometimes|integer|min:1',
            'risks' => 'nullable|string',
            'status' => 'sometimes|string|in:open,funded,closed,cancelled',
            'deadline' => 'nullable|date',
        ]);

        $investment->update($validated);

        return back()->with('success', 'Oportunidad actualizada.');
    }

    public function approveInvestor(Request $request, InvestmentOpportunity $investment, $userId)
    {
        $investment->investors()->updateExistingPivot($userId, [
            'status' => 'approved',
        ]);

        return back()->with('success', 'Inversor aprobado.');
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

    public function destroy(InvestmentOpportunity $investment)
    {
        if ($investment->approvedInvestors()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una oportunidad con inversores aprobados.');
        }

        $investment->delete();

        return redirect()->route('superadmin.investments.index')->with('success', 'Oportunidad eliminada.');
    }
}
