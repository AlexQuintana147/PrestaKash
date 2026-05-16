<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use Illuminate\Http\Request;

class CashboxController extends Controller
{
    public function index()
    {
        $cashboxes = Cashbox::with([
            'movements' => function ($q) {
                $q->with(['user', 'loan.user', 'installment.loan.user'])->latest()->limit(10);
            }
        ])->orderBy('id', 'desc')->get();

        return view('superadmin.cashboxes.index', compact('cashboxes'));
    }

    public function history(Request $request, Cashbox $cashbox)
    {
        $query = $cashbox->movements()->with(['user', 'loan.user', 'installment.loan.user'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        $movements = $query->paginate(10);

        return response()->json($movements);
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

    public function addMovement(Request $request, Cashbox $cashbox)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $amount = (float) $request->amount;

        if ($request->type === 'expense' && $cashbox->balance < $amount) {
            return back()->withErrors(['amount' => 'Saldo insuficiente en la caja.']);
        }

        \DB::transaction(function () use ($request, $cashbox, $amount) {
            $cashbox->movements()->create([
                'user_id' => auth()->id(),
                'type' => $request->type,
                'amount' => $amount,
                'description' => $request->description,
                'reference_type' => 'manual',
                'date' => now(),
            ]);

            if ($request->type === 'income') {
                $cashbox->increment('balance', $amount);
            } else {
                $cashbox->decrement('balance', $amount);
            }
        });

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_cashbox_id' => 'required|exists:cashboxes,id',
            'to_cashbox_id' => 'required|exists:cashboxes,id|different:from_cashbox_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $from = Cashbox::findOrFail($request->from_cashbox_id);
        $to = Cashbox::findOrFail($request->to_cashbox_id);

        if ($from->currency !== $to->currency) {
            return back()->withErrors(['to_cashbox_id' => 'Solo se puede desplazar dinero entre cajas de la misma moneda.']);
        }

        $amount = (float) $request->amount;

        if ($from->balance < $amount) {
            return back()->withErrors(['amount' => 'Saldo insuficiente en la caja origen.']);
        }

        \DB::transaction(function () use ($from, $to, $amount, $request) {
            $from->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => "Desplazamiento a {$to->name}: {$request->description}",
                'reference_type' => 'transfer',
                'reference_id' => $to->id,
                'date' => now(),
            ]);

            $to->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'income',
                'amount' => $amount,
                'description' => "Desplazamiento desde {$from->name}: {$request->description}",
                'reference_type' => 'transfer',
                'reference_id' => $from->id,
                'date' => now(),
            ]);

            $from->decrement('balance', $amount);
            $to->increment('balance', $amount);
        });

        return back()->with('success', "Desplazamiento de {$from->currency} {$amount} realizado correctamente.");
    }
}
