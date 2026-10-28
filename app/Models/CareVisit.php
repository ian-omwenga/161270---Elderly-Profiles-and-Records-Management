<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CareVisit extends Model
{
    protected $fillable = [
        'elder_id',
        'caregiver_id',
        'visit_date',
        'clock_in',
        'clock_out',
        'tasks_completed',
        'mood_score',
        'appetite_score',
        'pain_level',
        'medication_taken',
        'urgent_concern',
        'visit_note',
        'cognitive_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'mood_score' => 'integer',
        'appetite_score' => 'integer',
        'pain_level' => 'integer',
        'medication_taken' => 'boolean',
        'urgent_concern' => 'boolean',
        'mood_score' => 'integer',
        'appetite_score' => 'integer',
        'pain_level' => 'integer',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(ElderProfile::class, 'elder_id');
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function sentimentResult(): HasOne
    {
        return $this->hasOne(SentimentResult::class, 'visit_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'visit_id');
    }
}
