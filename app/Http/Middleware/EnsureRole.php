<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return $next($request);
            }

            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return redirect()->route('shops.index')->with('status', 'Access restricted.');
    }
}
