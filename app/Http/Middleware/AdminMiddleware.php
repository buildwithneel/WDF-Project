<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Middleware
 *
 * Protects all /admin routes.
 * Only allows users with role = 'admin' to pass through.
 * Non-admin authenticated users receive a 403 Forbidden response.
 *
 * Registered in bootstrap/app.php as alias 'admin'.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated AND has admin role
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // User is admin — allow the request to continue
        return $next($request);
    }
}
