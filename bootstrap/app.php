<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /**
         * 1) Session habis / belum login -> redirect ke login
         * (Laravel 12: render() harus callable, jadi type-hint exception di parameter)
         */
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        });

        /**
         * 2) 403 tapi user ternyata sudah logout/expired -> redirect login
         */
        $exceptions->render(function (\Throwable $e, $request) {
            $status = ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : null;

            if ($status === Response::HTTP_FORBIDDEN && !auth()->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }
                return redirect()->route('login');
            }

            return null; // biarkan default handler
        });

        /**
         * 3) 419 Page Expired (CSRF/session expired) -> redirect login
         */
        $exceptions->render(function (\Throwable $e, $request) {
            $status = ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : null;

            if ($status === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Page expired.'], 419);
                }

                return redirect()->route('login')
                    ->with('status', 'Session habis, silakan login kembali.');
            }

            return null;
        });

    })
    ->create();
