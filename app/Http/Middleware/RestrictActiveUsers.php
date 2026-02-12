<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictActiveUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($request->is('active-users') || $request->is('active-users/*')) {
            // Only allow admins to see the active users page,
            // even when impersonating another user.
            if (! $user || ! $user->hasAnyRole(['super_admin', 'admin'])) {
                abort(403);
            }
        }

        return $next($request);
    }
}

