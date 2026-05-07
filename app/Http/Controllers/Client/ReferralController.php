<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('referrals');

        if (!$user->referral_code) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
        }

        $referralLink = route('referral.handle', ['code' => $user->referral_code]);
        $totalReferrals = $user->referrals()->count();

        return view('client.referrals.index', compact('user', 'referralLink', 'totalReferrals'));
    }
}
