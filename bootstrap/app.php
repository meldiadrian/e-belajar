<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SetSecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\FirewallMiddleware::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            \Illuminate\Support\Facades\Log::error('Database Query Error: ' . $e->getMessage(), [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            // Cegah kebocoran struktur query database ke scanner/penyerang saat debug off atau request JSON
            if (!config('app.debug') || $request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan sistem data.',
                ], 500);
            }
        });
    })->create();
