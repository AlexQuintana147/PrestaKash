<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'notifications/*',
        ]);
        $middleware->alias([
            'onboarding.step1' => \App\Http\Middleware\OnboardingStep1Completed::class,
            'incomplete'       => \App\Http\Middleware\RedirectIfIncomplete::class,
            'prevent.back'     => \App\Http\Middleware\PreventBackHistory::class,
            'superadmin'       => \App\Http\Middleware\SuperAdmin::class,
            'cleanup.rejected' => \App\Http\Middleware\CleanupRejectedUsers::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\RedirectIfIncomplete::class,
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\RedirectSuperAdmin::class,
            \App\Http\Middleware\RedirectClient::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            return response()->view('errors.404', [], 403);
        });
    })->create();
