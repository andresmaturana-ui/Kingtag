<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['text', 'normalized', 'artist_id', 'profile_photo'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /**
     * Deja el texto de un tag en una forma comparable: "Kasé 1" y "KASE1"
     * son el mismo tag.
     */
    public static function normalize(string $text): string
    {
        return preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::ascii($text)));
    }

    /**
     * Busca un tag por su texto o lo crea sin dueño.
     */
    public static function findOrCreateByText(string $text): self
    {
        return static::firstOrCreate(
            ['normalized' => static::normalize($text)],
            ['text' => Str::upper(trim($text))],
        );
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function graffitis(): HasMany
    {
        return $this->hasMany(Graffiti::class);
    }

    public function photos(): HasManyThrough
    {
        return $this->hasManyThrough(Photo::class, Graffiti::class);
    }

    public function isClaimed(): bool
    {
        return $this->artist_id !== null;
    }

    /**
     * La imagen que representa al tag: la foto que subió el artista o, si no
     * la hay, la del último grafiti registrado.
     */
    public function imageUrl(): ?string
    {
        if ($this->profile_photo) {
            return Storage::disk('public')->url($this->profile_photo);
        }

        $latest = $this->graffitis()->latest('id')->first();

        return $latest?->thumbUrl();
    }
}
