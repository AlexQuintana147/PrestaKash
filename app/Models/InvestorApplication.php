<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'occupation',
        'intended_amount',
        'source_of_funds',
        'experience',
        'preferred_term',
        'status',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
