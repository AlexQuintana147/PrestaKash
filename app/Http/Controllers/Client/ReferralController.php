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

        $referralsQuery = $user->referralRecords()->with(['referred.documents', 'payouts.loan']);

        if ($filter === 'approved') {
            $referralsQuery->whereHas('referred.documents', fn($q) => $q->where('status', 'approved'));
        } elseif ($filter === 'pending') {
            $referralsQuery->where(function ($q) {
                $q->whereDoesntHave('referred.documents')
                  ->orWhereHas('referred.documents', fn($q) => $q->where('status', 'pending'));
            });
        } elseif ($filter === 'rejected') {
            $referralsQuery->whereHas('referred.documents', fn($q) => $q->where('status', 'rejected'));
        }

        $referrals = $referralsQuery->paginate(10)->withQueryString();
        $totalReferrals = $user->referralRecords()->count();

        $counts = [
            'all' => $totalReferrals,
            'approved' => $user->referralRecords()->whereHas('referred.documents', fn($q) => $q->where('status', 'approved'))->count(),
            'pending' => $user->referralRecords()->where(function ($q) {
                $q->whereDoesntHave('referred.documents')
                  ->orWhereHas('referred.documents', fn($q) => $q->where('status', 'pending'));
            })->count(),
            'rejected' => $user->referralRecords()->whereHas('referred.documents', fn($q) => $q->where('status', 'rejected'))->count(),
        ];

        $totalEarnings = \App\Models\ReferralPayout::whereIn('referral_id', $user->referralRecords()->pluck('id'))
            ->where('status', 'paid')
            ->sum('amount');

        return view('client.referrals.index', compact('user', 'referralLink', 'totalReferrals', 'referrals', 'filter', 'counts', 'totalEarnings'));
    }
}
