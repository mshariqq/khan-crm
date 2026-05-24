<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roleId): Response
    {
        if (!Auth::guard('staff')->check()) {
            abort(401);
        }

        $user = Auth::guard('staff')->user();
        if ((int) $user->role !== (int) $roleId) {
            abort(403, 'Access denied. Required role not assigned.');
        }

        return $next($request);
    }
}
