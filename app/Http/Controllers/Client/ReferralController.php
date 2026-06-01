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
            ->whereIn('status', ['approved', 'pending'])
            ->sum('amount');

        $bankAccounts = $user->bankAccounts;
        $latestWithdrawal = \App\Models\ReferralWithdrawal::where('user_id', $user->id)->latest()->first();

        return view('client.referrals.index', compact('user', 'referralLink', 'totalReferrals', 'referrals', 'filter', 'counts', 'totalEarnings', 'bankAccounts', 'latestWithdrawal'));
    }

    public function withdraw(Request $request)
    {
        $user = Auth::user();

        $totalEarnings = \App\Models\ReferralPayout::whereIn('referral_id', $user->referralRecords()->pluck('id'))
            ->whereIn('status', ['approved', 'pending'])
            ->sum('amount');

        if ($totalEarnings < 50) {
            return back()->withErrors(['withdraw' => 'Aún no has alcanzado el monto mínimo de S/ 50 para retirar.']);
        }

        $data = $request->validate([
            'account_id' => 'required|exists:bank_accounts,id',
        ]);

        $bankAccount = $user->bankAccounts()->findOrFail($data['account_id']);

        // Create withdrawal record
        \App\Models\ReferralWithdrawal::create([
            'user_id' => $user->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => $totalEarnings,
            'status' => 'pending',
        ]);

        // Mark payouts as withdrawn
        \App\Models\ReferralPayout::whereIn('referral_id', $user->referralRecords()->pluck('id'))
            ->whereIn('status', ['approved', 'pending'])
            ->update(['status' => 'withdrawn']);

        return back()->with('success', 'Solicitud de retiro enviada. El equipo de PrestaKash procesará tu pago en las próximas 24 horas hábiles.');
    }

    public function confirmVoucher(Request $request, \App\Models\ReferralWithdrawal $withdrawal)
    {
        $user = Auth::user();

        if ($withdrawal->user_id !== $user->id) {
            abort(403);
        }

        if ($withdrawal->voucher_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($withdrawal->voucher_path);
        }

        $withdrawal->update([
            'status' => 'confirmed',
            'voucher_path' => null,
        ]);

        return back()->with('success', 'Comprobante confirmado. El archivo ha sido eliminado del sistema.');
    }
}
