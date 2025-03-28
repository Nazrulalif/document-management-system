<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');

            
            if ($lastActivity && time() - $lastActivity > config('session.lifetime')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Check if the user logged in via Azure SAML
                if (session()->has('azure_user')) {
                    session()->forget('azure_user'); // Remove Azure session marker
                  
                }
                return redirect('/login')->with('message', 'Session expired due to inactivity.');
            }

            // Update last activity timestamp
            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}
