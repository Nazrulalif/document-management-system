<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->role_guid == 1 || Auth::user()->role_guid == 2 || Auth::user()->role_guid == 3) {
                return $next($request);
            } else {
                return redirect('/home')->with('message', 'error');
            }
        } else {
            return redirect('/login')->with('message', 'error');
        }

        return $next($request);
    }
}
