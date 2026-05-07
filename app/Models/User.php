<?php

namespace App\Models;

use App\Models\ClientProfile;
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
        'referred_by',
        'referral_code',
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

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isTrabajador(): bool
    {
        return $this->role?->name === 'trabajador';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'superadmin';
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public static function generateReferralCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }
}
