<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Session;
use Slides\Saml2\Events\SignedIn;
use Slides\Saml2\Http\Controllers\Saml2Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Slides\Saml2\Auth as Saml2Auth;
class SamlLoginController extends Controller
{
    public function login(SignedIn $event, Request $request)
    {
        $samlUser = $event->getSaml2User();
        $attributes = $samlUser->getAttributes();
        $azureUser = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] ?? ($attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ?? null);

        $finduser = User::where('email', $azureUser)->first();

        if (!$finduser) {
            session()->put('error', 'User not found. Please Super Administrator');
            session()->save(); // Explicitly save session before redirect
            return redirect()->route('login');
        }

        if ($finduser->login_method == 'email_password') {
            session()->put('error', 'Authentication failed, your account is using email/password login.');
            return redirect(route('login'));
        }

        try {
            if ($finduser->is_active === 'Y') {
                Auth::login($finduser, true);

                // Log the login action
                AuditLog::create([
                    'action' => 'Login',
                    'model' => 'User',
                    'user_guid' => $finduser->id,
                    'ip_address' => $request->ip(),
                ]);

                $finduser->update([
                    'login_method' => 'azure_saml',
                ]);

                session()->flash('success', 'You have successfully logged in.');
            } else {
                session()->put('error', 'Authentication failed. Your account is inactive.');
            }
        } catch (\Exception $e) {
            session()->put('error', 'Something went wrong. Please try again.');
            return redirect()->route('login');
        }
    }
}
