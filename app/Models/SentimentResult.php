<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentResult extends Model
{
    protected $fillable = [
        'visit_id',
        'compound_score',
        'positive_score',
        'neutral_score',
        'negative_score',
        'classification',
        'flagged',
    ];

    protected function casts(): array
    {
        return [
            'compound_score' => 'float',
            'positive_score' => 'float',
            'neutral_score' => 'float',
            'negative_score' => 'float',
            'flagged' => 'boolean',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(CareVisit::class, 'visit_id');
    }
}
