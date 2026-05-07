<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function handle(Request $request, string $code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if ($referrer) {
            session(['referral_code' => $code]);
        }

        return redirect()->route('login')->with('info', 'Has llegado con un código de referido.');
    }
}
