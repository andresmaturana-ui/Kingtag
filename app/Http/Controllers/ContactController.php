<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:80'],
            'contact' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create($data + ['user_id' => $request->user()?->id]);

        return redirect()->route('home')->with('status', 'Gracias, recibimos tu mensaje.');
    }
}
