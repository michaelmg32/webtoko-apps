<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Admin dapat mengakses semua route
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check jika user role sesuai dengan salah satu role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
