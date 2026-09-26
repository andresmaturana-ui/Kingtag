<?php

namespace App\Services;

use App\Models\Graffiti;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Registra un tag visto en la calle. Si ya había un grafiti con el mismo texto
 * a menos de MERGE_RADIUS metros, la foto se suma a ese grafiti en vez de
 * crear uno nuevo, así cada pieza cuenta una sola vez en el ranking.
 */
class RegisterSighting
{
    /** El GPS del celular tiene un error de 5 a 20 metros. */
    public const MERGE_RADIUS = 20;

    /**
     * Si el teléfono avisó que su lectura era menos precisa (entre calles con
     * edificios pasa seguido), buscamos el grafiti un poco más lejos, hasta
     * este máximo, para no duplicarlo.
     */
    public const MAX_MERGE_RADIUS = 50;

    public static function mergeRadius(?float $accuracy): float
    {
        return min(max(self::MERGE_RADIUS, $accuracy ?? 0), self::MAX_MERGE_RADIUS);
    }

    public function __construct(private PhotoStore $photos) {}

    /**
     * @return array{graffiti: Graffiti, merged: bool}
     */
    public function handle(User $user, string $text, float $lat, float $lng, UploadedFile $file, ?float $accuracy = null): array
    {
        $stored = $this->photos->store($file);

        return DB::transaction(function () use ($user, $text, $lat, $lng, $stored, $accuracy) {
            $tag = Tag::findOrCreateByText($text);

            $graffiti = Graffiti::withinMeters($lat, $lng, self::mergeRadius($accuracy), $tag->graffitis()->getQuery())->first();
            $merged = $graffiti !== null;

            if ($merged) {
                $graffiti->increment('reports');
            } else {
                $graffiti = $tag->graffitis()->create([
                    'lat' => $lat,
                    'lng' => $lng,
                    'photo' => $stored['path'],
                    'thumb' => $stored['thumb'],
                ]);
            }

            Photo::create([
                'graffiti_id' => $graffiti->id,
                'user_id' => $user->id,
                'path' => $stored['path'],
                'thumb' => $stored['thumb'],
            ]);

            return ['graffiti' => $graffiti, 'merged' => $merged];
        });
    }
}
