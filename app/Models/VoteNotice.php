<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aviso de que alguien le dio King a una foto tuya o de tu tag.
 */
#[Fillable(['user_id', 'actor_id', 'photo_id', 'kind', 'read_at'])]
class VoteNotice extends Model
{
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
