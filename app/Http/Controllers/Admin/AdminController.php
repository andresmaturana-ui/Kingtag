<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use App\Services\Moderation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private Moderation $moderation) {}

    public function index(): View
    {
        return view('admin.index', [
            'counts' => $this->moderation->counts(),
            'unread' => ContactMessage::whereNull('read_at')->count(),
        ]);
    }

    public function messages(): View
    {
        return view('admin.messages', [
            'messages' => ContactMessage::with('user')->latest('id')->paginate(30),
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return back();
    }

    public function deleteMessage(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'Mensaje borrado.');
    }

    public function tags(Request $request): View
    {
        $q = Tag::normalize((string) $request->query('q', ''));

        return view('admin.tags', [
            'q' => $request->query('q', ''),
            'tags' => Tag::query()
                ->with('artist')
                ->withCount(['graffitis', 'photos'])
                ->when($q !== '', fn ($query) => $query->where('normalized', 'like', "%{$q}%"))
                ->latest('id')
                ->paginate(30)
                ->withQueryString(),
        ]);
    }

    public function deleteTag(Tag $tag): RedirectResponse
    {
        $text = $tag->text;
        $this->moderation->deleteTag($tag);

        return redirect()->route('admin.tags')->with('status', "Borraste el tag {$text} con todos sus grafitis.");
    }

    public function unclaimTag(Tag $tag): RedirectResponse
    {
        $this->moderation->unclaimTag($tag);

        return back()->with('status', "El tag {$tag->text} quedó sin dueño.");
    }

    public function deletePhoto(Photo $photo): RedirectResponse
    {
        $this->moderation->deletePhoto($photo);

        return back()->with('status', 'Foto borrada.');
    }

    public function users(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        return view('admin.users', [
            'q' => $q,
            'users' => User::query()
                ->with('tag')
                ->withCount('photos')
                ->when($q !== '', fn ($query) => $query->where('username', 'like', "%{$q}%"))
                ->latest('id')
                ->paginate(30)
                ->withQueryString(),
        ]);
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $password = Str::password(10, symbols: false);
        $user->update(['password' => $password]);

        return back()->with('status', "Nueva clave para {$user->username}: {$password}");
    }

    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'No puedes borrar tu propia cuenta.');

        // Su tag queda sin dueño y sus fotos se mantienen sin autor.
        $user->delete();

        return back()->with('status', "Borraste la cuenta {$user->username}.");
    }
}
