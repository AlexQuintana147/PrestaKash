<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestPayment extends Model
{
    protected $fillable = [
        'loan_installment_id',
        'payment_voucher',
        'payment_method',
        'bank_name',
        'operation_number',
        'amount',
        'late_fee',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function installment()
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }
}
