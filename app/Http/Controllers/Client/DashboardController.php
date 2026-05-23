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

        return view('client.dashboard', compact(
            'user', 'department', 'province', 'district',
            'activeLoans', 'pendingLoans', 'totalDebt', 'monthlyPayment',
            'hasProfile', 'pendingChange', 'rejectedChange',
            'isInvestor', 'hasInvestorApp',
            'overdueInstallments', 'hasOverdue', 'overdueCount', 'overdueTotal', 'overdueLateFees', 'oldestOverdue',
            'overdueByLoan'
        ));
    }

    public function invest()
    {
        $rejectedApplication = \App\Models\InvestorApplication::where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->latest()
            ->first();

        return view('client.invest.index', compact('rejectedApplication'));
    }
}
