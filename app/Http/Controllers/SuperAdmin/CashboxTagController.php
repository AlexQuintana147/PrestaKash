<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CashboxTag;
use Illuminate\Http\Request;

class CashboxTagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        CashboxTag::create($request->only('name', 'color'));

        return back()->with('success', 'Etiqueta creada correctamente.');
    }

    public function update(Request $request, CashboxTag $cajas_etiqueta)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $cajas_etiqueta->update($request->only('name', 'color'));

        return back()->with('success', 'Etiqueta actualizada correctamente.');
    }

    public function destroy(CashboxTag $cajas_etiqueta)
    {
        $cajas_etiqueta->delete();

        return back()->with('success', 'Etiqueta eliminada correctamente.');
    }
}
