<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->documents?->status === 'approved') {
            if (
                !$request->routeIs('client.*') &&
                !$request->routeIs('ubigeo.*') &&
                !$request->routeIs('logout')
            ) {
                return redirect()->route('client.dashboard');
            }
        }

        return $next($request);
    }
}
