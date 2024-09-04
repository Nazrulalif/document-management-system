<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect(route('dashboard.admin'));
        }
        return view('session/login');
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
            // dd($user->role_guid);
            if ($user->role_guid == 1) {
                return redirect()->intended(route('dashboard.admin'))->with("success", "Log in Successfully");
            } else {
                return redirect()->intended(route('dashboard.user'))->with("success", "Log in Successfully");
            }
        }

        return redirect(route('login'))->with("error", "Detail error");
    }
}
