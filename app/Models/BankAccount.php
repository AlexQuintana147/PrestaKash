<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'user_id',
        'alias',
        'bank_name',
        'account_type',
        'currency',
        'account_number',
        'cci',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
