<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;

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
}
