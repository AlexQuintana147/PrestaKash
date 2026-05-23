<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\InvestorApplication;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'     => User::count(),
            'pending'   => User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count(),
            'completed' => User::where('profile_completed', true)->count(),
            'today'     => User::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::with(['role', 'documents'])
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentUsers'));
    }

    public function sidebarStats()
    {
        return response()->json([
            'pending_users' => User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count(),
            'pending_profile_changes' => ProfileChangeRequest::where('status', 'pending')->count(),
            'pending_investors' => InvestorApplication::where('status', 'pending')->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'pending_investments' => DB::table('investor_opportunity')->where('status', 'pending')->count(),
            'pending_vouchers' => DB::table('investor_opportunity')->where('payment_status', 'pending')->whereNotNull('voucher_path')->count(),
            'pending_payments' => LoanInstallment::where('status', 'processing')->count(),
        ]);
    }
}
