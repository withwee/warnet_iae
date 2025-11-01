<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login.view')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}

