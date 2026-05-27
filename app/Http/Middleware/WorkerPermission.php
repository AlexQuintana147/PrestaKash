<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkerPermission
{
    private static array $routeSectionMap = [
        'usuarios' => 'clients',
        'inversores' => 'investors',
        'prestamos' => 'loans',
        'cobranzas' => 'collections',
        'inversiones' => 'investments',
        'cajas' => 'cashboxes',
        'trabajadores' => 'workers',
        'referidos' => 'clients',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Superadmin has full access
        if ($user->role?->name === 'superadmin') {
            return $next($request);
        }

        // Only trabajador needs permission check
        if ($user->role?->name !== 'trabajador') {
            abort(404);
        }

        $section = $this->resolveSection($request);

        if (!$section) {
            // Allow access to dashboard, sidebar-stats and other non-section routes
            return $next($request);
        }

        $perm = $user->workerPermissions()->where('section', $section)->first();

        if (!$perm || !$perm->can_view) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }

    private function resolveSection(Request $request): ?string
    {
        $path = $request->path(); // e.g. "gestion-interna/usuarios"
        $segments = explode('/', $path);

        foreach ($segments as $segment) {
            if (isset(self::$routeSectionMap[$segment])) {
                return self::$routeSectionMap[$segment];
            }
        }

        return null;
    }
}
