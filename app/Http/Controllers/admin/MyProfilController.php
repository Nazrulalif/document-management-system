<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MyProfilController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $data = User::join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.id', $userId)
            ->first();

        $org_list = User_organization::join('organizations', 'organizations.id', '=', 'user_organizations.org_guid')
            ->where('user_organizations.user_guid', $userId)
            ->get();


        $fileCount =  Document::where('upload_by', $userId)->count();
        $folderCount = Folder::where('Created_by', $userId)->count();

        return view('admin.my-profile.detail', compact('data', 'fileCount', 'folderCount', 'org_list'));
    }

    public function file(Request $request)
    {
        $userId = Auth::user()->id;
        $data = User::with('organizations')
            ->join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.id', $userId)
            ->first();

        $fileCount =  Document::where('upload_by', $userId)->count();
        $folderCount = Folder::where('Created_by', $userId)->count();


        $query = $request->input('query');

        if ($query) {
            $fileList = Document::where('upload_by', $userId)
                ->where(function ($q) use ($query) {
                    $q->where('doc_title', 'LIKE', "%{$query}%")
                        ->orWhere('doc_type', 'LIKE', "%{$query}%");
                })
                ->paginate(12);
        } else {
            $fileList = Document::where('upload_by', $userId)->paginate(12);
        }

        return view('admin.my-profile.file-list', compact(
            'data',
            'fileCount',
            'folderCount',
            'fileList',
        ));
    }

    public function setting()
    {
        $userId = Auth::user()->id;
        $data = User::with('organizations')
            ->join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.id', $userId)
            ->first();

        $fileCount =  Document::where('upload_by', $userId)->count();
        $folderCount = Folder::where('Created_by', $userId)->count();

        $org_list = User_organization::join('organizations', 'organizations.id', '=', 'user_organizations.org_guid')
            ->where('user_organizations.user_guid', $userId)
            ->get();


        return view('admin.my-profile.setting', compact(
            'data',
            'fileCount',
            'folderCount',
            'org_list',
        ));
    }

    public function setting_post(Request $request)
    {
        // Get the current user ID
        $userId = Auth::user()->id;

        // Validation rules
        $request->validate([
            'full_name' => 'required|string|max:255',
            'ic_number' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $userId, // Ensures email is unique, except for the current user
            'race' => 'required|string|max:50',
            'nationality' => 'required|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Get the user data
        $user = User::findOrFail($userId);

        // Handle profile picture removal
        if ($request->input('remove_avatar') == 1) {
            // Delete the old picture if it exists
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture); // Delete from SFTP
            }

            // Set profile picture to null in the database
            $user->profile_picture = null;
        } elseif ($request->hasFile('profile_picture')) {
            // Handle profile picture upload
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture); // Delete old picture from SFTP
            }

            // Upload the new profile picture
            $file = $request->file('profile_picture');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file on the SFTP server
            $filePath = 'uploads/profile-picture/' . $uniqueFileName;
            Storage::put($filePath, file_get_contents($file));
            $user->profile_picture = $filePath;
        }

        // Update the user's information
        $user->update([
            'full_name' => $request->full_name,
            'ic_number' => $request->ic_number,
            'email' => $request->email,
            'race' => $request->race,
            'nationality' => $request->nationality,
        ]);

        // Return success message
        return redirect()->back()->with(['success' => 'Your profile details updated successfully']);
    }


    public function change_password(Request $request)
    {
        // Validation rules with confirmation
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

        return redirect()->back()->with('success', 'Your password has been changed successfully.');
    }
}
