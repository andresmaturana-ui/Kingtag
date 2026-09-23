<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MyTagController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SightingController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/buscar', SearchController::class)->name('search');
Route::get('/ranking', RankingController::class)->name('ranking');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');

Route::get('/mapa/grafitis', [MapController::class, 'graffitis'])->name('map.graffitis');
Route::get('/mapa/cerca', [MapController::class, 'nearby'])->name('map.nearby');

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

    Route::get('/registrar', [SightingController::class, 'create'])->name('sightings.create');
    // Máximo 30 registros por hora por usuario, para frenar el spam.
    Route::post('/registrar', [SightingController::class, 'store'])->name('sightings.store')->middleware('throttle:30,60');
});
