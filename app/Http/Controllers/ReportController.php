<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Photo;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function photo(Request $request, Photo $photo): RedirectResponse
    {
        $this->store($request, ['photo_id' => $photo->id]);

        return redirect()->route('photos.show', $photo)->with('status', 'Gracias. Revisaremos la foto.');
    }

    public function comment(Request $request, Comment $comment): RedirectResponse
    {
        $this->store($request, ['comment_id' => $comment->id]);

        return redirect(route('photos.show', $comment->photo_id).'#comentarios')
            ->with('status', 'Gracias. Revisaremos el comentario.');
    }

    /**
     * Guarda el reporte. Si la misma persona reporta lo mismo otra vez,
     * no se duplica.
     */
    private function store(Request $request, array $target): void
    {
        $data = $request->validate(['reason' => ['required', Rule::in(array_keys(Report::REASONS))]]);

        Report::updateOrCreate(
            $target + ['user_id' => $request->user()->id, 'resolved_at' => null],
            $data,
        );
    }
}
