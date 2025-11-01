<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->session()->get('user');

        if (!$user || ($user['role'] ?? null) !== 'admin') {
            return redirect()->route('home')->withErrors(['error' => 'Anda tidak memiliki akses ke halaman ini.']);
        }

        return $next($request);
    }
}
