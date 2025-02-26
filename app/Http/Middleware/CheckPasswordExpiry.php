<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isPasswordExpired()) {
            // Allow access to the password reset page to avoid infinite redirects
            if (!$request->is('password-expired') && !$request->is('password-expired/*') && !$request->is('logout')) {
                return redirect()->route('password.expired')->with('warning', 'Your password has expired. Please reset it.');
            }
        }

        return $next($request);
    }
}
