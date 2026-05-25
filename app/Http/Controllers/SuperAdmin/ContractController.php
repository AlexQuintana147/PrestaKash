<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractClause;
use App\Models\User;
use App\Models\Loan;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $slug = Str::slug($validated['name']);
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

        $slug = Str::slug($validated['name']);
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

    public function availableContracts(Request $request)
    {
        $contracts = Contract::where('is_active', true)->get(['id', 'name']);
        return response()->json($contracts);
    }

    public function previewClient(Contract $contract, Request $request)
    {
        $userId = $request->query('user_id');
        $type = $request->query('type');
        $relatedCode = $request->query('related_code');

        $user = User::findOrFail($userId);
        $contract->load('clauses');
        $allVars = Contract::getAllVariablesFlat();

        $data = $this->buildContractData($user, $type, $relatedCode);
        $data['contrato_nombre'] = $contract->name;
        $data['contrato_numero'] = 'CT-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);
        $data['fecha_firma'] = now()->format('d/m/Y');
        $data['ciudad'] = 'Lima';
        $data['fecha_actual'] = now()->format('d/m/Y');
        $data['dia_hoy'] = now()->format('d');
        $data['mes_hoy'] = Contract::monthToText((int)now()->format('m'));
        $data['anio_hoy'] = now()->format('Y');
        $data['dia_letras'] = Contract::numberToWords((int)now()->format('d'));
        $data['mes_letras'] = Contract::monthToText((int)now()->format('m'));
        $data['anio_letras'] = Contract::numberToWords((int)now()->format('Y'));
        $data['fecha_completa'] = now()->format('d') . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . now()->format('Y');
        $data['fecha_completa_letras'] = Contract::numberToWords((int)now()->format('d')) . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . Contract::numberToWords((int)now()->format('Y'));

        $renderedClauses = [];
        foreach ($contract->clauses as $clause) {
            if ($clause->box_type) {
                $boxHtml = $this->buildBoxContent($clause->box_type, $type, $relatedCode, $user);
                if ($boxHtml) {
                    $renderedClauses[] = [
                        'title' => null,
                        'content' => $boxHtml,
                        'is_box' => true,
                    ];
                }
                continue;
            }
            $renderedContent = $clause->content;
            foreach ($data as $key => $value) {
                if (isset($allVars[$key])) {
                    $renderedContent = str_replace('[' . $key . ']', e($value), $renderedContent);
                }
            }
            $renderedClauses[] = [
                'title' => $clause->title,
                'content' => $renderedContent,
                'is_box' => false,
            ];
        }

        return view('superadmin.contracts.preview-client', compact('contract', 'renderedClauses', 'user'));
    }

    public function downloadPDF(Contract $contract, Request $request)
    {
        $userId = $request->query('user_id');
        $type = $request->query('type');
        $relatedCode = $request->query('related_code');

        $user = User::findOrFail($userId);
        $contract->load('clauses');
        $allVars = Contract::getAllVariablesFlat();

        $data = $this->buildContractData($user, $type, $relatedCode);
        $data['contrato_nombre'] = $contract->name;
        $data['contrato_numero'] = 'CT-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);
        $data['fecha_firma'] = now()->format('d/m/Y');
        $data['ciudad'] = 'Lima';
        $data['fecha_actual'] = now()->format('d/m/Y');
        $data['dia_hoy'] = now()->format('d');
        $data['mes_hoy'] = Contract::monthToText((int)now()->format('m'));
        $data['anio_hoy'] = now()->format('Y');
        $data['dia_letras'] = Contract::numberToWords((int)now()->format('d'));
        $data['mes_letras'] = Contract::monthToText((int)now()->format('m'));
        $data['anio_letras'] = Contract::numberToWords((int)now()->format('Y'));
        $data['fecha_completa'] = now()->format('d') . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . now()->format('Y');
        $data['fecha_completa_letras'] = Contract::numberToWords((int)now()->format('d')) . ' de ' . Contract::monthToText((int)now()->format('m')) . ' de ' . Contract::numberToWords((int)now()->format('Y'));

        $renderedClauses = [];
        foreach ($contract->clauses as $clause) {
            if ($clause->box_type) {
                $boxHtml = $this->buildBoxContent($clause->box_type, $type, $relatedCode, $user);
                if ($boxHtml) {
                    $renderedClauses[] = [
                        'title' => null,
                        'content' => $boxHtml,
                        'is_box' => true,
                    ];
                }
                continue;
            }
            $renderedContent = $clause->content;
            foreach ($data as $key => $value) {
                if (isset($allVars[$key])) {
                    $renderedContent = str_replace('[' . $key . ']', e($value), $renderedContent);
                }
            }
            $renderedClauses[] = [
                'title' => $clause->title,
                'content' => $renderedContent,
                'is_box' => false,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('superadmin.contracts.pdf-client', [
            'contract' => $contract,
            'renderedClauses' => $renderedClauses,
            'user' => $user,
        ]);

        $filename = 'contrato_' . Str::slug($user->name) . '_' . $relatedCode . '.pdf';
        return $pdf->download($filename);
    }

    private function buildContractData(User $user, $type, $relatedCode)
    {
        $data = [
            'nombre_completo' => $user->name,
            'nombres' => $user->first_name,
            'apellidos' => $user->last_name,
            'dni' => $user->dni,
            'email' => $user->email,
            'telefono' => $user->phone,
            'direccion' => $user->address,
            'empresa_nombre' => 'PrestaKash Soluciones Financieras S.A.C.',
            'empresa_ruc' => '20601234567',
            'empresa_direccion' => 'Av. Central 456, Oficina 302, Cercado de Lima, Perú',
            'empresa_telefono' => '+51 (01) 111 222 333',
            'empresa_email' => 'contacto@prestakash.com',
            'empresa_representante' => 'Juan Carlos Pérez García',
            'empresa_representante_dni' => '45678901',
            'logo_empresa' => '',
            'firma_representante' => '[FIRMA DEL REPRESENTANTE]',
            'sello_empresa' => '[SELLO DE LA EMPRESA]',
            'firma_cliente' => '[FIRMA DEL CLIENTE]',
            'huella_cliente' => '[HUELLA DIGITAL]',
        ];

        if ($type === 'loan') {
            $loan = Loan::where('user_id', $user->id)->where('code', $relatedCode)->first();
            if ($loan) {
                $data['prestamo_monto'] = 'S/ ' . number_format($loan->amount, 2, ',', '.');
                $data['prestamo_codigo'] = $loan->code;
                $data['prestamo_plazo'] = $loan->months . ' meses';
                $data['prestamo_tasa_interes'] = $loan->interest_rate . '% anual';
                $data['prestamo_cuota'] = 'S/ ' . number_format($loan->monthly_payment, 2, ',', '.');
                $data['prestamo_fecha_desembolso'] = $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '';
                $data['prestamo_motivo'] = $loan->reason;
                $data['prestamo_total'] = 'S/ ' . number_format($loan->total_amount, 2, ',', '.');
                $data['moneda'] = 'Soles (PEN)';
            }
        } elseif ($type === 'investment') {
            $investment = InvestmentOpportunity::where('code', $relatedCode)->first();
            if ($investment) {
                $pivot = $investment->investors()->where('user_id', $user->id)->first()->pivot;
                $data['monto_invertido'] = 'S/ ' . number_format($pivot->amount, 2, ',', '.');
                $data['tasa_retorno_mensual'] = $investment->monthly_return_rate . '%';
                $data['plazo_meses'] = $investment->term_months . ' meses';
                $data['codigo_inversion'] = $investment->code;
                $data['oportunidad_titulo'] = $investment->title;
                $data['moneda'] = $investment->currency;
                $data['tipo_amortizacion'] = $pivot->amortization_type;
                $data['cuota_mensual'] = 'S/ ' . number_format($pivot->amount * ($investment->monthly_return_rate / 100), 2, ',', '.');
            }
        }

        return $data;
    }

    private function buildBoxContent(string $boxType, string $type, ?string $relatedCode, User $user): string
    {
        $boxDef = Contract::getBoxTypes()[$boxType] ?? null;
        if (!$boxDef) return '';

        $label = $boxDef['label'];
        $c = $boxDef['color'];
        $bg = $boxDef['bgLight'];
        $txt = $boxDef['textLight'];
        $border = $boxDef['borderLight'];

        $badge = '<span style="display:inline-block;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;padding:2px 8px;border-radius:12px;background:'.$bg.';color:'.$txt.';border:1px solid '.$border.'">Automático</span>';

        $header = '<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">'.
            '<div style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:'.$bg.';color:'.$c.';font-size:16px;font-weight:700;">&#9632;</div>'.
            '<div><span style="font-size:11.5px;font-weight:700;color:#374151">'.e($label).'</span> '.$badge.'</div></div>';

        if ($boxType === 'datos_cliente') {
            $html = '<div style="border:2px dashed '.$border.';border-radius:10px;padding:18px 20px;margin-bottom:22px;background:linear-gradient(135deg,'.$bg.'08,'.$bg.'02);">'.$header;
            $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;color:#374151">';
            $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600;width:35%">Nombre completo:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($user->name).'</td></tr>';
            $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">DNI:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($user->dni).'</td></tr>';
            $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Correo:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($user->email).'</td></tr>';
            $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Teléfono:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($user->phone).'</td></tr>';
            $html .= '<tr><td style="padding:6px 0;font-weight:600">Dirección:</td><td style="padding:6px 0">'.e($user->address).'</td></tr>';
            $html .= '</table></div>';
            return $html;
        }

        if ($type === 'loan' && $relatedCode) {
            $loan = Loan::with('installments')->where('user_id', $user->id)->where('code', $relatedCode)->first();
            if (!$loan) return '';

            if ($boxType === 'cronograma') {
                $html = '<div style="border:2px dashed '.$border.';border-radius:10px;padding:18px 20px;margin-bottom:22px;background:linear-gradient(135deg,'.$bg.'08,'.$bg.'02);">'.$header;
                $html .= '<table style="width:100%;border-collapse:collapse;font-size:11px">';
                $html .= '<thead><tr style="background:#f9fafb"><th style="padding:8px;border:1px solid #e5e7eb;text-align:left">N°</th><th style="padding:8px;border:1px solid #e5e7eb;text-align:left">Fecha vencimiento</th><th style="padding:8px;border:1px solid #e5e7eb;text-align:right">Capital</th><th style="padding:8px;border:1px solid #e5e7eb;text-align:right">Interés</th><th style="padding:8px;border:1px solid #e5e7eb;text-align:right">Total</th><th style="padding:8px;border:1px solid #e5e7eb;text-align:center">Estado</th></tr></thead><tbody>';
                foreach ($loan->installments as $inst) {
                    $statusLabel = $inst->status === 'approved' ? 'Pagada' : 'Pendiente';
                    $statusColor = $inst->status === 'approved' ? '#059669' : '#d97706';
                    $html .= '<tr><td style="padding:8px;border:1px solid #e5e7eb">'.$inst->installment_number.'</td><td style="padding:8px;border:1px solid #e5e7eb">'.$inst->due_date->format('d/m/Y').'</td><td style="padding:8px;border:1px solid #e5e7eb;text-align:right">S/ '.number_format($inst->capital, 2).'</td><td style="padding:8px;border:1px solid #e5e7eb;text-align:right">S/ '.number_format($inst->interest, 2).'</td><td style="padding:8px;border:1px solid #e5e7eb;text-align:right;font-weight:600">S/ '.number_format($inst->amount, 2).'</td><td style="padding:8px;border:1px solid #e5e7eb;text-align:center;color:'.$statusColor.'">'.$statusLabel.'</td></tr>';
                }
                $html .= '</tbody></table></div>';
                return $html;
            }

            if ($boxType === 'monto_debe') {
                $totalPending = $loan->installments->where('status', '!=', 'approved')->sum('amount');
                $totalCapital = $loan->installments->where('status', '!=', 'approved')->sum('capital');
                $totalInterest = $loan->installments->where('status', '!=', 'approved')->sum('interest');
                $totalLateFee = $loan->installments->where('status', '!=', 'approved')->sum(fn($i) => $i->late_fee);
                $html = '<div style="border:2px dashed '.$border.';border-radius:10px;padding:18px 20px;margin-bottom:22px;background:linear-gradient(135deg,'.$bg.'08,'.$bg.'02);">'.$header;
                $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;color:#374151">';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600;width:45%">Total pendiente:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:700">S/ '.number_format($totalPending + $totalLateFee, 2).'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Capital:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right">S/ '.number_format($totalCapital, 2).'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Intereses:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right">S/ '.number_format($totalInterest, 2).'</td></tr>';
                if ($totalLateFee > 0) {
                    $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Mora:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;color:#dc2626">S/ '.number_format($totalLateFee, 2).'</td></tr>';
                }
                $html .= '<tr><td style="padding:6px 0;font-weight:600">Cuota mensual:</td><td style="padding:6px 0;text-align:right">S/ '.number_format($loan->monthly_payment, 2).'</td></tr>';
                $html .= '</table></div>';
                return $html;
            }

            if ($boxType === 'condiciones') {
                $html = '<div style="border:2px dashed '.$border.';border-radius:10px;padding:18px 20px;margin-bottom:22px;background:linear-gradient(135deg,'.$bg.'08,'.$bg.'02);">'.$header;
                $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;color:#374151">';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600;width:45%">Tasa de interés anual:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->interest_rate).'%</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Plazo:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->months).' meses</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Frecuencia de pago:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->payment_frequency ?? 'Mensual').'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Sistema de amortización:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->amortization_system ?? 'Francés').'</td></tr>';
                if ($loan->late_fee_enabled) {
                    $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Tasa de mora:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->late_fee_rate).'%</td></tr>';
                    $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Días de gracia:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($loan->grace_days).' días</td></tr>';
                }
                $html .= '<tr><td style="padding:6px 0;font-weight:600">Moneda:</td><td style="padding:6px 0">'.e($loan->currency ?? 'PEN').'</td></tr>';
                $html .= '</table></div>';
                return $html;
            }
        }

        if ($type === 'investment' && $relatedCode) {
            $investment = InvestmentOpportunity::where('code', $relatedCode)->first();
            if (!$investment) return '';
            $pivot = $investment->investors()->where('user_id', $user->id)->first()->pivot ?? null;
            if (!$pivot) return '';

            if ($boxType === 'resumen_inversion') {
                $monthlyReturn = $pivot->amount * ($investment->monthly_return_rate / 100);
                $totalReturn = $monthlyReturn * $investment->term_months;
                $html = '<div style="border:2px dashed '.$border.';border-radius:10px;padding:18px 20px;margin-bottom:22px;background:linear-gradient(135deg,'.$bg.'08,'.$bg.'02);">'.$header;
                $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;color:#374151">';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600;width:45%">Oportunidad:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($investment->title).'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Monto invertido:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:700">S/ '.number_format($pivot->amount, 2).'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Tasa retorno mensual:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($investment->monthly_return_rate).'%</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Plazo:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">'.e($investment->term_months).' meses</td></tr>';
                $html .= '<tr><td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:600">Retorno mensual:</td><td style="padding:6px 0;border-bottom:1px solid #f3f4f6">S/ '.number_format($monthlyReturn, 2).'</td></tr>';
                $html .= '<tr><td style="padding:6px 0;font-weight:600">Retorno total estimado:</td><td style="padding:6px 0;font-weight:700;color:#059669">S/ '.number_format($totalReturn, 2).'</td></tr>';
                $html .= '</table></div>';
                return $html;
            }
        }

        return '';
    }
}
