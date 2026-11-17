<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allergy extends Model
{
    protected $fillable = [
        'elder_profile_id',
        'allergen',
        'reaction',
        'severity',
    ];

    public function elderProfile(): BelongsTo
    {
        return $this->belongsTo(ElderProfile::class, 'elder_profile_id');
    }
}
