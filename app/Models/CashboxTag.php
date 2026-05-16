<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashboxTag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function movements()
    {
        return $this->hasMany(CashboxMovement::class);
    }
}
