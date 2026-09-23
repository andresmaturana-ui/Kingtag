<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\RegisterSighting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * "Registrar tag": cualquier usuario fotografía un tag en la calle.
 */
class SightingController extends Controller
{
    public function create(): View
    {
        return view('sightings.create');
    }

    public function store(Request $request, RegisterSighting $register): RedirectResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:60'],
            'photo' => ['required', 'image', 'max:12288'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'lat.required' => 'No pudimos obtener tu ubicación. Activa el GPS y dale permiso a la app.',
            'lng.required' => 'No pudimos obtener tu ubicación. Activa el GPS y dale permiso a la app.',
        ]);

        if (Tag::normalize($data['text']) === '') {
            throw ValidationException::withMessages(['text' => 'El tag tiene que tener al menos una letra o número.']);
        }

        $result = $register->handle(
            $request->user(),
            $data['text'],
            (float) $data['lat'],
            (float) $data['lng'],
            $data['photo'],
        );

        $status = $result['merged']
            ? 'Este grafiti ya estaba registrado. Sumamos tu foto.'
            : 'Grafiti registrado. ¡Gracias!';

        return redirect()->route('tags.show', $result['graffiti']->tag_id)->with('status', $status);
    }
}
