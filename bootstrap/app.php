<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // default kosong
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /**
         * 1) Jika session habis / user belum login
         *    -> arahkan ke halaman login (bukan error page)
         */
        $exceptions->render(AuthenticationException::class, function ($e, $request) {
            // kalau request API/AJAX bisa return json 401
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        });

        /**
         * 2) Jika 403 tapi ternyata user SUDAH tidak login (session expired)
         *    -> redirect login agar UX enak
         */
        $exceptions->render(function (\Throwable $e, $request) {
            $code = (int) $e->getCode();

            if ($code === Response::HTTP_FORBIDDEN && !auth()->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }

                return redirect()->route('login');
            }

            return null; // biarkan Laravel handle sisanya
        });

        /**
         * 3) (Opsional tapi sering kepakai) error 419 CSRF/session expired
         *    -> redirect login + flash message
         */
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
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
