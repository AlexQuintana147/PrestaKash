<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'amount',
        'status',
        'voucher_path',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
