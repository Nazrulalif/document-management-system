<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index(Request $request)
    {


        // if (Auth::user()->role_guid != 1) {
        //     return redirect(route('dashboard.admin'));
        // }

        // $company = Organization::all(); // Fetch all users from the database
        if ($request->ajax()) {

            $data = Organization::where('is_operation', '=', 'Y')
                ->orderBy('id', 'DESC')
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


        return view('admin.company.company-list');
    }

    public function show($id)
    {
        // Fetch the company data from the database
        $company = Organization::find($id);

        if ($company) {
            return response()->json(['data' => $company]);
        } else {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // Find the company by ID
        $company = Organization::find($id);

        // Check if company was found
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Update the company details
        $company->update([
            'org_name' => request('org_name'),
            'org_address' => request('org_address'),
            'org_place' => request('org_place'),
            'nature_of_business' => request('nature_of_business'),
            'org_number' => request('org_number'),
            'reg_date' => request('reg_date'),
        ]);


        // Return success response
        return response()->json(['message' => 'Company details updated successfully']);
    }

    public function destroy($id)
    {
        try {
            // Find the organization by ID and deactivate it
            $org = Organization::findOrFail($id);
            $org->is_operation = 'N';
            $org->save();

            // Get the user IDs associated with the organization
            $userIds = User_organization::where('org_guid', '=', $id)->pluck('user_guid');


            // Check if users are associated with other organizations
            foreach ($userIds as $userId) {
                // Count the number of active organizations associated with this user
                $activeOrgCount = User_organization::where('user_guid', $userId)
                    ->join('organizations', 'user_organizations.org_guid', '=', 'organizations.id')
                    ->where('organizations.is_operation', '=', 'Y') // Check only active organizations
                    ->count();

                // If the user has no other active organizations, deactivate the user account
                if ($activeOrgCount == 0) { // means this is the only company they belong to
                    User::where('id', $userId)->update(['is_active' => 'N']);
                }
            }

            // Log the action
            AuditLog::create([
                'action' => 'Deactivated',
                'model' => 'Company',
                'changes' => $org->org_name,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
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

            // Return a JSON response indicating success
            return response()->json([
                'success' => true,
                'message' => 'Company successfully deleted!'
            ]);
        } catch (\Exception $e) {
            // Return a JSON response indicating an error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the company.'
            ], 500);
        }
    }



    public function bulk_destroy(Request $request)
    {
        $ids = $request->input('ids');

        // Validate IDs
        if (is_array($ids) && !empty($ids)) {
            // Ensure IDs are numeric
            $ids = array_filter($ids, 'is_numeric');

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No valid numeric IDs provided.']);
            }

            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($ids) {
                // Retrieve organizations to deactivate
                $orgs = Organization::whereIn('id', $ids)->get();

                // Update organizations to inactive
                $orgUpdated = Organization::whereIn('id', $ids)->update([
                    'is_operation' => 'N'
                ]);

                // Get user IDs associated with the organizations to deactivate
                $userIds = User_organization::whereIn('org_guid', $ids)->pluck('user_guid');


                foreach ($orgs as $org) {
                    AuditLog::create([
                        'action' => "Deactivated",
                        'model' => 'Company',
                        'changes' => $org->org_name, // Log the specific organization's name
                        'user_guid' => Auth::user()->id,
                        'ip_address' => request()->ip(),
                    ]);
                }

                // Check if any users should be deactivated based on their remaining organizations
                foreach ($userIds as $userId) {
                    // Count active organizations associated with this user
                    $activeOrgCount = User_organization::where('user_guid', $userId)
                        ->join('organizations', 'user_organizations.org_guid', '=', 'organizations.id')
                        ->where('organizations.is_operation', '=', 'Y') // Only count active organizations
                        ->count();

                    // If the user has no active organizations, deactivate the user account
                    if ($activeOrgCount == 0) {
                        User::where('id', $userId)->update(['is_active' => 'N']);
                    }
                }

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

                return response()->json([
                    'success' => true,
                    'updated_organizations' => $orgUpdated,
                    'updated_users' => $userIds->count(), // Return the count of users affected
                ]);
            });
        }

        return response()->json(['success' => false, 'message' => 'No valid IDs provided.']);
    }




    public function create(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_address' => 'required|string|max:255',
            'org_place' => 'required|string|max:255',
            'nature_of_business' => 'required|string|max:255',
            'org_number' => 'required|string|max:255',
            'reg_date' => 'required|date',
        ]);

        // Attempt to create the organization
        Organization::create([
            'org_name' => $validatedData['org_name'],
            'reg_date' => $validatedData['reg_date'],
            'org_address' => $validatedData['org_address'],
            'org_place' => $validatedData['org_place'],
            'nature_of_business' => $validatedData['nature_of_business'],
            'org_number' => $validatedData['org_number'],
            'is_parent' => 'N',
            'is_operation' => 'Y',
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

        // Set a success message
        session()->flash('success', 'New Company Successfully added!');

        // Redirect back to the form
        return redirect()->back();
    }

    public function view(Request $request, $uuid)
    {
        if ($request->ajax()) {
            // Fetch users who belong to the specified organization
            $data = User::with('organizations')
                ->select('users.*', 'roles.role_name as role_name')
                ->join('roles', 'users.role_guid', '=', 'roles.id')
                ->join('user_organizations', 'users.id', '=', 'user_organizations.user_guid') // Join with user_organizations table
                ->join('organizations', 'user_organizations.org_guid', '=', 'organizations.id') // Join with organizations table
                ->where('users.id', '!=', Auth::user()->id) // Exclude the current user
                ->where('organizations.uuid', '=', $uuid) // Filter by organization UUID
                ->where('users.is_active', '=', 'Y') // Only include active users
                ->orderBy('users.id', 'DESC') // Order by user ID descending
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }


        // Retrieve the organization data based on UUID and operational status
        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        // Count documents associated with the organization identified by the UUID
        $fileCount = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
            ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count folders associated with the organization identified by the UUID
        $folderCount = Folder::join('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
            ->join('organizations', 'organizations.id', '=', 'shared_folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count active users associated with the organization identified by the UUID
        $userCount = User::join('user_organizations', 'user_organizations.user_guid', '=', 'users.id')
            ->join('organizations', 'organizations.id', '=', 'user_organizations.org_guid') // Join organizations through user_organizations
            ->where('users.is_active', 'Y')
            ->where('organizations.uuid', $uuid)
            ->count();


        return view('admin.company.user-list', compact(
            'data',
            'fileCount',
            'folderCount',
            'userCount',
        ));
    }

    public function file(Request $request, $uuid)
    {
        // Retrieve the organization data based on UUID and operational status
        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        // Count documents associated with the organization identified by the UUID
        $fileCount = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
            ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count folders associated with the organization identified by the UUID
        $folderCount = Folder::join('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
            ->join('organizations', 'organizations.id', '=', 'shared_folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count active users associated with the organization identified by the UUID
        $userCount = User::join('user_organizations', 'user_organizations.user_guid', '=', 'users.id')
            ->join('organizations', 'organizations.id', '=', 'user_organizations.org_guid') // Join organizations through user_organizations
            ->where('users.is_active', 'Y')
            ->where('organizations.uuid', $uuid)
            ->count();

        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        $query = $request->input('query');

        if ($query) {
            $fileList = Document::select('*', 'documents.created_at as created_at')
                ->join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->join('users', 'users.id', '=', 'documents.upload_by')
                ->where(function ($q) use ($query) {
                    $q->where('documents.doc_title', 'LIKE', "%{$query}%")
                        ->orWhere('documents.doc_type', 'LIKE', "%{$query}%")
                        ->orWhere('users.full_name', 'LIKE', "%{$query}%");
                })
                ->where('organizations.uuid', $uuid)
                ->paginate(8);
        } else {
            $fileList = Document::select('*', 'documents.created_at as created_at')
                ->join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->join('users', 'users.id', '=', 'documents.upload_by')
                ->where('organizations.uuid', $uuid)
                ->paginate(8);
        }


        return view('admin.company.file-list', compact(
            'data',
            'fileCount',
            'folderCount',
            'userCount',
            'fileList',
        ));
    }

    public function setting(Request $request, $uuid)
    {
        // Retrieve the organization data based on UUID and operational status
        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        // Count documents associated with the organization identified by the UUID
        $fileCount = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
            ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count folders associated with the organization identified by the UUID
        $folderCount = Folder::join('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
            ->join('organizations', 'organizations.id', '=', 'shared_folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        // Count active users associated with the organization identified by the UUID
        $userCount = User::join('user_organizations', 'user_organizations.user_guid', '=', 'users.id')
            ->join('organizations', 'organizations.id', '=', 'user_organizations.org_guid') // Join organizations through user_organizations
            ->where('users.is_active', 'Y')
            ->where('organizations.uuid', $uuid)
            ->count();


        return view('admin.company.setting', compact(
            'data',
            'fileCount',
            'folderCount',
            'userCount',
        ));
    }

    public function setting_post(Request $request, $uuid)
    {
        // Validation rules
        $request->validate([
            'org_name' => 'required|string|max:255',
            'reg_date' => 'required',
            'org_number' => 'required',
            'org_address' => 'required',
            'org_place' => 'required',
            'nature_of_business' => 'required',
            'org_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Get the user data
        $data = Organization::where('uuid', $uuid)->firstOrFail();

        // Handle profile picture removal
        if ($request->input('remove_avatar') == 1) {
            // Delete the old picture if it exists
            if ($data->profile_picture) {
                Storage::delete('public/' . $data->org_logo);
            }

            // Set profile picture to null in the database
            $data->org_logo = null;
        } elseif ($request->hasFile('org_logo')) {
            // Handle profile picture upload
            if ($data->org_logo) {
                Storage::delete('public/' . $data->org_logo);
            }

            $file = $request->file('org_logo');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the new picture
            $filePath = $file->storeAs('uploads/company-logo', $uniqueFileName, 'public');
            $data->org_logo = $filePath;
        }

        // Update the user's information
        $data->update([
            'org_name' => $request->org_name,
            'reg_date' => $request->reg_date,
            'org_number' => $request->org_number,
            'org_address' => $request->org_address,
            'org_place' => $request->org_place,
            'nature_of_business' => $request->nature_of_business,
        ]);

        if (isset($filePath)) {
            $data->org_logo = $filePath;
            $data->save();
        }

        return redirect()->back()->with(['success' => 'Your profile details updated successfully']);
    }

    public function deactivate($uuid)
    {

        try {
            // Find the organization by ID and delete it
            $org = Organization::where('uuid', $uuid)->firstOrFail();
            $org->is_operation = 'N';
            $org->save();

            User::join('organizations', 'organizations.id', '=', 'org_guid')
                ->where('organizations.uuid', '=', $uuid)->update([
                    'users.is_active' => 'N'
                ]);

            AuditLog::create([
                'action' => 'Deactivated',
                'model' => 'Company',
                'changes' =>  $org->org_name,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
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

            // Return a JSON response indicating success
            return response()->json([
                'success' => true,
                'message' => 'Company successfully deleted!'
            ]);
        } catch (\Exception $e) {
            // Return a JSON response indicating an error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the company.'
            ], 500);
        }
    }
}
