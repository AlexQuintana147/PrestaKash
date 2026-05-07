<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    protected $fillable = [
        'user_id', 'institution', 'career', 'academic_status',
        'has_children', 'instagram', 'is_employed', 'marital_status',
        'spouse_name', 'spouse_dni',
    ];

    protected $casts = [
        'has_children' => 'boolean',
        'is_employed'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
