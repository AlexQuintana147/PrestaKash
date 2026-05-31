<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CashboxTag;
use Illuminate\Http\Request;

class CashboxTagController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            'color' => 'required|string|max:7',
        ]);

        $tag = CashboxTag::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'tag' => ['id' => $tag->id, 'name' => $tag->name, 'color' => $tag->color],
            ]);
        }

        return back()->with('success', 'Etiqueta creada correctamente.');
    }

    public function update(Request $request, CashboxTag $cajas_etiqueta)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            'color' => 'required|string|max:7',
        ]);

        $cajas_etiqueta->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'tag' => ['id' => $cajas_etiqueta->id, 'name' => $cajas_etiqueta->name, 'color' => $cajas_etiqueta->color],
            ]);
        }

        return back()->with('success', 'Etiqueta actualizada correctamente.');
    }

    public function destroy(CashboxTag $cajas_etiqueta)
    {
        $cajas_etiqueta->delete();

        return back()->with('success', 'Etiqueta eliminada correctamente.');
    }
}
