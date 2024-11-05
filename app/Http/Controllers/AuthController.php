<?php

namespace App\Http\Controllers;

use App\Mail\UserRegistered;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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
                    return redirect()->intended(route('dashboard.admin'))->with("success", "Authentication success");
                } else {
                    AuditLog::create([
                        'action' => 'Login',
                        'model' => 'User',
                        'user_guid' => $user->id,
                        'ip_address' => $request->ip(),
                    ]);
                    return redirect()->intended(route('home.user'))->with("success", "Authentication success");
                }
            } else {
                Auth::logout();
                return redirect(route('login'))->with("error", "Authentication failed, your account not exist in this system. please select another account.");
            }
        }

        return redirect(route('login'))->with("error", "Authentication failed, the details you entered are incorrect.");
    }

    public function azure_redirect()
    {
        return Socialite::with('azure')->redirect();
    }

    public function callbackAzure(Request $request)
    {
        try {
            $azureUser = Socialite::with('azure')->user();

            // Find user by email
            $finduser = User::where('email', $azureUser->getEmail())->first();

            if ($finduser) {
                // Login the user
                Auth::loginUsingId($finduser->id);

                if ($finduser->is_active === "Y") {
                    // Log the login action
                    AuditLog::create([
                        'action' => 'Login',
                        'model' => 'User',
                        'user_guid' => $finduser->id,
                        'ip_address' => $request->ip(),
                    ]);

                    // Redirect based on user role
                    if (in_array($finduser->role_guid, [1, 2, 3])) {
                        return redirect()->intended(route('dashboard.admin'))
                            ->with("success", "Authentication success");
                    } else {
                        return redirect()->intended(route('home.user'))
                            ->with("success", "Authentication success");
                    }
                } else {
                    Auth::guard()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // return redirect(route('login'))
                    //     ->with("error", "Your account has been deactivated");

                    $logoutUrl = "https://login.microsoftonline.com/common/oauth2/v2.0/logout?" .
                        "post_logout_redirect_uri=" . urlencode(route('login'));
                    return redirect($logoutUrl)
                        ->with("error", "Authentication failed, Your account has been deactivated.");
                }
            } else {
                Auth::guard()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                //     return redirect(route('login'))
                //         ->with("error", "Authentication failed, your account not exist in this system. please select another account.");
                $logoutUrl = "https://login.microsoftonline.com/common/oauth2/v2.0/logout?" .
                    "post_logout_redirect_uri=" . urlencode(route('login'));
                return redirect($logoutUrl)
                    ->with("error", "Authentication failed, your account not exist in this system. please select another account.");
            }
        } catch (\Exception $e) {
            // Redirect with an error message
            return redirect(route('login'))
                ->with("error", "Authentication failed, kindly contact the Administrator!");
        }
    }


    public function register_parent()
    {
        if (Auth::check()) {
            return redirect(route('dashboard.admin'));
        }

        $isParentExist = Organization::where('is_parent', 'Y')->first();

        if ($isParentExist) {
            return redirect(route('login'));
        } else {
            return view('session.register-parent-company');
        }
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


        DB::beginTransaction();

        try {
            // Create the organization
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

            // Create the user
            $user = User::create([
                'full_name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'ic_number' => $validatedData['ic_number'],
                'nationality' => $validatedData['nationality'],
                'gender' => $validatedData['gender'],
                'position' => $validatedData['position'],
                'role_guid' => '1',
                'race' => $validatedData['race'],
                'password' => Hash::make($generatedPassword),
                'is_active' => 'Y',
            ]);

            // Create the user_organization link
            $user_org = User_organization::create([
                'org_guid' => $org->id,
                'user_guid' => $user->id,
            ]);

            // Update user and organization counts
            $userCount = User::where('is_active', '=', 'Y')->count();
            $orgCount = Organization::where('is_operation', '=', 'Y')->count();

            // Get today's stat entry or create a new one if it doesn't exist
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                Stat::create([
                    'user_count' => $userCount,
                    'org_count' => $orgCount,
                ]);
            } else {
                $todayStats->update([
                    'user_count' => $userCount,
                    'org_count' => $orgCount,
                ]);
            }

            // Send email notification
            Mail::to($user->email)
                ->later(1, new UserRegistered($user, $generatedPassword));

            // Commit transaction
            DB::commit();

            // Redirect with success message
            return redirect()->intended(route('register.parent.success'))->with("success", "Your Account Successfully Registered!");
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();
            return redirect()->intended(route('register.parent'))->with("error", "Registration Failed, Please try again.");
        }
    }

    public function register_success()
    {
        return view('session.successfully-registered');
    }
}
