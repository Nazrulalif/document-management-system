<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \App\Http\Controllers\SamlLoginController;
use Illuminate\Support\Facades\Auth;
class SamlLoginListener
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
    public function handle(object $event): void
    {
        // $samlUser = $event->getSaml2User();
        // $attributes = $samlUser->getAttributes();
        // $azureUser = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] ?? null;

        // $finduser = User::where('email', $azureUser)->first();
        // Auth::login($finduser, true);


        $controller = app()->make(SamlLoginController::class);

        app()->call([$controller, 'login'], [
            'request' => request(),
            'event' => $event,
            
        ]);
        
    }
}
