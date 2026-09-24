<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MyTagController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SightingController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/buscar', SearchController::class)->name('search');
Route::get('/ranking', RankingController::class)->name('ranking');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');
Route::get('/fotos/{photo}', [PhotoController::class, 'show'])->name('photos.show');

Route::get('/mapa/grafitis', [MapController::class, 'graffitis'])->name('map.graffitis');
Route::get('/mapa/cerca', [MapController::class, 'nearby'])->name('map.nearby');

Route::get('/contacto', [ContactController::class, 'create'])->name('contact');
Route::post('/contacto', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,60');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->middleware('throttle:10,60');
    Route::get('/entrar', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/entrar', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/salir', [AuthController::class, 'logout'])->name('logout');

    Route::get('/mi-perfil', [TagController::class, 'mine'])->name('profile');
    Route::get('/mi-tag', [MyTagController::class, 'create'])->name('my-tag.create');
    Route::post('/mi-tag', [MyTagController::class, 'store'])->name('my-tag.store');

    Route::post('/fotos/{photo}/me-gusta', [PhotoController::class, 'like'])->name('photos.like')->middleware('throttle:60,1');
    Route::post('/fotos/{photo}/comentarios', [PhotoController::class, 'comment'])->name('photos.comment')->middleware('throttle:20,60');
    Route::delete('/comentarios/{comment}', [PhotoController::class, 'deleteComment'])->name('comments.delete');

    Route::get('/registrar', [SightingController::class, 'create'])->name('sightings.create');
    // Máximo 30 registros por hora por usuario, para frenar el spam.
    Route::post('/registrar', [SightingController::class, 'store'])->name('sightings.store')->middleware('throttle:30,60');
});

// Panel de administración. Se entra con una cuenta marcada como admin:
// php artisan kingtag:admin nombre_de_usuario
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('/mensajes', [AdminController::class, 'messages'])->name('messages');
    Route::post('/mensajes/{message}/leido', [AdminController::class, 'markRead'])->name('messages.read');
    Route::delete('/mensajes/{message}', [AdminController::class, 'deleteMessage'])->name('messages.delete');

    Route::get('/tags', [AdminController::class, 'tags'])->name('tags');
    Route::delete('/tags/{tag}', [AdminController::class, 'deleteTag'])->name('tags.delete');
    Route::post('/tags/{tag}/liberar', [AdminController::class, 'unclaimTag'])->name('tags.unclaim');
    Route::delete('/fotos/{photo}', [AdminController::class, 'deletePhoto'])->name('photos.delete');

    Route::get('/usuarios', [AdminController::class, 'users'])->name('users');
    Route::post('/usuarios/{user}/clave', [AdminController::class, 'resetPassword'])->name('users.password');
    Route::delete('/usuarios/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
});
