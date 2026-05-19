<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'old_data',
        'new_data',
        'status',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public static function fieldLabels(): array
    {
        return [
            'institution' => 'Institución',
            'career' => 'Carrera',
            'academic_status' => 'Estado académico',
            'has_children' => 'Tiene hijos',
            'children_count' => 'Número de hijos',
            'instagram' => 'Instagram',
            'is_employed' => 'Empleado',
            'employment_type' => 'Tipo de empleo',
            'workplace' => 'Lugar de trabajo',
            'job_title' => 'Cargo',
            'independent_type' => 'Tipo independiente',
            'monthly_income' => 'Ingreso mensual',
            'marital_status' => 'Estado civil',
            'spouse_first_name' => 'Nombre del cónyuge',
            'spouse_last_name' => 'Apellido del cónyuge',
            'spouse_dni' => 'DNI del cónyuge',
        ];
    }

    public static function formatValue(string $field, $value): string
    {
        if ($value === null) {
            return '—';
        }

        return match ($field) {
            'has_children' => $value ? 'Sí' : 'No',
            'is_employed' => $value ? 'Sí' : 'No',
            'academic_status' => match ($value) {
                'Educación superior' => 'Educación superior',
                'Técnico' => 'Técnico',
                'Ninguno de los anteriores' => 'Ninguno de los anteriores',
                default => $value,
            },
            'employment_type' => match ($value) {
                'dependiente' => 'Dependiente',
                'independiente' => 'Independiente',
                default => $value,
            },
            'independent_type' => match ($value) {
                'comerciante' => 'Comerciante',
                'empresario' => 'Empresario',
                'emprendedor' => 'Emprendedor',
                default => $value,
            },
            'marital_status' => match ($value) {
                'soltero' => 'Soltero(a)',
                'casado_separado' => 'Casado(a) - Bienes separados',
                'casado_compartido' => 'Casado(a) - Sociedad de gananciales',
                default => $value,
            },
            'monthly_income' => 'S/ ' . number_format((float) $value, 2),
            default => (string) $value,
        };
    }
}
