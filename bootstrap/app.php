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
        $middleware->alias([
            'onboarding.step1' => \App\Http\Middleware\OnboardingStep1Completed::class,
            'incomplete'       => \App\Http\Middleware\RedirectIfIncomplete::class,
            'prevent.back'     => \App\Http\Middleware\PreventBackHistory::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\RedirectIfIncomplete::class,
            \App\Http\Middleware\PreventBackHistory::class,
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
