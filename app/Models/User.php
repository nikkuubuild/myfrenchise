<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_CASE_MANAGER = 'case_manager';
    public const ROLE_VERIFICATION_OFFICER = 'verification_officer';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'state',
        'city',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Check if user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if user is a case manager.
     */
    public function isCaseManager(): bool
    {
        return $this->role === self::ROLE_CASE_MANAGER;
    }

    /**
     * Check if user is a verification officer.
     */
    public function isVerificationOfficer(): bool
    {
        return $this->role === self::ROLE_VERIFICATION_OFFICER;
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is any staff role.
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_CASE_MANAGER,
            self::ROLE_VERIFICATION_OFFICER,
            self::ROLE_ADMIN,
        ], true);
    }

    /**
     * User's submitted managed service enquiries.
     */
    public function enquiries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Enquiry::class);
    }
}
