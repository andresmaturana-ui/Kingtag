<?php

namespace App\Models;

use Database\Factories\GraffitiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

#[Fillable(['tag_id', 'lat', 'lng', 'photo', 'thumb', 'reports'])]
class Graffiti extends Model
{
    /** @use HasFactory<GraffitiFactory> */
    use HasFactory;

    protected $table = 'graffitis';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'reports' => 'integer',
        ];
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function photoUrl(): string
    {
        return Storage::disk('public')->url($this->photo);
    }

    public function thumbUrl(): string
    {
        return Storage::disk('public')->url($this->thumb);
    }

    /**
     * Limita la consulta a un cuadrado alrededor de un punto. Es un primer
     * filtro rápido; la distancia exacta se revisa con {@see withinMeters()}.
     */
    public function scopeAround(Builder $query, float $lat, float $lng, float $meters): void
    {
        $dLat = $meters / 111320;
        $dLng = $meters / (111320 * max(cos(deg2rad($lat)), 0.01));

        $query->whereBetween('lat', [$lat - $dLat, $lat + $dLat])
            ->whereBetween('lng', [$lng - $dLng, $lng + $dLng]);
    }

    /**
     * Distancia en metros entre dos puntos (fórmula de haversine).
     */
    public static function metersBetween(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(min(1, sqrt($a)));
    }

    /**
     * Los grafitis a menos de $meters de un punto, del más cercano al más lejano.
     *
     * @return Collection<int, self>
     */
    public static function withinMeters(float $lat, float $lng, float $meters, ?Builder $query = null): Collection
    {
        return ($query ?? static::query())
            ->around($lat, $lng, $meters)
            ->get()
            ->each(fn (self $g) => $g->distance = static::metersBetween($lat, $lng, $g->lat, $g->lng))
            ->filter(fn (self $g) => $g->distance <= $meters)
            ->sortBy('distance')
            ->values();
    }
}
