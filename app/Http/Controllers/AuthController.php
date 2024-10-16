<?php

namespace App\Http\Controllers;

use App\Mail\UserRegistered;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect(route('dashboard.admin'));
        }
        $isParentExist = Organization::where('is_parent', 'Y')->first();


        return view('session/login-form', compact('isParentExist'));
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
                    AuditLog::create([
                        'action' => 'Login',
                        'model' => 'User',
                        'user_guid' => $user->id,
                        'ip_address' => $request->ip(),
                    ]);
                    return redirect()->intended(route('home.user'))->with("success", "Log in Successfully");
                }
            } else {
                Auth::logout();
                return redirect(route('login'))->with("error", "Your account has been deactivated");
            }
        }

        return redirect(route('login'))->with("error", "The details you entered are incorrect");
    }

    public function register_parent()
    {
        return view('session.register-parent-company');
    }

    public function register_parent_post(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_address' => 'required|string|max:255',
            'org_place' => 'required|string|max:255',
            'nature_of_business' => 'required|string|max:255',
            'org_number' => 'required|string|max:255',
            'reg_date' => 'required|date',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'ic_number' => 'required|digits_between:6,12', // Ensure IC number has between 6 to 12 digits
            'nationality' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'org_name' => 'required|string|max:255',
            'race' => 'required|string|max:255',
        ], [
            'email.unique' => 'The email has already been taken.',
            'ic_number.digits_between' => 'The IC number must be between 6 and 12 digits.',
        ]);

        // Attempt to create the organization
        $org = Organization::create([
            'org_name' => $validatedData['org_name'],
            'reg_date' => $validatedData['reg_date'],
            'org_address' => $validatedData['org_address'],
            'org_place' => $validatedData['org_place'],
            'nature_of_business' => $validatedData['nature_of_business'],
            'org_number' => $validatedData['org_number'],
            'is_parent' => 'Y',
            'is_operation' => 'Y',
        ]);

        $generatedPassword = Str::random(10);
        // Attempt to create the organization
        $user = User::create([
            'full_name' => $validatedData['full_name'],
            'email' => $validatedData['email'],
            'ic_number' => $validatedData['ic_number'],
            'nationality' => $validatedData['nationality'],
            'gender' => $validatedData['gender'],
            'position' => $validatedData['position'],
            'role_guid' => '1',
            'org_guid' => $org->id,
            'race' => $validatedData['race'],
            'password' => Hash::make($generatedPassword),
            'is_active' => 'Y',
        ]);

        // Get current counts
        $userCount = User::where('is_active', '=', 'Y')->count();
        $orgCount = Organization::where('is_operation', '=', 'Y')->count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'user_count' => $userCount,
                'org_count' => $orgCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'user_count' => $userCount,
                'org_count' => $orgCount,
            ]);
        }

        Mail::to($user->email)
            ->later(10, new UserRegistered($user, $generatedPassword));

        // Set a success message

        return redirect()->intended(route('register.parent.success'))->with("success", "Your Account Successfully Register!");
    }

    public function register_success()
    {
        return view('session.successfully-registered');
    }
}
