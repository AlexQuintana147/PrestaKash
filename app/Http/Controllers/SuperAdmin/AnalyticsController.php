<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\User;
use App\Models\InvestmentOpportunity;
use App\Models\Cashbox;
use App\Models\CashboxMovement;
use App\Models\InvestorPayout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $loanStats = $this->getLoanStats($startOfMonth, $startOfLastMonth, $endOfLastMonth);
        $investmentStats = $this->getInvestmentStats($startOfMonth);
        $cashboxStats = $this->getCashboxStats();
        $userStats = $this->getUserStats($startOfMonth);
        $collectionStats = $this->getCollectionStats($startOfMonth);
        $monthlyTrend = $this->getMonthlyTrend();
        $loanStatusDistribution = $this->getLoanStatusDistribution();
        $revenueByMonth = $this->getRevenueByMonth();
        $topDebtors = $this->getTopDebtors();
        $investmentPerformance = $this->getInvestmentPerformance();
        $paymentTimeliness = $this->getPaymentTimeliness();

        return view('superadmin.analytics.index', compact(
            'loanStats',
            'investmentStats',
            'cashboxStats',
            'userStats',
            'collectionStats',
            'monthlyTrend',
            'loanStatusDistribution',
            'revenueByMonth',
            'topDebtors',
            'investmentPerformance',
            'paymentTimeliness'
        ));
    }

    private function getLoanStats($startOfMonth, $startOfLastMonth, $endOfLastMonth)
    {
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', 'approved')->count();
        $pendingLoans = Loan::where('status', 'pending')->count();
        $defaultedLoans = Loan::where('in_default', true)->count();

        $totalDisbursed = Loan::where('status', 'approved')->sum('amount');
        $disbursedThisMonth = Loan::where('status', 'approved')
            ->whereBetween('disbursement_date', [$startOfMonth, now()])
            ->sum('amount');
        $disbursedLastMonth = Loan::where('status', 'approved')
            ->whereBetween('disbursement_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $disbursedChange = $disbursedLastMonth > 0
            ? round((($disbursedThisMonth - $disbursedLastMonth) / $disbursedLastMonth) * 100, 1)
            : ($disbursedThisMonth > 0 ? 100 : 0);

        $avgLoanAmount = Loan::where('status', 'approved')->avg('amount') ?? 0;

        $avgInterestRate = Loan::where('status', 'approved')->avg('interest_rate') ?? 0;

        return compact(
            'totalLoans',
            'activeLoans',
            'pendingLoans',
            'defaultedLoans',
            'totalDisbursed',
            'disbursedThisMonth',
            'disbursedChange',
            'avgLoanAmount',
            'avgInterestRate'
        );
    }

    private function getInvestmentStats($startOfMonth)
    {
        $totalOpportunities = InvestmentOpportunity::count();
        $openOpportunities = InvestmentOpportunity::where('status', 'open')->count();
        $totalTarget = InvestmentOpportunity::sum('target_amount');
        $totalRaised = InvestmentOpportunity::with('approvedInvestors')->get()->sum('raised_amount');

        $activeInvestors = DB::table('investor_opportunity')
            ->where('status', 'approved')
            ->distinct('user_id')
            ->count('user_id');

        $avgReturnRate = InvestmentOpportunity::where('status', 'open')->avg('monthly_return_rate') ?? 0;

        $payoutsThisMonth = InvestorPayout::where('status', 'approved')
            ->whereBetween('paid_at', [$startOfMonth, now()])
            ->sum('amount');

        $pendingPayouts = InvestorPayout::where('status', 'pending')->sum('amount');

        return compact(
            'totalOpportunities',
            'openOpportunities',
            'totalTarget',
            'totalRaised',
            'activeInvestors',
            'avgReturnRate',
            'payoutsThisMonth',
            'pendingPayouts'
        );
    }

    private function getCashboxStats()
    {
        $totalBalance = Cashbox::where('is_active', true)->sum('balance');
        $activeCashboxes = Cashbox::where('is_active', true)->count();

        $incomeThisMonth = CashboxMovement::where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $expenseThisMonth = CashboxMovement::where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $netFlow = $incomeThisMonth - $expenseThisMonth;

        return compact('totalBalance', 'activeCashboxes', 'incomeThisMonth', 'expenseThisMonth', 'netFlow');
    }

    private function getUserStats($startOfMonth)
    {
        $totalUsers = User::count();
        $newThisMonth = User::whereBetween('created_at', [$startOfMonth, now()])->count();
        $approvedUsers = User::where('profile_completed', true)->count();
        $pendingDocs = User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count();

        return compact('totalUsers', 'newThisMonth', 'approvedUsers', 'pendingDocs');
    }

    private function getCollectionStats($startOfMonth)
    {
        $totalCollected = LoanInstallment::where('status', 'approved')
            ->whereBetween('paid_at', [$startOfMonth, now()])
            ->sum('amount');

        $processingPayments = LoanInstallment::where('status', 'processing')->count();

        $overdueInstallments = LoanInstallment::where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->count();

        $totalLateFees = LoanInstallment::where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->get()
            ->sum('late_fee');

        $collectionRate = $this->calculateCollectionRate();

        return compact('totalCollected', 'processingPayments', 'overdueInstallments', 'totalLateFees', 'collectionRate');
    }

    private function calculateCollectionRate()
    {
        $dueThisMonth = LoanInstallment::whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->count();

        if ($dueThisMonth === 0) return 0;

        $paidThisMonth = LoanInstallment::whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->where('status', 'approved')
            ->count();

        return round(($paidThisMonth / $dueThisMonth) * 100, 1);
    }

    private function getMonthlyTrend()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $label = $date->translatedFormat('M Y');

            $loans = Loan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $disbursed = Loan::where('status', 'approved')
                ->whereYear('disbursement_date', $date->year)
                ->whereMonth('disbursement_date', $date->month)
                ->sum('amount');

            $collected = LoanInstallment::where('status', 'approved')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');

            $months->push([
                'label' => $label,
                'loans' => $loans,
                'disbursed' => (float) $disbursed,
                'collected' => (float) $collected,
            ]);
        }

        return $months;
    }

    private function getLoanStatusDistribution()
    {
        return Loan::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getRevenueByMonth()
    {
        $data = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $label = $date->translatedFormat('M Y');

            $interest = LoanInstallment::where('status', 'approved')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('interest');

            $lateFees = LoanInstallment::where('status', 'approved')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->where('charged_late_fee', '>', 0)
                ->sum('charged_late_fee');

            $techFees = Loan::where('status', 'approved')
                ->whereYear('disbursement_date', $date->year)
                ->whereMonth('disbursement_date', $date->month)
                ->sum('technology_fee');

            $data->push([
                'label' => $label,
                'interest' => (float) $interest,
                'lateFees' => (float) $lateFees,
                'techFees' => (float) $techFees,
            ]);
        }

        return $data;
    }

    private function getTopDebtors()
    {
        return Loan::with('user')
            ->where('status', 'approved')
            ->where('in_default', true)
            ->orWhere(function ($q) {
                $q->where('status', 'approved')
                    ->whereHas('installments', fn($q) => $q->where('status', '!=', 'approved')->where('due_date', '<', now()->startOfDay()));
            })
            ->withSum(['installments as overdue_amount' => fn($q) => $q->where('status', '!=', 'approved')->where('due_date', '<', now()->startOfDay())], 'amount')
            ->orderByDesc('overdue_amount')
            ->take(5)
            ->get()
            ->map(fn($loan) => [
                'name' => $loan->user->name ?? 'N/A',
                'code' => $loan->code,
                'amount' => (float) ($loan->overdue_amount ?? 0),
                'total' => (float) $loan->amount,
            ]);
    }

    private function getInvestmentPerformance()
    {
        return InvestmentOpportunity::with('approvedInvestors')
            ->where('status', '!=', 'draft')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($inv) => [
                'title' => $inv->title,
                'target' => (float) $inv->target_amount,
                'raised' => (float) $inv->raised_amount,
                'progress' => $inv->progress_percent,
                'returnRate' => (float) $inv->monthly_return_rate,
            ]);
    }

    private function getPaymentTimeliness()
    {
        $approved = LoanInstallment::where('status', 'approved')
            ->whereNotNull('paid_at')
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->get();

        $onTime = 0;
        $late = 0;
        $grace = 0;

        foreach ($approved as $inst) {
            $detail = $inst->payment_status_detail;
            if (!$detail) { $onTime++; continue; }
            match ($detail['type'] ?? '') {
                'before', 'same_day' => $onTime++,
                'grace' => $grace++,
                'late' => $late++,
                default => $onTime++,
            };
        }

        $total = $onTime + $late + $grace;
        if ($total === 0) $total = 1;

        return [
            'onTime' => $onTime,
            'late' => $late,
            'grace' => $grace,
            'onTimePercent' => round(($onTime / $total) * 100, 1),
            'latePercent' => round(($late / $total) * 100, 1),
            'gracePercent' => round(($grace / $total) * 100, 1),
        ];
    }
}
