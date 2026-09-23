<?php

namespace App\Http\Controllers;

use App\Models\Graffiti;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Datos en JSON para los mapas y el feed de grafitis cercanos.
 */
class MapController extends Controller
{
    public const NEARBY_RADIUS = 100;

    /**
     * Los grafitis dentro del área visible del mapa.
     */
    public function graffitis(Request $request): JsonResponse
    {
        $data = $request->validate([
            'south' => ['required', 'numeric', 'between:-90,90'],
            'north' => ['required', 'numeric', 'between:-90,90'],
            'west' => ['required', 'numeric', 'between:-180,180'],
            'east' => ['required', 'numeric', 'between:-180,180'],
            'tag' => ['nullable', 'integer'],
        ]);

        $graffitis = Graffiti::query()
            ->with('tag')
            ->whereBetween('lat', [$data['south'], $data['north']])
            ->whereBetween('lng', [$data['west'], $data['east']])
            ->when($data['tag'] ?? null, fn ($q, $tag) => $q->where('tag_id', $tag))
            ->latest('id')
            ->limit(500)
            ->get();

        return response()->json($graffitis->map(fn (Graffiti $g) => $this->present($g)));
    }

    /**
     * Los grafitis a menos de 100 metros, del más cercano al más lejano.
     */
    public function nearby(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $graffitis = Graffiti::withinMeters(
            (float) $data['lat'],
            (float) $data['lng'],
            self::NEARBY_RADIUS,
            Graffiti::query()->with('tag'),
        );

        return response()->json($graffitis->map(fn (Graffiti $g) => $this->present($g)));
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Graffiti $g): array
    {
        return [
            'id' => $g->id,
            'lat' => $g->lat,
            'lng' => $g->lng,
            'tag' => $g->tag->text,
            'tag_url' => route('tags.show', $g->tag_id),
            'thumb' => $g->thumbUrl(),
            'photo' => $g->photoUrl(),
            'reports' => $g->reports,
            'distance' => isset($g->distance) ? (int) round($g->distance) : null,
        ];
    }
}
