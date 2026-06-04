<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
    protected $fillable = [
        'name',
        'bank_name',
        'currency',
        'account_number',
        'cci',
        'color',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(CashboxMovement::class);
    }

    /**
     * Decrement balance safely — throws if it would go negative.
     */
    public function safeDecrement(string $column, float $amount): void
    {
        $fresh = static::lockForUpdate()->find($this->id);

        if ($fresh->$column < $amount) {
            throw new \Exception("Saldo insuficiente en la caja «{$this->name}». Disponible: {$fresh->$column}, requerido: {$amount}");
        }

        $fresh->decrement($column, $amount);
        $this->$column = $fresh->$column - $amount;
    }
}
