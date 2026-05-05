<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'first_name',
        'last_name',
        'dni',
        'email',
        'phone',
        'department',
        'province',
        'district',
        'address',
        'google_id',
        'avatar',
        'profile_completed',
        'step1_completed',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'profile_completed' => 'boolean',
            'step1_completed'   => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function documents()
    {
        return $this->hasOne(UserDocument::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isTrabajador(): bool
    {
        return $this->role->name === 'trabajador';
    }
}
