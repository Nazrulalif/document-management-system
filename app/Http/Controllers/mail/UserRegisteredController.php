<?php

namespace App\Http\Controllers\mail;

use App\Http\Controllers\Controller;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserRegisteredController extends Controller
{
    public function user_registered()
    {
        return view('mail.user-registered');
    }
}
