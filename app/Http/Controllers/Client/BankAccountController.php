<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->bankAccounts;
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'alias'          => 'nullable|string|max:50',
            'bank_name'      => 'required|string',
            'account_type'   => 'required|string|in:ahorros,corriente',
            'account_number' => 'required|string|max:14',
            'cci'            => 'nullable|string|max:20',
        ]);

        $account = Auth::user()->bankAccounts()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cuenta agregada correctamente.',
            'account' => $account
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $request->validate([
            'alias'          => 'nullable|string|max:50',
            'bank_name'      => 'required|string',
            'account_type'   => 'required|string|in:ahorros,corriente',
            'account_number' => 'required|string|max:14',
            'cci'            => 'nullable|string|max:20',
        ]);

        $bankAccount->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cuenta actualizada correctamente.',
            'account' => $bankAccount
        ]);
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $bankAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta eliminada correctamente.'
        ]);
    }
}
