<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles): Response
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // Redirect to login if not authenticated
        }

        // Check if the authenticated user's role is in the allowed roles
        if (in_array(Auth::user()->role_guid, $roles)) {
            return $next($request);
        }

        // If not authorized, redirect to the dashboard or show a 403 error
        return redirect('/dashboard')->with('error', 'Unauthorized access.');
    }
}
