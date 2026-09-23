<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareVisit extends Model
{
    protected $fillable = [
        'elder_id',
        'caregiver_id',
        'visit_date',
        'tasks_completed',
        'mood_score',
        'appetite_score',
        'pain_level',
        'medication_taken',
        'visit_note',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'medication_taken' => 'boolean',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(ElderProfile::class, 'elder_id');
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }
}