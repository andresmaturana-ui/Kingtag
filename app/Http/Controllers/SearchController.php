<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $normalized = Tag::normalize($q);

        $results = $normalized === ''
            ? collect()
            : Tag::query()
                ->where('normalized', 'like', '%'.$normalized.'%')
                ->whereHas('graffitis')
                ->withCount('graffitis')
                ->orderByDesc('graffitis_count')
                ->limit(30)
                ->get();

        return view('search', ['q' => $q, 'results' => $results]);
    }
}
