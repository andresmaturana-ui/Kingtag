<?php

namespace App\Services;

use App\Models\Graffiti;
use App\Models\Photo;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Borra contenido junto con sus archivos. Cada foto es dueña de sus
 * archivos; la foto principal de un grafiti apunta a los de una de ellas.
 */
class Moderation
{
    /**
     * Borra una foto. Si era la última del grafiti, borra el grafiti; si era
     * la principal, otra foto pasa a ser la principal.
     */
    public function deletePhoto(Photo $photo): void
    {
        DB::transaction(function () use ($photo) {
            $graffiti = $photo->graffiti;
            $photo->delete();

            $next = $graffiti->photos()->latest('id')->first();

            if (! $next) {
                $graffiti->delete();
            } else {
                if ($graffiti->photo === $photo->path) {
                    $graffiti->photo = $next->path;
                    $graffiti->thumb = $next->thumb;
                }
                $graffiti->reports = max(1, $graffiti->reports - 1);
                $graffiti->save();
            }
        });

        Storage::disk('public')->delete([$photo->path, $photo->thumb]);
    }

    /**
     * Borra un tag con todos sus grafitis y fotos.
     */
    public function deleteTag(Tag $tag): void
    {
        $files = $tag->photos()->get()->flatMap(fn (Photo $p) => [$p->path, $p->thumb])->all();

        if ($tag->profile_photo) {
            $files[] = $tag->profile_photo;
        }

        // Los grafitis y fotos se borran en cascada desde la base de datos.
        $tag->delete();

        Storage::disk('public')->delete($files);
    }

    /**
     * Quita el dueño de un tag, por ejemplo si alguien reclamó uno ajeno.
     */
    public function unclaimTag(Tag $tag): void
    {
        if ($tag->profile_photo) {
            Storage::disk('public')->delete($tag->profile_photo);
        }

        $tag->update(['artist_id' => null, 'profile_photo' => null]);
    }

    /**
     * @return array<string, int>
     */
    public function counts(): array
    {
        return [
            'users' => DB::table('users')->count(),
            'tags' => Tag::count(),
            'graffitis' => Graffiti::count(),
            'photos' => Photo::count(),
        ];
    }
}
