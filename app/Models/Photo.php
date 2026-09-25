<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Fillable(['graffiti_id', 'user_id', 'path', 'thumb'])]
class Photo extends Model
{
    public function graffiti(): BelongsTo
    {
        return $this->belongsTo(Graffiti::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Los usuarios que le dieron "King" (antes "me gusta").
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'photo_likes')->withTimestamps();
    }

    /**
     * Los usuarios que le dieron "Toy". Cada usuario da King o Toy, no los dos.
     */
    public function toyers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'photo_toys')->withTimestamps();
    }

    /**
     * Cuenta los King y Toy y, si hay alguien conectado, marca cuál dio él.
     */
    public function scopeWithVotes(Builder $query, ?User $user): void
    {
        $query->withCount(['likers', 'toyers']);

        if ($user) {
            $query->withExists([
                'likers as kinged' => fn ($q) => $q->whereKey($user->id),
                'toyers as toyed' => fn ($q) => $q->whereKey($user->id),
            ]);
        }
    }

    /**
     * Da o quita un voto ("king" o "toy"). Dar uno quita el otro.
     */
    public function vote(User $user, string $vote): void
    {
        [$mine, $other] = $vote === 'king' ? [$this->likers(), $this->toyers()] : [$this->toyers(), $this->likers()];

        DB::transaction(function () use ($user, $mine, $other) {
            $other->detach($user->id);
            $mine->toggle($user->id);
        });
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function thumbUrl(): string
    {
        return Storage::disk('public')->url($this->thumb);
    }
}
