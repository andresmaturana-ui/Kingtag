<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Moderation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * «Mi cuenta»: los usuarios bloqueados y el botón para borrar la cuenta.
     */
    public function show(Request $request): View
    {
        return view('account.show', [
            'blocked' => $request->user()->blocked()->orderBy('username')->get(),
        ]);
    }

    /**
     * Página para borrar la cuenta. Es pública porque Google Play pide un
     * link donde cualquiera vea cómo borrar su cuenta, incluso sin la app.
     */
    public function confirmDelete(Request $request): View
    {
        return view('account.delete', [
            'photoCount' => $request->user()?->photos()->count() ?? 0,
        ]);
    }

    /**
     * Borra la cuenta. Se borran sus comentarios, King, mensajes y bloqueos
     * (en cascada desde la base de datos) y su tag queda sin dueño. Sus fotos
     * se borran si lo pide; si no, quedan sin autor.
     */
    public function destroy(Request $request, Moderation $moderation): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']], [
            'password.current_password' => 'La clave no es correcta.',
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($request->boolean('delete_photos')) {
            $user->photos()->with('graffiti')->get()->each(fn ($photo) => $moderation->deletePhoto($photo));
        }

        if ($tag = $user->tag()->first()) {
            $moderation->unclaimTag($tag);
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Tu cuenta fue borrada.');
    }

    /**
     * Bloquea o desbloquea a un usuario: deja de ver (o vuelve a ver) sus
     * fotos y comentarios.
     */
    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        $me = $request->user();
        abort_if($user->is($me), 422, 'No puedes bloquearte a ti mismo.');

        $changes = $me->blocked()->toggle($user->id);

        return back()->with('status', $changes['attached']
            ? "Bloqueaste a {$user->username}. Ya no verás sus fotos ni sus comentarios."
            : "Desbloqueaste a {$user->username}.");
    }
}
