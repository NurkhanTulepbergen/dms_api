<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Доступ запрещён!');
        }

        $roleArray = explode(',', $roles);

        if (! in_array(Auth::user()->role, $roleArray)) {
            return redirect('/')->with('error', 'Доступ запрещён!');
        }

        return $next($request);
    }
}
