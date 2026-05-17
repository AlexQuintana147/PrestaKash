<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestPayment extends Model
{
    protected $fillable = [
        'code',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'INT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
        });
    }

    public function installment()
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }
}
