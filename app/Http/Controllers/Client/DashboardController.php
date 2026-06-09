<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\ProfileChangeRequest;
use App\Models\UbigeoDepartment;
use App\Models\UbigeoDistrict;
use App\Models\UbigeoProvince;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('documents', 'clientProfile', 'loans');

        $department = $user->department
            ? UbigeoDepartment::where('id', str_pad($user->department, 2, '0', STR_PAD_LEFT))->value('name')
            : null;

        $province = $user->province
            ? UbigeoProvince::where('id', str_pad($user->province, 4, '0', STR_PAD_LEFT))->value('name')
            : null;

        $district = $user->district
            ? UbigeoDistrict::where('id', str_pad($user->district, 6, '0', STR_PAD_LEFT))->value('name')
            : null;

        $activeLoans = $user->loans->whereIn('status', ['approved', 'disbursed', 'active']);
        $pendingLoans = $user->loans->where('status', 'pending');
        $totalDebt = $activeLoans->sum('total_amount');
        $monthlyPayment = $activeLoans->sum('monthly_payment');
        $hasProfile = $user->clientProfile !== null;
        $pendingChange = ProfileChangeRequest::where('user_id', $user->id)->where('status', 'pending')->first();
        $rejectedChange = ProfileChangeRequest::where('user_id', $user->id)->where('status', 'rejected')->first();
        $isInvestor = $user->is_investor;
        $hasInvestorApp = $user->investorApplication !== null;

        $overdueInstallments = LoanInstallment::with('loan')
            ->whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereIn('status', ['approved', 'disbursed', 'active', 'paid']);
            })
            ->where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->get();

        $hasOverdue = $overdueInstallments->isNotEmpty();
        $overdueCount = $overdueInstallments->count();
        $overdueTotal = $overdueInstallments->sum('amount') + $overdueInstallments->sum('late_fee');
        $overdueLateFees = $overdueInstallments->sum('late_fee');
        $oldestOverdue = $overdueInstallments->first();

        $overdueByLoan = $overdueInstallments->groupBy('loan_id')->map(function ($items) {
            $loan = $items->first()->loan;
            return [
                'loan_id' => $loan->id,
                'loan_code' => $loan->code,
                'currency' => $loan->currency,
                'overdue_count' => $items->count(),
                'total_amount' => $items->sum('amount'),
                'total_late_fee' => $items->sum('late_fee'),
                'total_with_fee' => $items->sum('amount') + $items->sum('late_fee'),
                'oldest_due_date' => $items->first()->due_date,
                'days_overdue' => now()->startOfDay()->diffInDays($items->first()->due_date),
            ];
        })->values();

        $upcomingInstallments = LoanInstallment::with('loan')
            ->whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereIn('status', ['approved', 'disbursed', 'active']);
            })
            ->where('status', '!=', 'approved')
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(5)->endOfDay()])
            ->orderBy('due_date', 'asc')
            ->get();

        $hasUpcoming = $upcomingInstallments->isNotEmpty();
        $upcomingCount = $upcomingInstallments->count();

        $upcomingByLoan = $upcomingInstallments->groupBy('loan_id')->map(function ($items) {
            $loan = $items->first()->loan;
            return [
                'loan_id' => $loan->id,
                'loan_code' => $loan->code,
                'currency' => $loan->currency,
                'amount' => $loan->amount,
                'upcoming_count' => $items->count(),
                'next_due_date' => $items->first()->due_date,
                'days_left' => (int) now()->startOfDay()->diffInDays($items->first()->due_date->startOfDay(), false),
                'next_amount' => $items->first()->amount,
            ];
        })->values();

        $pendingInvestmentPayouts = \App\Models\InvestorPayout::where('investor_user_id', $user->id)
            ->where('status', 'pending')
            ->with(['opportunity:id,code,title,currency'])
            ->orderByDesc('created_at')
            ->get();

        $hasPendingPayouts = $pendingInvestmentPayouts->isNotEmpty();
        $pendingPayoutsCount = $pendingInvestmentPayouts->count();
        $pendingPayoutsTotal = $pendingInvestmentPayouts->sum('amount');
        $pendingPayoutsByOpportunity = $pendingInvestmentPayouts->groupBy('investment_opportunity_id')->map(function ($items) {
            $opportunity = $items->first()->opportunity;
            return [
                'opportunity_id' => $opportunity->id,
                'opportunity_code' => $opportunity->code,
                'opportunity_title' => $opportunity->title,
                'currency' => $opportunity->currency,
                'payouts_count' => $items->count(),
                'total_amount' => $items->sum('amount'),
                'latest_created' => $items->first()->created_at,
            ];
        })->values();

        // Notificaciones de préstamos (solo las no descartadas)
        $dismissedIds = $user->dismissed_loan_notifications ?? [];
        $counterOfferLoans = $user->loans->where('status', 'counter_offered')
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));
        $acceptedLoans = $user->loans->whereIn('status', ['approved', 'counter_accepted'])
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));
        $rejectedLoans = $user->loans->where('status', 'rejected')
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));

        $hasLoanNotifications = $counterOfferLoans->isNotEmpty() || $acceptedLoans->isNotEmpty() || $rejectedLoans->isNotEmpty();

        return view('client.dashboard', compact(
            'user', 'department', 'province', 'district',
            'activeLoans', 'pendingLoans', 'totalDebt', 'monthlyPayment',
            'hasProfile', 'pendingChange', 'rejectedChange',
            'isInvestor', 'hasInvestorApp',
            'overdueInstallments', 'hasOverdue', 'overdueCount', 'overdueTotal', 'overdueLateFees', 'oldestOverdue',
            'overdueByLoan',
            'upcomingInstallments', 'hasUpcoming', 'upcomingCount', 'upcomingByLoan',
            'pendingInvestmentPayouts', 'hasPendingPayouts', 'pendingPayoutsCount', 'pendingPayoutsTotal', 'pendingPayoutsByOpportunity',
            'counterOfferLoans', 'acceptedLoans', 'rejectedLoans', 'hasLoanNotifications'
        ));
    }

    public function invest()
    {
        $rejectionReason = auth()->user()->investor_rejection_reason;

        return view('client.invest.index', compact('rejectionReason'));
    }

    public function sidebarStats()
    {
        $userId = Auth::id();

        return response()->json(
            Cache::remember("client_sidebar_stats_{$userId}", 30, function () use ($userId) {
                $user = Auth::user();

                return [
                    'counter_offers' => $user->loans()->where('status', 'counter_offered')->count(),
                    'pending_payouts' => $user->is_investor
                        ? \App\Models\InvestorPayout::where('investor_user_id', $userId)->where('status', 'pending')->count()
                        : 0,
                    'investment_rejected' => $user->is_investor && $user->investment_rejection_reason ? 1 : 0,
                ];
            })
        );
    }

    public function loanNotifications()
    {
        $user = Auth::user()->load('loans');
        $dismissedIds = $user->dismissed_loan_notifications ?? [];

        $counterOfferLoans = $user->loans->where('status', 'counter_offered')
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));
        $acceptedLoans = $user->loans->whereIn('status', ['approved', 'counter_accepted'])
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));
        $rejectedLoans = $user->loans->where('status', 'rejected')
            ->reject(fn($loan) => in_array((string)$loan->id, $dismissedIds));

        $notifications = [];

        foreach ($counterOfferLoans as $loan) {
            $notifications[] = [
                'type' => 'counter_offer',
                'id' => $loan->id,
                'code' => $loan->code,
                'amount' => $loan->amount,
                'currency' => $loan->currency,
                'url' => route('client.loans') . '?open_counter=' . $loan->id,
            ];
        }

        foreach ($acceptedLoans as $loan) {
            $notifications[] = [
                'type' => 'accepted',
                'id' => $loan->id,
                'code' => $loan->code,
                'amount' => $loan->amount,
                'currency' => $loan->currency,
                'url' => route('client.loans.history.show', $loan),
            ];
        }

        foreach ($rejectedLoans as $loan) {
            $notifications[] = [
                'type' => 'rejected',
                'id' => $loan->id,
                'code' => $loan->code,
                'amount' => $loan->amount,
                'currency' => $loan->currency,
                'url' => route('client.loans'),
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'pending_loans' => $user->loans->where('status', 'pending')->count(),
        ]);
    }

    public function dismissNotification(Request $request)
    {
        $loanId = $request->input('loan_id');
        if (!$loanId) return response()->json(['error' => 'loan_id requerido'], 422);

        $user = Auth::user();
        $dismissed = $user->dismissed_loan_notifications ?? [];

        if (!in_array((string) $loanId, $dismissed)) {
            $dismissed[] = (string) $loanId;
            $user->forceFill(['dismissed_loan_notifications' => $dismissed])->save();
        }

        return response()->json(['success' => true, 'remaining' => count($dismissed)]);
    }
}
