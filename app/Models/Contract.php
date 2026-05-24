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
                'label' => 'Persona',
                'icon' => 'user',
                'variables' => [
                    'nombre_completo' => 'Nombre completo del cliente/inversionista',
                    'nombres' => 'Nombres (primer nombre)',
                    'apellidos' => 'Apellidos completos',
                    'dni' => 'DNI / Número de identificación',
                    'ruc' => 'RUC (si aplica)',
                    'email' => 'Correo electrónico',
                    'telefono' => 'Teléfono',
                    'direccion' => 'Dirección completa',
                    'fecha_nacimiento' => 'Fecha de nacimiento',
                    'estado_civil' => 'Estado civil',
                    'ocupacion' => 'Ocupación',
                ],
            ],
            'inversionista' => [
                'label' => 'Inversionista',
                'icon' => 'wallet',
                'variables' => [
                    'monto_invertido' => 'Monto total invertido',
                    'tasa_interes' => 'Tasa de interés (%)',
                    'plazo_meses' => 'Plazo en meses',
                    'cuota_mensual' => 'Cuota mensual a recibir',
                    'fecha_inicio' => 'Fecha de inicio de inversión',
                    'fecha_fin' => 'Fecha de fin de inversión',
                    'codigo_inversion' => 'Código de la inversión',
                    'oportunidad_titulo' => 'Título de la oportunidad',
                ],
            ],
            'pagos' => [
                'label' => 'Pagos',
                'icon' => 'credit-card',
                'variables' => [
                    'total_pagado' => 'Monto total pagado al inversionista',
                    'pagos_realizados' => 'Número de pagos realizados',
                    'pagos_pendientes' => 'Número de pagos pendientes',
                    'proximo_pago_fecha' => 'Fecha del próximo pago',
                    'proximo_pago_monto' => 'Monto del próximo pago',
                    'ultima_fecha_pago' => 'Fecha del último pago realizado',
                ],
            ],
            'mora' => [
                'label' => 'Mora',
                'icon' => 'alert-triangle',
                'variables' => [
                    'tasa_mora' => 'Tasa de mora (%)',
                    'dias_mora' => 'Días en mora actual',
                    'interes_mora_acumulado' => 'Interés por mora acumulado',
                    'penalidad_mora' => 'Penalidad por mora',
                ],
            ],
            'empresa' => [
                'label' => 'Empresa',
                'icon' => 'building-2',
                'variables' => [
                    'empresa_nombre' => 'Nombre de la empresa',
                    'empresa_ruc' => 'RUC de la empresa',
                    'empresa_direccion' => 'Dirección de la empresa',
                    'empresa_telefono' => 'Teléfono de la empresa',
                    'empresa_email' => 'Email de la empresa',
                    'empresa_representante' => 'Representante legal',
                    'logo_empresa' => '[Logo de la empresa]',
                    'firma_empresa' => '[Firma del representante]',
                    'sello_empresa' => '[Sello de la empresa]',
                ],
            ],
            'contrato' => [
                'label' => 'Contrato',
                'icon' => 'file-text',
                'variables' => [
                    'contrato_nombre' => 'Nombre del contrato',
                    'fecha_contrato' => 'Fecha de firma del contrato',
                    'numero_contrato' => 'Número de contrato único',
                    'ciudad' => 'Ciudad donde se firma',
                    'firma_cliente' => '[Firma del cliente]',
                    'huella_cliente' => '[Huella digital del cliente]',
                    'fecha_actual' => 'Fecha actual (día de hoy)',
                ],
            ],
            'fechas' => [
                'label' => 'Fechas',
                'icon' => 'calendar',
                'variables' => [
                    'dia_actual' => 'Día actual',
                    'mes_actual' => 'Mes actual (nombre)',
                    'anio_actual' => 'Año actual',
                    'dia_escrito' => 'Día en letras (ej: veintitrés)',
                    'mes_escrito' => 'Mes en letras (ej: mayo)',
                    'anio_escrito' => 'Año en letras (ej: dos mil veintiséis)',
                    'fecha_completa' => 'Fecha completa formateada',
                    'fecha_letras' => 'Fecha completa en letras',
                ],
            ],
        ];
    }
}
