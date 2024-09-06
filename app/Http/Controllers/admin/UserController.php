<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = User::select('users.*', 'roles.role_name as role_name', 'organizations.org_name')
                ->join('roles', 'users.role_guid', '=', 'roles.id')
                ->leftjoin('organizations', 'organizations.id', '=', 'users.org_guid')
                ->where('users.id', '!=', Auth::user()->id)
                ->where('users.is_active', '=', 'Y')
                ->get();

            // Format the date and time for each record
            $formatted_data = $data->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d-m-Y');
                return $item;
            });

            return DataTables::of($formatted_data)
                ->addIndexColumn()
                ->make(true);
        }
        $role = Role::all();
        $company = Organization::all();

        return view('admin.user.user-list', [
            'role' => $role,
            'company' => $company,
        ]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'ic_number' => 'required|digits_between:6,12', // Ensure IC number has between 6 to 12 digits
            'nationality' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'role_name' => 'required|string|max:255',
            'org_name' => 'required|string|max:255',
            'race' => 'required|string|max:255',
        ], [
            'email.unique' => 'The email has already been taken.',
            'ic_number.digits_between' => 'The IC number must be between 6 and 12 digits.',
        ]);

        $generatedPassword = Str::random(10);
        // Attempt to create the organization
        User::create([
            'full_name' => $validatedData['full_name'],
            'email' => $validatedData['email'],
            'ic_number' => $validatedData['ic_number'],
            'nationality' => $validatedData['nationality'],
            'gender' => $validatedData['gender'],
            'position' => $validatedData['position'],
            'role_guid' => $validatedData['role_name'],
            'org_guid' => $validatedData['org_name'],
            'race' => $validatedData['race'],
            'password' => Hash::make($generatedPassword),
            'is_active' => 'Y',
        ]);

        // Set a success message
        session()->flash('success', 'New User Successfully added!');

        return redirect()->back();
    }

    public function deactive($id)
    {

        $user = User::find($id);

        $user->is_active = 'N';
        $user->save();

        return response()->json(['success' => true]);
    }

    public function bulk_deactive(Request $request)
    {
        $ids = $request->input('ids');

        // Validate IDs
        if (is_array($ids)) {
            // Perform the deactive operation
            User::whereIn('id', $ids)->update(['is_active' => 'N']);
        }

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        // Fetch the company data from the database
        $user = User::find($id);

        if ($user) {
            return response()->json(['data' => $user]);
        } else {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }

    public function update($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if user was found
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the company details
        $user->update([
            'full_name' => request('full_name'),
            'email' => request('email'),
            'ic_number' => request('ic_number'),
            'nationality' => request('nationality'),
            'gender' => request('gender'),
            'position' => request('position'),
            'role_guid' => request('role_name'),
            'org_guid' => request('org_name'),
            'race' => request('race'),
        ]);


        // Return success response
        return response()->json(['message' => 'User details updated successfully']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'org_name' => 'required',
            'role_name' => 'required',
        ]);

        // Retrieve the parameters
        $orgGuid = $request->input('org_name');
        $roleGuid = $request->input('role_name');

        // Import the Excel file and pass organization and role data
        Excel::import(new UsersImport($orgGuid, $roleGuid), $request->file('file'));
        // Excel::import(new UsersImport, $request->file('file'));
        session()->flash('success', 'New User Successfully added!');
        return response()->json(['success' => true, 'message' => 'Users imported successfully!']);
    }

    public function user_registered()
    {
        return view('email.user-registered');
    }
}
