<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['standup_id', 'user_id', 'body', 'has_blocker'])]
class StandupNote extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'has_blocker' => 'boolean',
    ];

    public function standup(): BelongsTo
    {
        return $this->belongsTo(Standup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
