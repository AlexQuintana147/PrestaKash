<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $fillable = [
        'code',
        'loan_id',
        'installment_number',
        'amount',
        'capital',
        'interest',
        'due_date',
        'status',
        'payment_voucher',
        'payment_method',
        'bank_name',
        'operation_number',
        'requested_type',
        'amortization_type',
        'amortization_extra',
        'paid_at',
        'charged_late_fee',
        'admin_notes',
        'interest_payments_count',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'CUO-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
        });
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function interestPayments()
    {
        return $this->hasMany(\App\Models\InterestPayment::class, 'loan_installment_id')->orderBy('paid_at', 'asc');
    }

    public function getDisplayVoucherPathAttribute(): ?string
    {
        if (! empty($this->is_interest_payment) && ! empty($this->interest_payment)) {
            return $this->interest_payment->payment_voucher;
        }

        return $this->payment_voucher;
    }

    public function getCanViewPaymentVoucherAttribute(): bool
    {
        if (! $this->display_voucher_path) {
            return false;
        }

        if (! empty($this->is_interest_payment) && $this->interest_payment) {
            return true;
        }

        return $this->status === 'approved';
    }

    public function getLateFeeAttribute()
    {
        $loan = $this->loan;

        if ($this->status === 'approved' && $this->charged_late_fee > 0) {
            return $this->charged_late_fee;
        }

        if (isset($this->is_interest_payment) && $this->is_interest_payment && $this->interest_payment) {
            return $this->interest_payment->late_fee ?? 0;
        }

        if (!$loan->late_fee_enabled || $this->status === 'approved' || $this->due_date->isFuture()) {
            return 0;
        }

        $today = now()->startOfDay();
        $dueDate = $this->due_date->startOfDay();
        $daysPast = $dueDate->diffInDays($today);

        if ($daysPast <= $loan->grace_days) {
            return 0;
        }

        // Determinar hasta qué día se calcula la mora
        $nextInstallment = $loan->installments()
            ->where('installment_number', '>', $this->installment_number)
            ->orderBy('installment_number', 'asc')
            ->first();

        $limitDate = $today;
        if ($nextInstallment && $today->isAfter($nextInstallment->due_date)) {
            $limitDate = $nextInstallment->due_date->startOfDay();
        }

        $daysForCalculation = $dueDate->diffInDays($limitDate);

        // Cálculo diario basado en la tasa porcentual
        $moraRate = $this->amount * ($loan->late_fee_rate / 100) * $daysForCalculation;

        return round($moraRate, 2);
    }

    public function getTotalWithMoraAttribute()
    {
        return $this->amount + $this->late_fee;
    }

    public function getGraceDaysRemainingAttribute()
    {
        $loan = $this->loan;
        if (!$loan->late_fee_enabled || $this->status === 'approved' || $this->due_date->isFuture()) {
            return 0;
        }

        $today = now()->startOfDay();
        $dueDate = $this->due_date->startOfDay();
        $daysPast = $dueDate->diffInDays($today);

        if ($daysPast >= $loan->grace_days) {
            return 0;
        }

        return $loan->grace_days - $daysPast;
    }

    public function getPaymentStatusDetailAttribute()
    {
        if ($this->status !== 'approved' || !$this->paid_at) {
            return null;
        }

        $dueDate = $this->due_date->startOfDay();
        $paidDate = $this->paid_at->startOfDay();

        if ($paidDate->isBefore($dueDate)) {
            $daysBefore = $paidDate->diffInDays($dueDate);
            return ['label' => "{$daysBefore}d antes", 'color' => 'green', 'type' => 'before'];
        }

        if ($paidDate->isSameDay($dueDate)) {
            return ['label' => 'El mismo día', 'color' => 'green', 'type' => 'same_day'];
        }

        $daysLate = $dueDate->diffInDays($paidDate);
        if ($daysLate <= $this->loan->grace_days) {
            return ['label' => 'Salvado por gracia', 'color' => 'blue', 'days' => $daysLate, 'type' => 'grace'];
        }

        return ['label' => 'Pagado con mora', 'color' => 'orange', 'days' => $daysLate, 'type' => 'late'];
    }

    public function getAccumulatedDebtAttribute()
    {
        $loan = $this->loan;
        $previousPending = $loan->installments()
            ->where('installment_number', '<', $this->installment_number)
            ->where('status', '!=', 'approved')
            ->get();

        $totalBase = $this->amount + $previousPending->sum('amount');
        $totalMora = $this->late_fee + $previousPending->sum(fn($i) => $i->late_fee);

        return [
            'base' => $totalBase,
            'mora' => $totalMora,
            'total' => $totalBase + $totalMora,
            'count' => $previousPending->count() + 1
        ];
    }
}
