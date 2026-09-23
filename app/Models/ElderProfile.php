<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CareVisit;

class ElderProfile extends Model
{
    protected $fillable = [
        'created_by',
        'full_name',
        'date_of_birth',
        'medical_conditions',
        'medications',
        'allergies',
        'mobility_status',
        'care_preferences',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function careVisits(): HasMany
    {
        return $this->hasMany(CareVisit::class, 'elder_id');
    }
}

