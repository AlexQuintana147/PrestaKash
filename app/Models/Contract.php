<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function clauses(): HasMany
    {
        return $this->hasMany(ContractClause::class)->orderBy('order');
    }

    public static function getAvailableVariables(): array
    {
        return [
            'persona' => [
                'label' => 'Datos Personales',
                'icon' => 'user',
                'color' => '#6366f1',
                'bgLight' => '#eef2ff',
                'bgDark' => '#312e81',
                'textLight' => '#4338ca',
                'textDark' => '#a5b4fc',
                'borderLight' => '#c7d2fe',
                'borderDark' => '#3730a3',
                'variables' => [
                    'nombre_completo' => ['desc' => 'Nombre completo del cliente o inversionista', 'source' => 'User.name'],
                    'nombres' => ['desc' => 'Primer nombre(s) de la persona', 'source' => 'User.first_name'],
                    'apellidos' => ['desc' => 'Apellidos completos de la persona', 'source' => 'User.last_name'],
                    'dni' => ['desc' => 'Número de DNI del cliente', 'source' => 'User.dni'],
                    'ruc' => ['desc' => 'RUC de la empresa (si aplica)', 'source' => 'User.ruc'],
                    'razon_social' => ['desc' => 'Razón social de la empresa (si es persona jurídica)', 'source' => 'User.business_name'],
                    'email' => ['desc' => 'Correo electrónico registrado', 'source' => 'User.email'],
                    'telefono' => ['desc' => 'Número de teléfono', 'source' => 'User.phone'],
                    'direccion' => ['desc' => 'Dirección completa del domicilio', 'source' => 'User.address'],
                    'estado_civil' => ['desc' => 'Estado civil (soltero, casado, etc.)', 'source' => 'ClientProfile.marital_status'],
                    'ocupacion' => ['desc' => 'Profesión u ocupación laboral', 'source' => 'InvestorApplication.occupation / ClientProfile.job_title'],
                    'lugar_trabajo' => ['desc' => 'Nombre de la empresa donde trabaja', 'source' => 'ClientProfile.workplace'],
                ],
            ],
            'inversionista' => [
                'label' => 'Datos de Inversión',
                'icon' => 'wallet',
                'color' => '#059669',
                'bgLight' => '#ecfdf5',
                'bgDark' => '#064e3b',
                'textLight' => '#047857',
                'textDark' => '#6ee7b7',
                'borderLight' => '#a7f3d0',
                'borderDark' => '#065f46',
                'variables' => [
                    'monto_invertido' => ['desc' => 'Monto total que el inversionista ha invertido', 'source' => 'InvestorOpportunity.pivot.amount'],
                    'monto_deseado' => ['desc' => 'Monto que el inversionista desea invertir', 'source' => 'InvestorApplication.intended_amount'],
                    'tasa_retorno_mensual' => ['desc' => 'Porcentaje de retorno mensual de la inversión', 'source' => 'InvestmentOpportunity.monthly_return_rate'],
                    'plazo_meses' => ['desc' => 'Duración total de la inversión en meses', 'source' => 'InvestmentOpportunity.term_months'],
                    'cuota_mensual' => ['desc' => 'Cuota mensual que recibirá el inversionista', 'source' => 'Calculado: monto_invertido * tasa_retorno_mensual / 100'],
                    'fecha_inicio' => ['desc' => 'Fecha en que comenzó la inversión', 'source' => 'Pivot.approved_at'],
                    'fecha_fin' => ['desc' => 'Fecha estimada de finalización de la inversión', 'source' => 'Calculado: fecha_inicio + plazo_meses'],
                    'codigo_inversion' => ['desc' => 'Código único asignado a esta inversión', 'source' => 'InvestmentOpportunity.code'],
                    'oportunidad_titulo' => ['desc' => 'Nombre de la oportunidad de inversión', 'source' => 'InvestmentOpportunity.title'],
                    'moneda' => ['desc' => 'Moneda de la inversión (soles/dólares)', 'source' => 'InvestorApplication.currency / InvestmentOpportunity.currency'],
                    'tipo_amortizacion' => ['desc' => 'Tipo de amortización elegida por el inversionista', 'source' => 'InvestorOpportunity.pivot.amortization_type'],
                ],
            ],
            'prestamo' => [
                'label' => 'Datos del Préstamo',
                'icon' => 'landmark',
                'color' => '#d97706',
                'bgLight' => '#fffbeb',
                'bgDark' => '#78350f',
                'textLight' => '#b45309',
                'textDark' => '#fcd34d',
                'borderLight' => '#fde68a',
                'borderDark' => '#92400e',
                'variables' => [
                    'prestamo_monto' => ['desc' => 'Monto total del préstamo otorgado', 'source' => 'Loan.amount'],
                    'prestamo_codigo' => ['desc' => 'Código único del préstamo', 'source' => 'Loan.code'],
                    'prestamo_plazo' => ['desc' => 'Plazo del préstamo en meses', 'source' => 'Loan.months'],
                    'prestamo_tasa_interes' => ['desc' => 'Tasa de interés anual del préstamo (%)', 'source' => 'Loan.interest_rate'],
                    'prestamo_cuota' => ['desc' => 'Cuota mensual a pagar del préstamo', 'source' => 'Loan.monthly_payment'],
                    'prestamo_fecha_desembolso' => ['desc' => 'Fecha en que se desembolsó el dinero', 'source' => 'Loan.disbursement_date'],
                    'prestamo_motivo' => ['desc' => 'Motivo o razón del préstamo', 'source' => 'Loan.reason'],
                    'prestamo_total' => ['desc' => 'Monto total a pagar (capital + intereses)', 'source' => 'Loan.total_amount'],
                ],
            ],
            'pagos' => [
                'label' => 'Pagos Realizados',
                'icon' => 'credit-card',
                'color' => '#0891b2',
                'bgLight' => '#ecfeff',
                'bgDark' => '#164e63',
                'textLight' => '#0e7490',
                'textDark' => '#67e8f9',
                'borderLight' => '#a5f3fc',
                'borderDark' => '#155e75',
                'variables' => [
                    'total_pagado' => ['desc' => 'Suma de todos los pagos realizados hasta la fecha', 'source' => 'Suma InterestPayment.amount'],
                    'pagos_realizados' => ['desc' => 'Cantidad de pagos que ya se han efectuado', 'source' => 'Conteo InterestPayment pagados'],
                    'pagos_pendientes' => ['desc' => 'Cantidad de pagos que faltan por realizar', 'source' => 'Total cuotas - pagos_realizados'],
                    'proximo_pago_fecha' => ['desc' => 'Fecha del próximo pago programado', 'source' => 'LoanInstallment.date próxima pendiente'],
                    'proximo_pago_monto' => ['desc' => 'Monto correspondiente al próximo pago', 'source' => 'LoanInstallment.amount próxima pendiente'],
                    'ultimo_pago_fecha' => ['desc' => 'Fecha en que se realizó el último pago', 'source' => 'InterestPayment.paid_at último'],
                    'ultimo_pago_monto' => ['desc' => 'Monto del último pago realizado', 'source' => 'InterestPayment.amount último'],
                ],
            ],
            'mora' => [
                'label' => 'Mora y Penalidades',
                'icon' => 'alert-triangle',
                'color' => '#dc2626',
                'bgLight' => '#fef2f2',
                'bgDark' => '#7f1d1d',
                'textLight' => '#b91c1c',
                'textDark' => '#fca5a5',
                'borderLight' => '#fecaca',
                'borderDark' => '#991b1b',
                'variables' => [
                    'tasa_mora' => ['desc' => 'Porcentaje de tasa de mora aplicable (%)', 'source' => 'Loan.late_fee_rate'],
                    'dias_gracia' => ['desc' => 'Días de gracia antes de aplicar mora', 'source' => 'Loan.grace_days'],
                    'mora_diaria' => ['desc' => 'Monto monetario de la mora diaria', 'source' => 'Loan.late_fee_daily_amount'],
                    'dias_mora' => ['desc' => 'Días actuales en mora (si aplica)', 'source' => 'Calculado desde vencimiento'],
                    'interes_mora_acumulado' => ['desc' => 'Interés por mora acumulado hasta hoy', 'source' => 'Suma InterestPayment.late_fee'],
                    'penalidad_actual' => ['desc' => 'Penalidad total actual por mora', 'source' => 'Calculado: dias_mora * mora_diaria'],
                ],
            ],
            'empresa' => [
                'label' => 'Datos de la Empresa',
                'icon' => 'building-2',
                'color' => '#7c3aed',
                'bgLight' => '#f5f3ff',
                'bgDark' => '#4c1d95',
                'textLight' => '#6d28d9',
                'textDark' => '#c4b5fd',
                'borderLight' => '#ddd6fe',
                'borderDark' => '#5b21b6',
                'variables' => [
                    'empresa_nombre' => ['desc' => 'Nombre legal de la empresa', 'source' => 'CompanySetting.company_name'],
                    'empresa_ruc' => ['desc' => 'RUC de la empresa', 'source' => 'CompanySetting.company_ruc'],
                    'empresa_direccion' => ['desc' => 'Dirección fiscal de la empresa', 'source' => 'CompanySetting.company_address'],
                    'empresa_telefono' => ['desc' => 'Teléfono de contacto de la empresa', 'source' => 'CompanySetting.company_phone'],
                    'empresa_email' => ['desc' => 'Correo electrónico de la empresa', 'source' => 'CompanySetting.company_email'],
                    'empresa_representante' => ['desc' => 'Nombre del representante legal', 'source' => 'CompanySetting.representative_name'],
                    'empresa_representante_dni' => ['desc' => 'DNI del representante legal', 'source' => 'CompanySetting.representative_dni'],
                    'logo_empresa' => ['desc' => 'Logo de la empresa (se insertará como imagen)', 'source' => 'Imagen'],
                    'firma_representante' => ['desc' => 'Firma del representante legal (se insertará como imagen)', 'source' => 'Imagen'],
                    'sello_empresa' => ['desc' => 'Sello de la empresa (se insertará como imagen)', 'source' => 'Imagen'],
                ],
            ],
            'contrato' => [
                'label' => 'Datos del Contrato',
                'icon' => 'file-text',
                'color' => '#4b5563',
                'bgLight' => '#f9fafb',
                'bgDark' => '#1f2937',
                'textLight' => '#374151',
                'textDark' => '#9ca3af',
                'borderLight' => '#e5e7eb',
                'borderDark' => '#374151',
                'variables' => [
                    'contrato_nombre' => ['desc' => 'Título o nombre de este contrato', 'source' => 'Contract.name'],
                    'contrato_numero' => ['desc' => 'Número único de identificación del contrato', 'source' => 'Generado: CT-00001'],
                    'fecha_firma' => ['desc' => 'Fecha en que se firma este contrato', 'source' => 'Fecha actual'],
                    'ciudad' => ['desc' => 'Ciudad donde se firma el contrato', 'source' => 'Configuración'],
                    'firma_cliente' => ['desc' => 'Firma del cliente/inversionista (se insertará como imagen)', 'source' => 'Imagen'],
                    'huella_cliente' => ['desc' => 'Huella digital del cliente (se insertará como imagen)', 'source' => 'Imagen'],
                    'fecha_actual' => ['desc' => 'Fecha de hoy (día/mes/año)', 'source' => 'now()'],
                ],
            ],
            'fechas' => [
                'label' => 'Fechas en Letras',
                'icon' => 'calendar',
                'color' => '#db2777',
                'bgLight' => '#fdf2f8',
                'bgDark' => '#831843',
                'textLight' => '#be185d',
                'textDark' => '#f9a8d4',
                'borderLight' => '#fbcfe8',
                'borderDark' => '#9d174d',
                'variables' => [
                    'dia_hoy' => ['desc' => 'Día de hoy en número (ej: 23)', 'source' => 'now()->format("d")'],
                    'mes_hoy' => ['desc' => 'Mes actual en texto (ej: mayo)', 'source' => 'now()->formatLocalized("%B")'],
                    'anio_hoy' => ['desc' => 'Año actual (ej: 2026)', 'source' => 'now()->format("Y")'],
                    'dia_letras' => ['desc' => 'Día de hoy escrito con letras (ej: veintitrés)', 'source' => 'Conversión a texto'],
                    'mes_letras' => ['desc' => 'Mes actual escrito con letras (ej: mayo)', 'source' => 'Texto fijo del mes'],
                    'anio_letras' => ['desc' => 'Año actual escrito con letras (ej: dos mil veintiséis)', 'source' => 'Conversión a texto'],
                    'fecha_completa' => ['desc' => 'Fecha completa formateada (23 de mayo de 2026)', 'source' => 'Formato largo'],
                    'fecha_completa_letras' => ['desc' => 'Fecha completa escrita en letras', 'source' => 'Conversión completa a texto'],
                ],
            ],
        ];
    }

    public static function getVariableColor(string $varKey): ?string
    {
        foreach (self::getAvailableVariables() as $category) {
            if (isset($category['variables'][$varKey])) {
                return $category['color'];
            }
        }
        return null;
    }

    public static function getCategoryForVariable(string $varKey): ?string
    {
        foreach (self::getAvailableVariables() as $catKey => $category) {
            if (isset($category['variables'][$varKey])) {
                return $catKey;
            }
        }
        return null;
    }

    public static function getAllVariablesFlat(): array
    {
        $flat = [];
        foreach (self::getAvailableVariables() as $catKey => $category) {
            foreach ($category['variables'] as $varKey => $varData) {
                $flat[$varKey] = [
                    'key' => $varKey,
                    'label' => $varKey,
                    'description' => $varData['desc'],
                    'category' => $catKey,
                    'category_label' => $category['label'],
                    'color' => $category['color'],
                    'bg_light' => $category['bgLight'],
                    'bg_dark' => $category['bgDark'],
                    'text_light' => $category['textLight'],
                    'text_dark' => $category['textDark'],
                    'border_light' => $category['borderLight'],
                    'border_dark' => $category['borderDark'],
                    'source' => $varData['source'],
                ];
            }
        }
        return $flat;
    }
}
