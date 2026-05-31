<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'code',
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
        'disbursement_voucher',
        'disbursement_operation_number',
        'type',
        'disbursement_date',
        'payment_frequency',
        'amortization_system',
        'reason',
        'bank_name',
        'account_number',
        'account_type',
        'cci',
        'original_data',
        'late_fee_enabled',
        'grace_days',
        'late_fee_rate',
        'late_fee_daily_amount',
        'cashbox_id',
        'in_default',
    ];

    protected $casts = [
        'original_data' => 'array',
        'in_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'PRE-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function getOverdueInstallmentsAttribute()
    {
        return $this->installments()
            ->where('status', '!=', 'approved')
            ->where('due_date', '<', now()->startOfDay())
            ->get();
    }

    public function getAccumulatedOverdueDebtAttribute()
    {
        $overdue = $this->overdue_installments;
        return $overdue->sum('amount') + $overdue->sum('late_fee');
    }

    public function getNextInstallmentAttribute()
    {
        return $this->installments()
            ->where('status', '!=', 'approved')
            ->where('due_date', '>=', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->first();
    }
}
