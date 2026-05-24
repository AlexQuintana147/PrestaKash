<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractClause;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::withCount('clauses')->orderBy('created_at', 'desc')->get();
        return view('superadmin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $variables = Contract::getAvailableVariables();
        return view('superadmin.contracts.create', compact('variables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'clauses' => 'required|array|min:1',
            'clauses.*.title' => 'required|string|max:255',
            'clauses.*.content' => 'required|string',
        ]);

        $contract = Contract::create([
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        foreach ($validated['clauses'] as $index => $clause) {
            ContractClause::create([
                'contract_id' => $contract->id,
                'title' => $clause['title'],
                'content' => $clause['content'],
                'order' => $index,
            ]);
        }

        return redirect()->route('superadmin.contracts.edit', $contract)
            ->with('success', 'Contrato creado correctamente');
    }

    public function edit(Contract $contract)
    {
        $contract->load('clauses');
        $variables = Contract::getAvailableVariables();
        return view('superadmin.contracts.edit', compact('contract', 'variables'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'clauses' => 'required|array|min:1',
            'clauses.*.id' => 'nullable|exists:contract_clauses,id',
            'clauses.*.title' => 'required|string|max:255',
            'clauses.*.content' => 'required|string',
        ]);

        $contract->update([
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $existingIds = [];
        foreach ($validated['clauses'] as $index => $clause) {
            if (!empty($clause['id'])) {
                ContractClause::where('id', $clause['id'])->update([
                    'title' => $clause['title'],
                    'content' => $clause['content'],
                    'order' => $index,
                ]);
                $existingIds[] = $clause['id'];
            } else {
                $newClause = ContractClause::create([
                    'contract_id' => $contract->id,
                    'title' => $clause['title'],
                    'content' => $clause['content'],
                    'order' => $index,
                ]);
                $existingIds[] = $newClause->id;
            }
        }

        $contract->clauses()->whereNotIn('id', $existingIds)->delete();

        return back()->with('success', 'Contrato actualizado');
    }

    public function preview(Contract $contract, Request $request)
    {
        $contract->load('clauses');

        $sampleData = [
            'nombre_completo' => 'Pepe Luna Castillo',
            'nombres' => 'Pepe',
            'apellidos' => 'Luna Castillo',
            'dni' => '12345678',
            'ruc' => '20123456789',
            'email' => 'pepe@correo.com',
            'telefono' => '+51 999 888 777',
            'direccion' => 'Av. Principal 123, Lima',
            'fecha_nacimiento' => '15/03/1990',
            'estado_civil' => 'Casado',
            'ocupacion' => 'Ingeniero',
            'monto_invertido' => 'S/ 10,000.00',
            'tasa_interes' => '12%',
            'plazo_meses' => '12',
            'cuota_mensual' => 'S/ 933.33',
            'fecha_inicio' => '01/06/2026',
            'fecha_fin' => '01/06/2027',
            'codigo_inversion' => 'INV-2026-001',
            'oportunidad_titulo' => 'Oportunidad de Inversión A',
            'total_pagado' => 'S/ 4,666.65',
            'pagos_realizados' => '5',
            'pagos_pendientes' => '7',
            'proximo_pago_fecha' => '01/11/2026',
            'proximo_pago_monto' => 'S/ 933.33',
            'ultima_fecha_pago' => '01/10/2026',
            'tasa_mora' => '5%',
            'dias_mora' => '0',
            'interes_mora_acumulado' => 'S/ 0.00',
            'penalidad_mora' => 'S/ 0.00',
            'empresa_nombre' => 'PrestaKash S.A.C.',
            'empresa_ruc' => '20601234567',
            'empresa_direccion' => 'Av. Central 456, Lima, Perú',
            'empresa_telefono' => '+51 111 222 333',
            'empresa_email' => 'contacto@prestakash.com',
            'empresa_representante' => 'Juan Pérez García',
            'logo_empresa' => '[LOGO]',
            'firma_empresa' => '[FIRMA]',
            'sello_empresa' => '[SELLO]',
            'contrato_nombre' => $contract->name,
            'fecha_contrato' => now()->format('d/m/Y'),
            'numero_contrato' => 'CT-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
            'ciudad' => 'Lima',
            'firma_cliente' => '[FIRMA CLIENTE]',
            'huella_cliente' => '[HUELLA]',
            'fecha_actual' => now()->format('d/m/Y'),
            'dia_actual' => now()->format('d'),
            'mes_actual' => now()->formatLocalized('%B'),
            'anio_actual' => now()->format('Y'),
            'dia_escrito' => $this->numberToWords(now()->format('d')),
            'mes_escrito' => 'mayo',
            'anio_escrito' => 'dos mil veintiséis',
            'fecha_completa' => now()->format('d \\d\\e F \\d\\e Y'),
            'fecha_letras' => 'veintitrés de mayo de dos mil veintiséis',
        ];

        $renderedClauses = [];
        foreach ($contract->clauses as $clause) {
            $renderedContent = $clause->content;
            foreach ($sampleData as $key => $value) {
                $renderedContent = str_replace('[' . $key . ']', '<strong class="text-blue-600 dark:text-blue-400">' . e($value) . '</strong>', $renderedContent);
            }
            $renderedClauses[] = [
                'title' => $clause->title,
                'content' => $renderedContent,
            ];
        }

        return response()->json(['clauses' => $renderedClauses]);
    }

    private function numberToWords($num): string
    {
        $words = [
            1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro', 5 => 'cinco',
            6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve', 10 => 'diez',
            11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
            16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve',
            20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés',
            24 => 'veinticuatro', 25 => 'veinticinco', 26 => 'veintiséis', 27 => 'veintisiete',
            28 => 'veintiocho', 29 => 'veintinueve', 30 => 'treinta', 31 => 'treinta y uno',
        ];
        return $words[(int)$num] ?? $num;
    }
}
