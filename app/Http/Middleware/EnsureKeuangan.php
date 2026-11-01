<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKeuangan
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memastikan hanya pengguna dengan peran 'keuangan'
     * yang dapat mengkases route manajemen omzet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if (! $request->user()->isKeuangan()) {
            abort(403, 'Akses ditolak: halaman ini khusus Bagian Keuangan.');
        }

        return $next($request);
    }
}
