<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorPayout extends Model
{
    protected $fillable = [
        'investment_opportunity_id',
        'investor_user_id',
        'cashbox_id',
        'month_number',
        'amount',
        'interest_amount',
        'principal_amount',
        'payment_type',
        'payment_method',
        'voucher_path',
        'operation_number',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
    ];

    public function opportunity()
    {
        return $this->belongsTo(InvestmentOpportunity::class, 'investment_opportunity_id');
    }

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_user_id');
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }
}
