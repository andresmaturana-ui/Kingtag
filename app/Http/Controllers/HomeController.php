<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Inicio: los tres botones y, abajo, todas las fotos, de la más nueva a la más
     * vieja. Llegan de a 30 y el navegador pide las siguientes al hacer scroll.
     */
    public function __invoke(Request $request): View
    {
        return view('home', [
            'photos' => Photo::query()
                ->with('graffiti.tag')
                ->withVotes($request->user())
                ->latest('id')
                ->simplePaginate(30),
        ]);
    }
}
