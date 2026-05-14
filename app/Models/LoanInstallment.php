<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'amount',
        'capital',
        'interest',
        'due_date',
        'status',
        'payment_voucher',
        'paid_at',
        'admin_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function getLateFeeAttribute()
    {
        $loan = $this->loan;
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
