<?php

namespace App\Providers;


use \App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use \Slides\Saml2\Events\SignedIn;
use App\Listeners\SamlLoginListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Azure\AzureExtendSocialite::class . '@handle',
        ],
        SignedIn::class => [
            SamlLoginListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // parent::boot();

        // Event::listen(SignedIn::class, [SamlLoginListener::class, 'handle']);

        // Event::listen(\Slides\Saml2\Events\SignedIn::class, function (\Slides\Saml2\Events\SignedIn $event) {
        //     $samlUser = $event->getSaml2User();
        //     $attributes = $samlUser->getAttributes();
        //     $azureUser = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] ?? $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ?? null;
                
                
        //     $finduser = User::where('email', $azureUser)->first();

            
        //         Auth::login($finduser, true);
        
        // });

        // Event::listen(\Slides\Saml2\Events\SignedOut::class, function (\Slides\Saml2\Events\SignedOut $event) {
        //     Auth::logout();
        //     Session::save();
        // });
        
        
        
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
