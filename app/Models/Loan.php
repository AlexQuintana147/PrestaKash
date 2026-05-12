<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'months',
        'payment_days',
        'interest_rate',
        'technology_fee',
        'igv',
        'total_amount',
        'monthly_payment',
        'status',
        'type',
        'amortization_system',
        'reason',
        'bank_name',
        'account_number',
        'account_type',
        'cci',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
