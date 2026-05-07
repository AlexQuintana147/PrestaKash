<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'months',
        'interest_rate',
        'technology_fee',
        'igv',
        'total_amount',
        'monthly_payment',
        'status',
        'type',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }}
