<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Un aviso que el admin envió a todos los usuarios.
 */
#[Fillable(['sender_id', 'body'])]
class Broadcast extends Model
{
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(InboxMessage::class);
    }
}
