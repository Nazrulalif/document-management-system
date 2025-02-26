<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;

class ExpiredPasswordController extends Controller
{
    public function index(Request $request){
        return view('session.expired-password-form');
    }

    public function update(Request $request){
          // Validate the input
          $request->validate([
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                'confirmed', // Must match password_confirmation
                'regex:/[A-Z]/', // At least one uppercase letter
                'regex:/[a-z]/', // At least one lowercase letter
                'regex:/[0-9]/', // At least one number
                'regex:/[!@#$%^&*()\-_=+{};:,<.>]/', // At least one special character
            ],
            'password_confirmation' => 'required',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->password_changed_at = Carbon::now();
        $user->is_change_password = 'Y';
        $user->save();

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }

}
