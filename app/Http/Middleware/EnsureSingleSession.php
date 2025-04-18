<?php

namespace App\Http\Middleware;

use App\Models\User_Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()) {
            $userId = auth()->id();
            $sessionId = session()->getId();

            // Check if the user already has an active session
            $existingSession = User_Session::where('user_id', $userId)->first();

            if ($existingSession && $existingSession->session_id !== $sessionId) {
                // If a different session exists, log out the user
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect('/login')->with('error', 'Your account has been logged in elsewhere.');
            }
        }

        return $next($request);
    }
}
