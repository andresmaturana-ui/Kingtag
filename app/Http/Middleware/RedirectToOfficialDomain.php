<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToOfficialDomain
{
    /**
     * Manda a la dirección oficial (APP_URL) a quien entra por otro dominio,
     * por ejemplo tagking.cl → tagking.org, conservando la página.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $official = rtrim((string) config('app.url'), '/');
        $host = strtolower($request->getHost());

        if ($host !== parse_url($official, PHP_URL_HOST)
            && in_array($host, config('kingtag.redirect_hosts', []), true)) {
            return redirect()->away($official.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
