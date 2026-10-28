<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    public function isFamily(): bool
    {
        return $this->role === 'family';
    }

    public function isCaregiver(): bool
    {
        return $this->role === 'caregiver';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function createdElders(): HasMany
    {
        return $this->hasMany(ElderProfile::class, 'created_by');
    }

    public function assignedElders(): HasMany
    {
        return $this->hasMany(ElderProfile::class, 'assigned_caregiver_id');
    }

    public function careVisits(): HasMany
    {
        return $this->hasMany(CareVisit::class, 'caregiver_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'family_user_id');
    }
}
