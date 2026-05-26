<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractClause;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $contract = Contract::create([
            'name' => 'Contrato de Préstamo e Inversión – Plantilla Completa',
            'slug' => 'contrato-prestamo-inversion-completo',
            'description' => 'Contrato modelo con todas las cláusulas de texto y todos los cuadros automáticos para validación visual y funcional.',
            'is_active' => true,
        ]);

        $order = 0;

        $clauses = [
            [
                'title' => 'PRIMERA: PARTES DEL CONTRATO',
                'content' => "Este contrato de préstamo e inversión es celebrado entre:\n\n"
                    . "A) La empresa [empresa_nombre], con RUC [empresa_ruc], debidamente representada por su gerente general [empresa_representante], identificado con DNI N° [empresa_representante_dni], con domicilio en [empresa_direccion].\n\n"
                    . "B) El cliente [nombre_completo], identificado con DNI N° [dni], con domicilio en [direccion], teléfono [telefono] y correo electrónico [email].\n\n"
                    . "Ambas partes se reconocen mutuamente la capacidad legal para celebrar el presente contrato.",
                'box_type' => null,
            ],
            [
                'title' => '',
                'content' => '',
                'box_type' => 'datos_cliente',
            ],
            [
                'title' => 'SEGUNDA: OBJETO DEL CONTRATO',
                'content' => "El objeto del presente contrato es regular la relación jurídica entre las partes con respecto al préstamo de dinero que [empresa_nombre] otorga al cliente [nombre_completo], por el monto de [prestamo_monto] ([moneda]).\n\n"
                    . "El préstamo se encuentra codificado bajo el número [prestamo_codigo] y su finalidad es: [prestamo_motivo].\n\n"
                    . "El desembolso se realizó el día [prestamo_fecha_desembolso].",
                'box_type' => null,
            ],
            [
                'title' => '',
                'content' => '',
                'box_type' => 'cronograma',
            ],
            [
                'title' => 'TERCERA: PLAZO Y FORMA DE PAGO',
                'content' => "El préstamo se otorga por un plazo de [prestamo_plazo], con una tasa de interés anual del [prestamo_tasa_interes].\n\n"
                    . "El cliente se obliga a pagar cuotas mensuales de [prestamo_cuota], las cuales serán abonadas conforme al cronograma establecido en la cláusula anterior.\n\n"
                    . "El monto total a pagar asciende a [prestamo_total].",
                'box_type' => null,
            ],
            [
                'title' => '',
                'content' => '',
                'box_type' => 'monto_debe',
            ],
            [
                'title' => 'CUARTA: MORA Y PENALIDADES',
                'content' => "En caso de incumplimiento en el pago de las cuotas en las fechas estipuladas, se aplicará una tasa de mora del [tasa_mora] por cada día de atraso, contados a partir del vencimiento de la cuota y después de los [dias_gracia] días de gracia.\n\n"
                    . "La mora diaria asciende a [mora_diaria]. En caso de mora prolongada, se podrán aplicar penalidades adicionales conforme a las condiciones establecidas en este contrato.\n\n"
                    . "Días actuales en mora: [dias_mora]. Interés por mora acumulado: [interes_mora_acumulado]. Penalidad actual: [penalidad_actual].",
                'box_type' => null,
            ],
            [
                'title' => '',
                'content' => '',
                'box_type' => 'condiciones',
            ],
            [
                'title' => 'QUINTA: CUENTA BANCARIA DEL CLIENTE',
                'content' => "Para efectos de depósitos y/o devoluciones, el cliente designa la siguiente cuenta bancaria:\n\n"
                    . "Número de cuenta: [numero_cuenta]\n"
                    . "CCI: [cci]\n"
                    . "Tipo de cuenta: [tipo_cuenta]\n"
                    . "Moneda: [tipo_moneda]\n\n"
                    . "En caso de modificación de estos datos, el cliente deberá notificar por escrito a [empresa_nombre] con al menos cinco (05) días hábiles de anticipación.",
                'box_type' => null,
            ],
            [
                'title' => 'SEXTA: CUENTA BANCARIA DE LA EMPRESA',
                'content' => "Para efectos de pagos por parte del cliente, los fondos deberán ser depositados a la siguiente cuenta de [empresa_nombre]:\n\n"
                    . "Número de cuenta: [numero_cuenta_empresa]\n"
                    . "CCI: [cci_empresa]\n"
                    . "Tipo de cuenta: [tipo_cuenta_empresa]\n"
                    . "Moneda: [tipo_moneda_empresa]\n\n"
                    . "Los pagos realizados a otras cuentas no serán reconocidos salvo autorización expresa y por escrito de la empresa.",
                'box_type' => null,
            ],
            [
                'title' => '',
                'content' => '',
                'box_type' => 'resumen_inversion',
            ],
            [
                'title' => 'SÉPTIMA: ESTADO DE PAGOS',
                'content' => "Hasta la fecha de emisión del presente documento, el cliente ha realizado [pagos_realizados], sumando un total pagado de [total_pagado].\n\n"
                    . "Quedan pendientes por cancelar [pagos_pendientes].\n\n"
                    . "Último pago registrado: [ultimo_pago_monto] el día [ultimo_pago_fecha].\n"
                    . "Próximo pago programado: [proximo_pago_monto] con fecha de vencimiento [proximo_pago_fecha].",
                'box_type' => null,
            ],
            [
                'title' => 'OCTAVA: DISPOSICIONES FINALES',
                'content' => "Ambas partes declaran que el presente contrato se firma en la ciudad de [ciudad], el día [dia_hoy] de [mes_hoy] del año [anio_hoy] ([fecha_completa]).\n\n"
                    . "El contrato se rige por las leyes vigentes de la República del Perú. Cualquier controversia será resuelta por las vías legales correspondientes.\n\n"
                    . "Firma del cliente:\n[huella_cliente]\n[firma_cliente]\n\n"
                    . "Firma del representante legal de [empresa_nombre]:\n[firma_representante]\n[sello_empresa]\n[logo_empresa]",
                'box_type' => null,
            ],
        ];

        foreach ($clauses as $clause) {
            ContractClause::create([
                'contract_id' => $contract->id,
                'box_type' => $clause['box_type'],
                'title' => $clause['title'],
                'content' => $clause['content'],
                'order' => $order++,
            ]);
        }
    }
}
