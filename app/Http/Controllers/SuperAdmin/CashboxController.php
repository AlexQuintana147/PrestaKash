<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use Illuminate\Http\Request;

class CashboxController extends Controller
{
    public function index()
    {
        $cashboxes = Cashbox::orderBy('created_at', 'desc')->get();
        return view('superadmin.cashboxes.index', compact('cashboxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'currency' => 'required|in:PEN,USD',
            'account_number' => 'nullable|string|max:255',
            'cci' => 'nullable|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        Cashbox::create($request->all());

        return back()->with('success', 'Caja creada correctamente.');
    }

    public function update(Request $request, Cashbox $cashbox)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'currency' => 'required|in:PEN,USD',
            'account_number' => 'nullable|string|max:255',
            'cci' => 'nullable|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $cashbox->update($request->all());

        return back()->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(Cashbox $cashbox)
    {
        $cashbox->delete();
        return back()->with('success', 'Caja eliminada correctamente.');
    }
}
