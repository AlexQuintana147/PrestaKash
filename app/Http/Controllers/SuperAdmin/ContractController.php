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
            'name' => 'required|string|max:255|unique:contracts,slug',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'clauses' => 'required|array|min:1',
            'clauses.*.title' => 'nullable|string|max:255',
            'clauses.*.content' => 'nullable|string',
            'clauses.*.box_type' => 'nullable|string|max:50',
        ]);

        $slug = \Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Contract::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $contract = Contract::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($validated['clauses'] as $index => $clause) {
            ContractClause::create([
                'contract_id' => $contract->id,
                'box_type' => $clause['box_type'] ?? null,
                'title' => $clause['title'] ?? '',
                'content' => $clause['content'] ?? '',
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
            'is_active' => 'sometimes|boolean',
            'clauses' => 'required|array|min:1',
            'clauses.*.id' => 'nullable|integer|exists:contract_clauses,id',
            'clauses.*.title' => 'nullable|string|max:255',
            'clauses.*.content' => 'nullable|string',
            'clauses.*.box_type' => 'nullable|string|max:50',
        ]);

        $slug = \Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Contract::where('slug', $slug)->where('id', '!=', $contract->id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $contract->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $existingIds = [];
        foreach ($validated['clauses'] as $index => $clause) {
            if (!empty($clause['id'])) {
                $clauseModel = $contract->clauses()->where('id', $clause['id'])->first();
                if (!$clauseModel) {
                    continue;
                }
                $clauseModel->update([
                    'box_type' => $clause['box_type'] ?? null,
                    'title' => $clause['title'] ?? '',
                    'content' => $clause['content'] ?? '',
                    'order' => $index,
                ]);
                $existingIds[] = $clause['id'];
            } else {
                $newClause = ContractClause::create([
                    'contract_id' => $contract->id,
                    'box_type' => $clause['box_type'] ?? null,
                    'title' => $clause['title'] ?? '',
                    'content' => $clause['content'] ?? '',
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
        $allVars = Contract::getAllVariablesFlat();

        $sampleData = [
            'nombre_completo' => 'Pepe Luna Castillo',
            'nombres' => 'Pepe',
            'apellidos' => 'Luna Castillo',
            'dni' => '12345678',
            'ruc' => '20123456789',
            'razon_social' => 'Luna Castillo E.I.R.L.',
            'email' => 'pepe@correo.com',
            'telefono' => '+51 999 888 777',
            'direccion' => 'Av. Principal 123, Urbanización San Isidro, Lima',
            'estado_civil' => 'Casado',
            'ocupacion' => 'Ingeniero de Sistemas',
            'lugar_trabajo' => 'Tecnologías del Perú S.A.C.',
            'monto_invertido' => 'S/ 10,000.00',
            'monto_deseado' => 'S/ 15,000.00',
            'tasa_retorno_mensual' => '3.5%',
            'plazo_meses' => '12',
            'cuota_mensual' => 'S/ 350.00',
            'fecha_inicio' => '01 de junio de 2026',
            'fecha_fin' => '01 de junio de 2027',
            'codigo_inversion' => 'OPP-2026-A001',
            'oportunidad_titulo' => 'Fondo de Inversión Comercial Q3-2026',
            'moneda' => 'Soles (PEN)',
            'tipo_amortizacion' => 'Capital e intereses mensuales',
            'prestamo_monto' => 'S/ 5,000.00',
            'prestamo_codigo' => 'PRE-2026-0042',
            'prestamo_plazo' => '6 meses',
            'prestamo_tasa_interes' => '8.5% anual',
            'prestamo_cuota' => 'S/ 867.20',
            'prestamo_fecha_desembolso' => '15 de marzo de 2026',
            'prestamo_motivo' => 'Capital de trabajo para expansión comercial',
            'prestamo_total' => 'S/ 5,203.20',
            'total_pagado' => 'S/ 4,336.80',
            'pagos_realizados' => '5 pagos',
            'pagos_pendientes' => '1 pago',
            'proximo_pago_fecha' => '15 de septiembre de 2026',
            'proximo_pago_monto' => 'S/ 867.20',
            'ultimo_pago_fecha' => '15 de agosto de 2026',
            'ultimo_pago_monto' => 'S/ 867.20',
            'tasa_mora' => '5% mensual',
            'dias_gracia' => '3 días',
            'mora_diaria' => 'S/ 1.45',
            'dias_mora' => '0 días',
            'interes_mora_acumulado' => 'S/ 0.00',
            'penalidad_actual' => 'S/ 0.00',
            'empresa_nombre' => 'PrestaKash Soluciones Financieras S.A.C.',
            'empresa_ruc' => '20601234567',
            'empresa_direccion' => 'Av. Central 456, Oficina 302, Cercado de Lima, Perú',
            'empresa_telefono' => '+51 (01) 111 222 333',
            'empresa_email' => 'contacto@prestakash.com',
            'empresa_representante' => 'Juan Carlos Pérez García',
            'empresa_representante_dni' => '45678901',
            'logo_empresa' => '[LOGO DE LA EMPRESA]',
            'firma_representante' => '[FIRMA DEL REPRESENTANTE]',
            'sello_empresa' => '[SELLO DE LA EMPRESA]',
            'contrato_nombre' => $contract->name,
            'contrato_numero' => 'CT-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
            'fecha_firma' => now()->format('d/m/Y'),
            'ciudad' => 'Lima',
            'firma_cliente' => '[FIRMA DEL CLIENTE]',
            'huella_cliente' => '[HUELLA DIGITAL]',
            'fecha_actual' => now()->format('d/m/Y'),
            'dia_hoy' => now()->format('d'),
            'mes_hoy' => Contract::monthToText((int)now()->format('m')),
            'anio_hoy' => now()->format('Y'),
            'dia_letras' => Contract::numberToWords((int)now()->format('d')),
            'mes_letras' => Contract::monthToText((int)now()->format('m')),
            'anio_letras' => Contract::numberToWords((int)now()->format('Y')),
            'fecha_completa' => now()->format('d') . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . now()->format('Y'),
            'fecha_completa_letras' => Contract::numberToWords((int)now()->format('d')) . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . Contract::numberToWords((int)now()->format('Y')),
        ];

        $renderedClauses = [];
        foreach ($contract->clauses as $clause) {
            $renderedContent = $clause->content;
            foreach ($sampleData as $key => $value) {
                if (isset($allVars[$key])) {
                    $v = $allVars[$key];
                    $colorStyle = "color:" . $v['color'] . ";background:" . ($v['bg_light']) . ";padding:1px 6px;border-radius:4px;font-size:inherit;";
                    $darkColorStyle = "color:" . ($v['text_dark']) . ";background:" . ($v['bg_dark']) . ";padding:1px 6px;border-radius:4px;font-size:inherit;";
                    $renderedContent = str_replace(
                        '[' . $key . ']',
                        '<span style="' . $colorStyle . '" class="dark:inline" data-dark-style="' . htmlspecialchars($darkColorStyle) . '">' . e($value) . '</span>',
                        $renderedContent
                    );
                }
            }
            $renderedClauses[] = [
                'title' => $clause->title,
                'content' => $renderedContent,
            ];
        }

        return response()->json(['clauses' => $renderedClauses]);
    }
}
