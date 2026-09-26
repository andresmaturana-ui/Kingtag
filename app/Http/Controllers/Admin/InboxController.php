<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\InboxMessage;
use App\Models\User;
use App\Services\Inbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mensajes del admin a los usuarios: a todos, o uno a uno con sus respuestas.
 */
class InboxController extends Controller
{
    public function __construct(private Inbox $inbox) {}

    /**
     * Las conversaciones, la más reciente primero, y los avisos a todos.
     */
    public function index(): View
    {
        $latest = InboxMessage::selectRaw('user_id, max(id) as last_id')
            ->whereNull('broadcast_id')
            ->groupBy('user_id');

        $conversations = InboxMessage::query()
            ->joinSub($latest, 'latest', 'inbox_messages.id', '=', 'latest.last_id')
            ->with('user')
            ->orderByDesc('inbox_messages.id')
            ->select('inbox_messages.*')
            ->paginate(30);

        $unread = InboxMessage::where('from_admin', false)->whereNull('read_at')
            ->selectRaw('user_id, count(*) as n')->groupBy('user_id')->pluck('n', 'user_id');

        return view('admin.inbox', [
            'conversations' => $conversations,
            'unread' => $unread,
            'broadcasts' => Broadcast::withCount([
                'copies',
                'copies as read_count' => fn ($q) => $q->whereNotNull('read_at'),
            ])->latest('id')->limit(10)->get(),
        ]);
    }

    public function sendToAll(Request $request): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $broadcast = $this->inbox->sendToAll($request->user(), $data['body']);
        $n = $broadcast->copies()->count();

        return back()->with('status', $n === 1 ? 'Aviso enviado a 1 usuario.' : "Aviso enviado a {$n} usuarios.");
    }

    /**
     * La conversación con un usuario, como chat. Al abrirla, sus respuestas quedan leídas.
     */
    public function show(User $user): View
    {
        $messages = InboxMessage::where('user_id', $user->id)->with('sender')->latest('id')->limit(200)->get()->reverse();

        InboxMessage::where('user_id', $user->id)->where('from_admin', false)->whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.conversation', ['user' => $user, 'messages' => $messages]);
    }

    public function send(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $this->inbox->sendToUser($request->user(), $user, $data['body']);

        return redirect()->route('admin.inbox.show', $user)->with('status', "Mensaje enviado a {$user->username}.");
    }
}
