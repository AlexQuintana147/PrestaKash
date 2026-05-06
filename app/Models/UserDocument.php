<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $fillable = [
        'user_id',
        'selfie',
        'dni_front',
        'dni_back',
        'utility_bill',
        'status',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
