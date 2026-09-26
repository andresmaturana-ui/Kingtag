<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Services\Ranking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Inicio: los tres botones y, abajo, todas las fotos, de la más nueva a la más
     * vieja. Llegan de a 30 y el navegador pide las siguientes al hacer scroll.
     * Cada foto muestra el puesto de su tag en el ranking.
     */
    public function __invoke(Request $request, Ranking $ranking): View
    {
        return view('home', [
            'photos' => Photo::query()
                ->with('graffiti.tag')
                ->withVotes($request->user())
                ->latest('id')
                ->simplePaginate(30),
            'positions' => $ranking->positions(),
        ]);
    }
}
