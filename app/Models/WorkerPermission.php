<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerPermission extends Model
{
    protected $fillable = ['user_id', 'section', 'can_view', 'can_create', 'can_edit', 'can_delete'];

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean',
            'can_create' => 'boolean',
            'can_edit' => 'boolean',
            'can_delete' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function availableSections(): array
    {
        return [
            'clients' => 'Clientes',
            'investors' => 'Inversores',
            'loans' => 'Préstamos',
            'collections' => 'Cobranzas',
            'investments' => 'Inversiones',
            'cashboxes' => 'Cajas',
        ];
    }
}
