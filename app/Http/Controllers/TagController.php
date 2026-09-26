<?php

namespace App\Http\Controllers;

use App\Models\Graffiti;
use App\Models\Tag;
use App\Services\Ranking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * El perfil de un tag: mapa de sus grafitis, feed de fotos y su lugar en
     * el ranking junto al tag de arriba y el de abajo.
     */
    public function show(Request $request, Tag $tag, Ranking $ranking): View
    {
        $tag->load('artist');
        $votes = Tag::query()->whereKey($tag->id)->withVoteCounts()->first();
        $graffitis = $tag->graffitis()->latest('id')->get();

        return view('tags.show', [
            'tag' => $tag,
            'graffitis' => $graffitis,
            'points' => $graffitis->map(fn (Graffiti $g) => [
                'lat' => $g->lat,
                'lng' => $g->lng,
                'thumb' => $g->thumbUrl(),
                'tag' => $tag->text,
            ]),
            'photos' => $tag->photos()->latest('photos.id')->limit(60)->get(),
            'rank' => $ranking->around($tag),
            'kings' => (int) $votes->kings_count,
            'toys' => (int) $votes->toys_count,
            'isAdmin' => (bool) $request->user()?->is_admin,
            'canModerate' => (bool) $request->user()?->canModerate(),
        ]);
    }

    /**
     * "Mi perfil" del menú: lleva al tag del usuario o a reclamar uno.
     */
    public function mine(Request $request): RedirectResponse
    {
        $tag = $request->user()->tag()->first();

        return $tag
            ? redirect()->route('tags.show', $tag)
            : redirect()->route('my-tag.create');
    }
}
