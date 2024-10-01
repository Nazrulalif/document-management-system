<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect(route('dashboard.admin'));
        }
        return view('session/login-form');
    }

    public function post(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->is_active == "Y") {
                // dd($user->role_guid);
                if ($user->role_guid == 1 || $user->role_guid == 2 || $user->role_guid == 3) {
                    AuditLog::create([
                        'action' => 'Login',
                        'model' => 'User',
                        'user_guid' => $user->id,
                        'ip_address' => $request->ip(),
                    ]);
                    return redirect()->intended(route('dashboard.admin'))->with("success", "Log in Successfully");
                } else {
                    return redirect()->intended(route('dashboard.user'))->with("success", "Log in Successfully");
                }
            } else {
                Auth::logout();
                return redirect(route('login'))->with("error", "Your account has been deactivated");
            }
        }

        return redirect(route('login'))->with("error", "The details you entered are incorrect");
    }
}
