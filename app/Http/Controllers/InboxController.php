<?php

namespace App\Http\Controllers;

use App\Services\Inbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Mis mensajes": lo que escribe TAGKING, las respuestas del usuario y los avisos de King.
 */
class InboxController extends Controller
{
    public function __construct(private Inbox $inbox) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $items = $this->inbox->feed($user);
        // Se marcan como leídos al abrir, pero en esta visita se ven destacados.
        $this->inbox->markReadFor($user);

        return view('inbox.index', ['items' => $items]);
    }

    public function reply(Request $request): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $this->inbox->reply($request->user(), $data['body']);

        return redirect()->route('inbox')->with('status', 'Mensaje enviado a TAGKING.');
    }
}
