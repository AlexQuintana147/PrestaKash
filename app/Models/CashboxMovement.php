<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashboxMovement extends Model
{
    protected $fillable = [
        'cashbox_id',
        'user_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
