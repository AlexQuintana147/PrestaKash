<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('referrals');

        $referralLink = route('register', ['ref' => $user->referral_code]);
        $totalReferrals = $user->referrals()->count();

        return view('client.referrals.index', compact('user', 'referralLink', 'totalReferrals'));
    }
}
