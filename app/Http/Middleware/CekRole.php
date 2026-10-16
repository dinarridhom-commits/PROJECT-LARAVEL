<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('loginError', 'Silakan login terlebih dahulu.');
        }

        // Jika parameter role tidak diberikan, default hanya 'superadmin'
        if (empty($roles)) {
            $roles = ['superadmin'];
        }

       if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

