<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Inicio: los tres botones y, abajo, las últimas fotos que se subieron.
     */
    public function __invoke(): View
    {
        return view('home', [
            'photos' => Photo::query()
                ->with('graffiti.tag')
                ->withCount('likers')
                ->latest('id')
                ->simplePaginate(30),
        ]);
    }
}
