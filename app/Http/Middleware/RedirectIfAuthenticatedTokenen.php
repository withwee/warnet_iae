<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedTokenen
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('jwt_token')) {
            // Jika sudah login, redirect ke dashboard
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
