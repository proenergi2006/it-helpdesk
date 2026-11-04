<?php

namespace App\Http\Middleware;

use App\Models\Asset;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// User GA dan Logistik hanya boleh memakai modul aset, profil, dan fitur tiket dasar.
// Halaman IT lain (dashboard, trend, report, proyek, dokumen, meeting, approval) ditutup.
class LimitAssetRoles
{
    private const ALLOWED = [
        'assets.*', 'profile.*', 'logout', 'welcome', 'tickets.store', 'tickets.my', 'tickets.my.*',
        'trix.upload', 'password.*', 'verification.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && isset(Asset::ROLE_DEPARTMENTS[$user->role]) && !$request->routeIs(...self::ALLOWED)) {
            if ($request->isMethod('GET') && !$request->expectsJson()) {
                return redirect()->route('assets.dashboard');
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
