<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    /**
     * El manifest.webmanifest se sirve desde acá (y no como archivo estático)
     * para poder agregarle a los íconos un "?v=" que cambia cuando cambia el
     * ícono. Así, cuando se reemplaza el ícono, la URL cambia y el celular
     * no se queda con el archivo viejo que tenía guardado.
     */
    public function __invoke(): JsonResponse
    {
        $v = filemtime(public_path('icons/icon-512.png'));

        return response()->json([
            'name' => config('kingtag.name'),
            'short_name' => config('kingtag.name'),
            'description' => 'Los tags de la ciudad, en un mapa.',
            'lang' => 'es',
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#111111',
            'theme_color' => '#111111',
            'icons' => [
                ['src' => "/icons/icon-192.png?v={$v}", 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => "/icons/icon-512.png?v={$v}", 'sizes' => '512x512', 'type' => 'image/png'],
                ['src' => "/icons/icon-512.png?v={$v}", 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
