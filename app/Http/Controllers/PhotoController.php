<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Photo;
use App\Services\Moderation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Una foto en grande, con su tag, sus "me gusta" y sus comentarios.
     */
    public function show(Request $request, Photo $photo): View
    {
        $photo->load(['graffiti.tag', 'comments' => fn ($q) => $q->with('user')->oldest('id')])
            ->loadCount('likers');

        return view('photos.show', [
            'photo' => $photo,
            'tag' => $photo->graffiti->tag,
            'liked' => $request->user() && $photo->likers()->whereKey($request->user()->id)->exists(),
            'canModerate' => (bool) $request->user()?->canModerate(),
        ]);
    }

    /**
     * Da o quita el "me gusta".
     */
    public function like(Request $request, Photo $photo): RedirectResponse
    {
        $photo->likers()->toggle($request->user()->id);

        return redirect()->route('photos.show', $photo);
    }

    public function comment(Request $request, Photo $photo): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:500']]);

        $photo->comments()->create($data + ['user_id' => $request->user()->id]);

        return redirect(route('photos.show', $photo).'#comentarios');
    }

    /**
     * Borra una foto: lo pueden hacer los administradores y los curadores.
     * Vuelve al perfil del tag, porque la foto ya no existe.
     */
    public function destroy(Request $request, Photo $photo, Moderation $moderation): RedirectResponse
    {
        abort_unless($request->user()->canModerate(), 403);

        $tag = $photo->graffiti->tag;
        $moderation->deletePhoto($photo);

        return redirect()->route('tags.show', $tag)->with('status', 'Foto borrada.');
    }

    /**
     * Borra un comentario: lo puede hacer quien lo escribió o un administrador.
     */
    public function deleteComment(Request $request, Comment $comment): RedirectResponse
    {
        $user = $request->user();
        abort_unless($comment->user_id === $user->id || $user->is_admin, 403);

        $comment->delete();

        return redirect(route('photos.show', $comment->photo_id).'#comentarios');
    }
}
