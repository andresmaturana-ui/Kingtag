<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un mensaje de la conversación entre TAGKING y un usuario. from_admin dice
 * quién lo escribió; read_at, si ya lo leyó el otro lado.
 */
#[Fillable(['user_id', 'from_admin', 'sender_id', 'broadcast_id', 'body', 'read_at'])]
class InboxMessage extends Model
{
    protected function casts(): array
    {
        return [
            'from_admin' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
