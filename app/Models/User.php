<?php

namespace App\Models;

use App\Models\ClientProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'first_name',
        'last_name',
        'dni',
        'email',
        'phone',
        'person_type',
        'ruc',
        'business_name',
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
        'loan_interest_rate',
        'credit_line',
        'is_ghost',
        'ghost_code',
        'ghost_type',
        'ghost_cashbox_id',
        'ghost_currency',
        'investor_rejection_reason',
        'investment_rejection_reason',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_completed' => 'boolean',
            'step1_completed' => 'boolean',
            'loan_interest_rate' => 'float',
            'credit_line' => 'decimal:2',
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

    public function profileChangeRequests()
    {
        return $this->hasMany(ProfileChangeRequest::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function investorApplication()
    {
        return $this->hasOne(InvestorApplication::class);
    }

    public function investmentOpportunities()
    {
        return $this->belongsToMany(InvestmentOpportunity::class, 'investor_opportunity')
            ->withPivot(['amount', 'status', 'admin_notes'])
            ->withTimestamps();
    }

    public function createdOpportunities()
    {
        return $this->hasMany(InvestmentOpportunity::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isTrabajador(): bool
    {
        return $this->role?->name === 'trabajador';
    }

    public function workerPermissions()
    {
        return $this->hasMany(WorkerPermission::class);
    }

    public function hasPermission(string $section, string $action = 'view'): bool
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        $perm = $this->workerPermissions()->where('section', $section)->first();
        if (!$perm) return false;
        return match($action) {
            'view' => $perm->can_view,
            'create' => $perm->can_create,
            'edit' => $perm->can_edit,
            'delete' => $perm->can_delete,
            default => false,
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'superadmin';
    }

    public function getIsInvestorAttribute(): bool
    {
        return $this->investorApplication && $this->investorApplication->status === 'approved';
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralRecords()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function getReferralStatusAttribute(): array
    {
        if ($this->blocked()) {
            return ['label' => 'Bloqueado', 'color' => 'red'];
        }

        if ($this->documents?->status === 'rejected') {
            return ['label' => 'Rechazado', 'color' => 'red'];
        }

        if ($this->profile_completed && $this->documents?->status === 'approved') {
            return ['label' => 'Aceptado', 'color' => 'green'];
        }

        if ($this->documents?->status === 'pending' || ($this->profile_completed && !$this->documents)) {
            return ['label' => 'Pendiente', 'color' => 'yellow'];
        }

        return ['label' => 'En proceso', 'color' => 'gray'];
    }

    public function blocked()
    {
        return BlockedUser::where('email', $this->email)->exists();
    }

    public static function generateReferralCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }

    public function ghostCashbox()
    {
        return $this->belongsTo(Cashbox::class, 'ghost_cashbox_id');
    }

    public function ghostMovements()
    {
        return $this->hasMany(CashboxMovement::class, 'user_id')->where('reference_type', 'ghost');
    }
}
