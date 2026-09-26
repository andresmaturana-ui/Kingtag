<?php

namespace App\Services;

use App\Models\Broadcast;
use App\Models\InboxMessage;
use App\Models\Photo;
use App\Models\User;
use App\Models\VoteNotice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * La bandeja "Mis mensajes": conversación con TAGKING más los avisos de King y Toy.
 */
class Inbox
{
    /**
     * Cuántas cosas nuevas tiene el usuario: mensajes del admin y avisos sin leer.
     */
    public function unreadFor(User $user): int
    {
        return InboxMessage::where('user_id', $user->id)->where('from_admin', true)->whereNull('read_at')->count()
            + VoteNotice::where('user_id', $user->id)->whereNull('read_at')->count();
    }

    /**
     * Respuestas de usuarios que ningún admin ha leído.
     */
    public function unreadReplies(): int
    {
        return InboxMessage::where('from_admin', false)->whereNull('read_at')->count();
    }

    /**
     * Mensajes y avisos del usuario mezclados, del más nuevo al más antiguo.
     */
    public function feed(User $user, int $limit = 100): Collection
    {
        $messages = InboxMessage::where('user_id', $user->id)->latest('id')->limit($limit)->get();
        $notices = VoteNotice::where('user_id', $user->id)
            ->with(['actor', 'photo.graffiti.tag'])
            ->latest('id')->limit($limit)->get();

        return $messages->concat($notices)
            ->sortByDesc(fn ($item) => $item->created_at->getTimestamp() * 10 + ($item instanceof InboxMessage ? 1 : 0))
            ->take($limit)
            ->values();
    }

    public function markReadFor(User $user): void
    {
        InboxMessage::where('user_id', $user->id)->where('from_admin', true)->whereNull('read_at')->update(['read_at' => now()]);
        VoteNotice::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function sendToUser(User $admin, User $user, string $body): InboxMessage
    {
        return InboxMessage::create([
            'user_id' => $user->id,
            'from_admin' => true,
            'sender_id' => $admin->id,
            'body' => $body,
        ]);
    }

    /**
     * Un aviso para todos: cada usuario (menos quien lo envía) recibe su copia.
     */
    public function sendToAll(User $admin, string $body): Broadcast
    {
        return DB::transaction(function () use ($admin, $body) {
            $broadcast = Broadcast::create(['sender_id' => $admin->id, 'body' => $body]);
            $now = now();

            User::whereKeyNot($admin->id)->select('id')->chunkById(500, function ($users) use ($broadcast, $admin, $body, $now) {
                InboxMessage::insert($users->map(fn ($u) => [
                    'user_id' => $u->id,
                    'from_admin' => true,
                    'sender_id' => $admin->id,
                    'broadcast_id' => $broadcast->id,
                    'body' => $body,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            });

            return $broadcast;
        });
    }

    public function reply(User $user, string $body): InboxMessage
    {
        return InboxMessage::create([
            'user_id' => $user->id,
            'from_admin' => false,
            'sender_id' => $user->id,
            'body' => $body,
        ]);
    }

    /**
     * Después de un voto: avisa a quien subió la foto y al dueño del tag.
     * Si el voto se quitó o cambió, el aviso anterior desaparece.
     */
    public function syncVoteNotices(Photo $photo, User $actor): void
    {
        VoteNotice::where('photo_id', $photo->id)->where('actor_id', $actor->id)->delete();

        $kind = match (true) {
            $photo->likers()->whereKey($actor->id)->exists() => 'king',
            $photo->toyers()->whereKey($actor->id)->exists() => 'toy',
            default => null,
        };
        if (! $kind) {
            return;
        }

        $photo->loadMissing('graffiti.tag');
        collect([$photo->user_id, $photo->graffiti->tag->artist_id])
            ->filter()
            ->unique()
            ->reject(fn ($id) => $id === $actor->id)
            ->each(fn ($id) => VoteNotice::create([
                'user_id' => $id,
                'actor_id' => $actor->id,
                'photo_id' => $photo->id,
                'kind' => $kind,
            ]));
    }
}
