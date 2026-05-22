<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashboxMovement extends Model
{
    protected $fillable = [
        'code',
        'cashbox_id',
        'user_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'investor_user_id',
        'interest_payment_id',
        'cashbox_tag_id',
        'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'MOV-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
        });
    }

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

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'reference_id');
    }

    public function installment()
    {
        return $this->belongsTo(LoanInstallment::class, 'reference_id');
    }

    public function investment()
    {
        return $this->belongsTo(InvestmentOpportunity::class, 'reference_id');
    }

    public function investorPayout()
    {
        return $this->belongsTo(InvestorPayout::class, 'reference_id');
    }

    public function tag()
    {
        return $this->belongsTo(CashboxTag::class, 'cashbox_tag_id');
    }
}
