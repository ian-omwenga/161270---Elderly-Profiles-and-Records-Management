<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'visit_id',
        'family_user_id',
        'message',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(CareVisit::class, 'visit_id');
    }

    public function familyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'family_user_id');
    }
}
