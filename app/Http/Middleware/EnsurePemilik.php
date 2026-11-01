<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePemilik
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memastikan hanya pengguna dengan peran 'pemilik'
     * yang dapat mengkases route yang dilindungi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user tidak/belum terautentikasi, biarkan middleware auth yang menangani
        if (! $request->user()) {
            return $next($request);
        }

        // Jika user login tetapi BUKAN pemilik usaha, tolak dengan HTTP 403 Forbidden
        // Memilih abort(403) agar mengembalikan HTTP status 403 yang valid untuk audit keamanan
        if (! $request->user()->isPemilik()) {
            abort(403, 'Akses ditolak: halaman ini khusus pemilik usaha.');
        }

        return $next($request);
    }
}
