<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\PhotoStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * "Ingresa tu tag": el artista reclama su tag para que todos los grafitis
 * registrados con ese texto aparezcan en su perfil.
 */
class MyTagController extends Controller
{
    public function create(Request $request): View
    {
        return view('tags.mine', ['tag' => $request->user()->tag()->first()]);
    }

    public function store(Request $request, PhotoStore $photos): RedirectResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:60'],
            'photo' => ['required', 'image', 'max:12288'],
        ]);

        $user = $request->user();
        $normalized = Tag::normalize($data['text']);

        if ($normalized === '') {
            throw ValidationException::withMessages(['text' => 'El tag tiene que tener al menos una letra o número.']);
        }

        $tag = Tag::firstWhere('normalized', $normalized);
        $current = $user->tag()->first();

        if ($current && $current->id !== $tag?->id) {
            throw ValidationException::withMessages([
                'text' => "Tu cuenta ya tiene el tag {$current->text}.",
            ]);
        }

        if ($tag && $tag->isClaimed() && $tag->artist_id !== $user->id) {
            throw ValidationException::withMessages([
                'text' => 'Ese tag ya fue reclamado por otro artista.',
            ]);
        }

        $stored = $photos->store($data['photo'], 'tags');

        $tag ??= new Tag(['normalized' => $normalized]);

        if ($tag->profile_photo) {
            Storage::disk('public')->delete($tag->profile_photo);
        }

        $tag->fill([
            'text' => Str::upper(trim($data['text'])),
            'artist_id' => $user->id,
            'profile_photo' => $stored['thumb'],
        ])->save();

        // La versión grande no se usa para el perfil.
        Storage::disk('public')->delete($stored['path']);

        return redirect()->route('tags.show', $tag)->with('status', 'Tu tag quedó registrado.');
    }
}
