<?php

return [

    // El nombre que se ve en la app (logo, pestaña del navegador).
    'name' => 'TAGKING',

    /*
    |--------------------------------------------------------------------------
    | Dominios que llevan a la dirección oficial
    |--------------------------------------------------------------------------
    |
    | La dirección oficial es APP_URL (https://tagking.org). Quien entre por
    | uno de estos dominios se va a la misma página en la dirección oficial.
    |
    */

    'redirect_hosts' => array_filter(array_map('trim', explode(',', env(
        'KINGTAG_REDIRECT_HOSTS',
        'tagking.cl,www.tagking.cl,www.tagking.org',
    )))),

    /*
    |--------------------------------------------------------------------------
    | Centro del mapa
    |--------------------------------------------------------------------------
    |
    | Dónde se abre el mapa cuando no sabemos la ubicación de la persona.
    | Por defecto, el centro de Santiago.
    |
    */

    'map_center' => [
        'lat' => (float) env('KINGTAG_MAP_LAT', -33.4489),
        'lng' => (float) env('KINGTAG_MAP_LNG', -70.6693),
    ],

    /*
    |--------------------------------------------------------------------------
    | Leer el tag de la foto con IA
    |--------------------------------------------------------------------------
    |
    | Al cazar un tag, la IA de Claude (Anthropic) mira la foto y sugiere
    | el texto. Se activa poniendo la clave en .env como ANTHROPIC_API_KEY.
    | Sin clave la app funciona igual, solo que la persona escribe el tag.
    |
    */

    'reader' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('KINGTAG_IA_MODELO', 'claude-haiku-4-5'),
    ],

];
