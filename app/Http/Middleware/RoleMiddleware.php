<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {

        // cek login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // cek role (bisa lebih dari 1)
        if (!in_array($user->role, $roles)) {
            return redirect('/login')->with('error','Akses ditolak!');
        }

        return $next($request);
    }
}