<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('session.forgot-password-form');
    }

    public function email_verify(Request $request)
    {
        // Validate the email exists in the users table
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'The email does not exist in our records.'
        ]);

        // First, delete any expired tokens older than 60 minutes
        PasswordResetToken::where('created_at', '<', now()->subMinutes(60))->delete();

        // Check if an email has already been sent (exists in password_reset_tokens table)
        if (PasswordResetToken::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'The reset link has already been sent to this email!']);
        }

        // Generate a token and save it
        $token = Str::random(64);

        PasswordResetToken::create([
            'email' => $request->email,
            'token' => $token,
        ]);

        // Send email with a delay of 3 seconds
        Mail::to($request->email)->later(now()->addSeconds(1), new ResetPassword($token));

        return redirect()->back()->with('success', 'Reset password email sent successfully.');
    }


    public function reset_password($token)
    {
        return view('session.reset-password-form', compact('token'));
    }

    public function reset_password_post(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);



        // Check if the reset token exists
        $updatePassword = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$updatePassword) {
            return redirect()->back()->with('error', 'Invalid email.');
        }

        // Update the user's password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the password reset token
        PasswordResetToken::where('email', $request->email)->delete();

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }
}
