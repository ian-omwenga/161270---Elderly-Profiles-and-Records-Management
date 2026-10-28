<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ElderProfile extends Model
{
    protected $fillable = [
        'created_by',
        'assigned_caregiver_id',
        'full_name',
        'photo_path',
        'date_of_birth',
        'gender',
        'blood_type',
        'residence',
        'physician_name',
        'physician_phone',
        'medical_conditions',
        'medications',
        'allergies',
        'mobility_status',
        'independence_score',
        'care_preferences',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'independence_score' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedCaregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_caregiver_id');
    }

    public function careVisits(): HasMany
    {
        return $this->hasMany(CareVisit::class, 'elder_id');
    }

    public function medicationList(): HasMany
    {
        return $this->hasMany(Medication::class, 'elder_profile_id');
    }

    public function allergyList(): HasMany
    {
        return $this->hasMany(Allergy::class, 'elder_profile_id');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return route('elder.photo', $this->id);
        }

        return null;
    }
}
