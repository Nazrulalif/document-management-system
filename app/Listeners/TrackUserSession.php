<?php

namespace App\Listeners;

use App\Models\User_Session;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackUserSession
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $userId = $event->user->id;
        $sessionId = session()->getId();

         // Remove old session (enforce single login)
        User_Session::where('user_id', $userId)->delete();

        User_Session::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}
