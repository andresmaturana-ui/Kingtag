<?php

namespace App\Providers;

use App\Models\Report;
use App\Services\Inbox;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // El numerito de "Mis mensajes" en el menú, y para los admins el de respuestas sin leer y reportes pendientes.
        View::composer(['layouts.app', 'admin.nav', 'admin.index'], function ($view) {
            // Se calcula una vez por visita, aunque lo usen varias vistas.
            $attributes = request()->attributes;
            if (! $attributes->has('inbox_counts')) {
                $user = auth()->user();
                $inbox = app(Inbox::class);
                $attributes->set('inbox_counts', [
                    'inboxUnread' => $user ? $inbox->unreadFor($user) : 0,
                    'adminUnreadReplies' => $user?->is_admin ? $inbox->unreadReplies() : 0,
                    'pendingReports' => $user?->is_admin ? Report::whereNull('resolved_at')->count() : 0,
                ]);
            }
            $view->with($attributes->get('inbox_counts'));
        });
    }
}
