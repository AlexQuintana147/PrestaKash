<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(404);
        }

        $role = auth()->user()->role?->name;
        if (!in_array($role, ['superadmin', 'trabajador'])) {
            abort(404);
        }

        return $next($request);
    }
}
