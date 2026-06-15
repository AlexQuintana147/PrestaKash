<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\InvestorApplication;
use App\Models\InvestmentOpportunity;
use App\Models\InvestorPayout;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'     => User::whereHas('role', fn($q) => $q->where('name', 'usuario'))->count(),
            'pending'   => User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count(),
            'completed' => User::where('profile_completed', true)->whereHas('role', fn($q) => $q->where('name', 'usuario'))->count(),
            'today'     => User::whereHas('role', fn($q) => $q->where('name', 'usuario'))->whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::with(['documents'])
            ->whereHas('role', fn($q) => $q->where('name', 'usuario'))
            ->whereHas('documents', fn($q) => $q->where('status', 'pending'))
            ->latest()
            ->take(5)
            ->get();

        $upcomingCollections = LoanInstallment::with(['loan.user'])
            ->where('status', '!=', 'approved')
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('due_date', 'asc')
            ->take(15)
            ->get();

        $overdueInstallments = LoanInstallment::with(['loan.user'])
            ->where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->take(20)
            ->get();

        $overdueLoans = Loan::with(['user', 'installments'])
            ->where('status', 'approved')
            ->where('in_default', true)
            ->take(10)
            ->get();

        $upcomingPayouts = InvestorPayout::with(['investor', 'opportunity'])
            ->where('status', 'pending')
            ->orderBy('paid_at', 'asc')
            ->take(15)
            ->get();

        $fundedOpportunities = InvestmentOpportunity::with(['approvedInvestors'])
            ->whereIn('status', ['funded', 'closed'])
            ->latest('closed_at')
            ->take(5)
            ->get();

        $collectionStats = [
            'due_today' => LoanInstallment::where('status', '!=', 'approved')
                ->whereDate('due_date', today())
                ->count(),
            'due_today_amount' => LoanInstallment::where('status', '!=', 'approved')
                ->whereDate('due_date', today())
                ->sum('amount'),
            'overdue' => LoanInstallment::where('status', '!=', 'approved')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
            'overdue_amount' => LoanInstallment::where('status', '!=', 'approved')
                ->where('due_date', '<', now()->startOfDay())
                ->sum('amount'),
            'processing' => LoanInstallment::where('status', 'processing')->count(),
            'processing_amount' => LoanInstallment::where('status', 'processing')->sum('amount'),
        ];

        $payoutStats = [
            'pending_payouts' => InvestorPayout::where('status', 'pending')->count(),
            'pending_payouts_amount' => InvestorPayout::where('status', 'pending')->sum('amount'),
            'confirmed_payouts' => InvestorPayout::where('status', 'confirmed')->count(),
            'confirmed_payouts_amount' => InvestorPayout::where('status', 'confirmed')->sum('amount'),
        ];

        $loanStats = [
            'active' => Loan::where('status', 'approved')->count(),
            'active_amount' => Loan::where('status', 'approved')->sum('total_amount'),
            'pending' => Loan::where('status', 'pending')->count(),
            'in_default' => Loan::where('status', 'approved')->where('in_default', true)->count(),
        ];

        $cashboxes = Cashbox::where('is_active', true)->orderBy('name')->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'recentUsers',
            'upcomingCollections',
            'overdueInstallments',
            'overdueLoans',
            'upcomingPayouts',
            'fundedOpportunities',
            'collectionStats',
            'payoutStats',
            'loanStats',
            'cashboxes'
        ));
    }

    public function sidebarStats()
    {
        return response()->json(
            Cache::remember('sidebar_stats', 10, function () {
                return [
                    'pending_users' => User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count(),
                    'pending_profile_changes' => ProfileChangeRequest::where('status', 'pending')->count(),
                    'pending_investors' => InvestorApplication::where('status', 'pending')->count(),
                    'pending_loans' => Loan::where('status', 'pending')->count(),
                    'pending_investments' => DB::table('investor_opportunity')->where('status', 'pending')->count(),
                    'pending_vouchers' => DB::table('investor_opportunity')->where('payment_status', 'pending')->whereNotNull('voucher_path')->count(),
                    'pending_payments' => LoanInstallment::where('status', 'processing')->count(),
                ];
            })
        );
    }
}
