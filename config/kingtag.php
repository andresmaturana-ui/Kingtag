<?php

return [

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

];
