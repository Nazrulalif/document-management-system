<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
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
            // Find the organization by ID and delete it
            $org = Organization::findOrFail($id);
            $org->is_operation = 'N';
            $org->save();

            User::where('org_guid', '=', $id)->update([
                'is_active' => 'N'
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

                $org = Organization::whereIn('id', $ids)->get();
                // Update organizations
                $orgUpdated = Organization::whereIn('id', $ids)->update([
                    'is_operation' => 'N'
                ]);

                // Update users
                $userUpdated = User::whereIn('org_guid', $ids)->update([
                    'is_active' => 'N'
                ]);

                foreach ($org as $org) {
                    AuditLog::create([
                        'action' => "Deactivated",
                        'model' => 'Company',
                        'changes' => $org->org_name, // Log the specific organization's name
                        'user_guid' => Auth::user()->id,
                        'ip_address' => request()->ip(),
                    ]);
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
                    'updated_users' => $userUpdated,
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

            $data = User::select('users.*', 'roles.role_name as role_name', 'organizations.org_name')
                ->join('roles', 'users.role_guid', '=', 'roles.id')
                ->leftjoin('organizations', 'organizations.id', '=', 'users.org_guid')
                ->where('users.id', '!=', Auth::user()->id)
                ->where('organizations.uuid', '=', $uuid)
                ->where('users.is_active', '=', 'Y')
                ->orderBy('users.id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        $fileCount = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $folderCount = Folder::join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $userCount = User::join('organizations', 'organizations.id', '=', 'users.org_guid')
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
        $fileCount = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $folderCount = Folder::join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $userCount = User::join('organizations', 'organizations.id', '=', 'users.org_guid')
            ->where('users.is_active', 'Y')
            ->where('organizations.uuid', $uuid)
            ->count();

        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();

        $query = $request->input('query');

        if ($query) {
            $fileList = Document::select('*', 'documents.created_at as created_at')
                ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
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
                ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
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
        $fileCount = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $folderCount = Folder::join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->where('organizations.uuid', $uuid)
            ->count();

        $userCount = User::join('organizations', 'organizations.id', '=', 'users.org_guid')
            ->where('users.is_active', 'Y')
            ->where('organizations.uuid', $uuid)
            ->count();

        $data = Organization::where('uuid', $uuid)->where('is_operation', 'Y')->firstOrFail();


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
