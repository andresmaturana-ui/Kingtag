<?php

namespace App\Http\Controllers;

use App\Services\Ranking;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __invoke(Ranking $ranking): View
    {
        return view('ranking', ['tags' => $ranking->top(20)]);
    }
}
