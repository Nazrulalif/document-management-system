<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        // Fetch roles with related users, including a count of active users
        $roles = Role::withCount(['users as active_users_count' => function ($query) {
            $query->where('is_active', 'Y');
        }])->get();

        // Loop through each role and explode the role description into list items
        foreach ($roles as $role) {
            $role->listItems = explode("\n", $role->role_description); // Split description by new lines
        }

        // Pass the roles with the list items and active user counts to the view
        return view('admin.role.role-list', [
            'roles' => $roles,
        ]);
    }

    public function show($id)
    {

        $role = Role::find($id);

        if ($role) {
            return response()->json(['data' => $role]);
        } else {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }

    public function update($id)
    {
        // Find the user by ID
        $role = Role::find($id);

        // Check if user was found
        if (!$role) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the company details
        $role->update([
            'role_name' => request('role_name'),
            'role_description' => request('role_description'),
        ]);


        session()->flash('success', 'Role details updated successfully!');
    }

    public function view(Request $request, $uuid)
    {

        $role_user = Role::withCount(['users as active_users_count' => function ($query) {
            $query->where('is_active', 'Y');
        }])->where('uuid', $uuid)->firstOrFail();

        // Explode the role description into list items
        $role_user->listItems = explode("\n", $role_user->role_description);

        $company = Organization::all();
        $role = role::all();

        if ($request->ajax()) {

            $data = User::select('*', 'users.id as id', 'users.uuid as userUUID')
                ->join('roles', 'roles.id', '=', 'users.role_guid')
                ->where('roles.uuid', '=', $uuid)
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

        return view('admin.role.view-role', [
            'role' => $role,
            'company' => $company,
            'role_user' => $role_user,
        ]);
    }
}
