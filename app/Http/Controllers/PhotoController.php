<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Photo;
use App\Services\Inbox;
use App\Services\Moderation;
use App\Services\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Una foto en grande, con su tag y su puesto en el ranking, sus King y sus comentarios.
     */
    public function show(Request $request, Photo $photo, Ranking $ranking): View
    {
        $photo = Photo::withVotes($request->user())
            ->with(['graffiti.tag', 'comments' => fn ($q) => $q->with('user')->oldest('id')])
            ->findOrFail($photo->id);

        return view('photos.show', [
            'photo' => $photo,
            'tag' => $photo->graffiti->tag,
            'position' => $ranking->positions()[$photo->graffiti->tag_id] ?? null,
            'canModerate' => (bool) $request->user()?->canModerate(),
        ]);
    }

    /**
     * Da o quita el King. Desde el inicio llega por JavaScript y
     * responde los números nuevos; sin JavaScript vuelve a la página anterior.
     */
    public function vote(Request $request, Photo $photo, string $vote, Inbox $inbox): RedirectResponse|JsonResponse
    {
        $photo->vote($request->user(), $vote);
        $inbox->syncVoteNotices($photo, $request->user());

        if ($request->wantsJson()) {
            $photo = Photo::withVotes($request->user())->findOrFail($photo->id);

            return response()->json([
                'king' => $photo->likers_count,
                'kinged' => (bool) $photo->kinged,
            ]);
        }

        return redirect()->back(fallback: route('photos.show', $photo));
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
