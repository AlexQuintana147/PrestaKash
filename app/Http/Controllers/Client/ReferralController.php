<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $user = Auth::user();

        if (!$user->referral_code) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
        }

        $referralLink = route('referral.handle', ['code' => $user->referral_code]);

        $referralsQuery = $user->referrals()->with('documents');

        if ($filter === 'approved') {
            $referralsQuery->whereHas('documents', fn($q) => $q->where('status', 'approved'));
        } elseif ($filter === 'pending') {
            $referralsQuery->where(function ($q) {
                $q->whereDoesntHave('documents')
                  ->orWhereHas('documents', fn($q) => $q->where('status', 'pending'));
            });
        } elseif ($filter === 'rejected') {
            $referralsQuery->whereHas('documents', fn($q) => $q->where('status', 'rejected'));
        }

        $referrals = $referralsQuery->get();
        $totalReferrals = $user->referrals()->count();

        $counts = [
            'all' => $totalReferrals,
            'approved' => $user->referrals()->whereHas('documents', fn($q) => $q->where('status', 'approved'))->count(),
            'pending' => $user->referrals()->where(function ($q) {
                $q->whereDoesntHave('documents')
                  ->orWhereHas('documents', fn($q) => $q->where('status', 'pending'));
            })->count(),
            'rejected' => $user->referrals()->whereHas('documents', fn($q) => $q->where('status', 'rejected'))->count(),
        ];

        return view('client.referrals.index', compact('user', 'referralLink', 'totalReferrals', 'referrals', 'filter', 'counts'));
    }
}
