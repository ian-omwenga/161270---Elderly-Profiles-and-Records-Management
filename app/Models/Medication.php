<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    protected $fillable = [
        'elder_profile_id',
        'name',
        'dosage',
        'schedule',
        'category',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function elderProfile(): BelongsTo
    {
        return $this->belongsTo(ElderProfile::class, 'elder_profile_id');
    }
}
