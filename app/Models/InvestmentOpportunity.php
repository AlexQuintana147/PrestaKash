<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentOpportunity extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'target_amount',
        'min_investment',
        'currency',
        'annual_return_rate',
        'term_months',
        'risks',
        'status',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'OPP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function investors()
    {
        return $this->belongsToMany(User::class, 'investor_opportunity')
            ->withPivot(['amount', 'status', 'admin_notes'])
            ->withTimestamps();
    }

    public function approvedInvestors()
    {
        return $this->belongsToMany(User::class, 'investor_opportunity')
            ->wherePivot('status', 'approved')
            ->withPivot(['amount', 'status', 'admin_notes'])
            ->withTimestamps();
    }

    public function getRaisedAmountAttribute()
    {
        return $this->approvedInvestors()->sum('amount');
    }

    public function getProgressPercentAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->raised_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->raised_amount);
    }

    public function getEstimatedReturnAttribute()
    {
        return round($this->annual_return_rate * $this->term_months, 2);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isFunded(): bool
    {
        return $this->raised_amount >= $this->target_amount;
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }
}
