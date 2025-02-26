<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Mail\UserRegistered;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Stat;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if (Auth::user()->role_guid == 1) {
                $data = User::select('users.*', 'roles.role_name as role_name')
                    ->join('roles', 'users.role_guid', '=', 'roles.id')
                    ->where('users.id', '!=', Auth::user()->id)
                    ->where('users.is_active', '=', 'Y')
                    ->orderBy('users.id', 'DESC')
                    ->with(['organizations' => function ($query) {
                        $query->where('is_operation', 'Y'); // Only include active organizations
                    }])
                    ->get();
            } else {
                $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

                $data = User::select('users.*', 'roles.role_name as role_name')
                    ->join('roles', 'users.role_guid', '=', 'roles.id')
                    ->where('users.id', '!=', Auth::user()->id)
                    ->where('users.is_active', '=', 'Y')
                    ->whereHas('organizations', function ($query) use ($user_orgs) {
                        $query->whereIn('organizations.id', $user_orgs); // Check if user belongs to any of the user's organizations
                    })
                    ->orderBy('users.id', 'DESC')
                    ->with('organizations') // Assuming the relationship is defined in the User model
                    ->get();
            }



            // Format the date and time for each record
            $formatted_data = $data->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d-m-Y');
                $item->company_list = $item->organizations->pluck('org_name')->implode('<br>'); // Concatenate organization names
                return $item;
            });

            return DataTables::of($formatted_data)
                ->addIndexColumn()
                ->addColumn('company_list', function ($row) {
                    return $row->company_list; // This will display the list of companies with line breaks
                })
                ->rawColumns(['company_list']) // Enable HTML rendering for this column
                ->make(true);
        }

        $role = Role::where('id', '!=', '1')->get();

        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        if (Auth::user()->role_guid == 1) {
            $company = Organization::where('is_operation', '=', 'Y')->get();
        } else {
            $company = Organization::where('is_operation', '=', 'Y')->whereIn('id', $user_orgs)->get();
        }

        return view('admin.user.user-list', [
            'role' => $role,
            'company' => $company,
        ]);
    }
    private function generateSecurePassword($length = 10)
    {
        $uppercase = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, length: 2); // 2 uppercase letters
        $lowercase = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3); // 3 lowercase letters
        $numbers = substr(str_shuffle('0123456789'), 0, 2); // 2 numbers
        $specialChars = substr(str_shuffle('!@#$%^&*()_-+=<>?'), 0, 2); // 2 special characters
        $remaining = Str::random($length - 9); // Remaining random characters
    
        // Shuffle and return the password
        return str_shuffle($uppercase . $lowercase . $numbers . $specialChars . $remaining);
    }
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role_name' => 'required|string|max:255',
            'org_name' => 'required|array',           // Ensure org_name is an array
            'org_name.*' => 'exists:organizations,id',
        ], [
            'email.unique' => 'The email has already been taken.',
            // 'ic_number.digits_between' => 'The IC number must be between 6 and 12 digits.',
        ]);

        try {
            $generatedPassword = $this->generateSecurePassword();
            // Attempt to create the organization
            $user = User::create([
                'full_name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'ic_number' => $request->ic_number,
                'nationality' => $request->nationality ?? 'not set',
                'gender' => $request->gender,
                'position' => $request->position ?? 'not set',
                'role_guid' => $request->role_name,
                'race' => $request->race ?? 'not set',
                'password' => Hash::make($generatedPassword),
                'password_changed_at' => Carbon::now(),
                'is_active' => 'Y',
                'is_change_password' => 'N',

            ]);

            foreach ($validatedData['org_name'] as $org_name) {
                User_organization::create([
                    'user_guid' => $user->id,
                    'org_guid' => $org_name,
                ]);
            }

            // Get current counts
            $userCount = User::where('is_active', '=', 'Y')->count();
            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'user_count' => $userCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'user_count' => $userCount,
                ]);
            }

            Mail::to($user->email)
                ->later(1, new UserRegistered($user, $generatedPassword));

            // Set a success message
            session()->flash('success', 'New User Successfully added!');

            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', 'Registration failed, please try again.');

            return redirect()->back();
        }
    }

    public function deactive($id)
    {

        $user = User::find($id);

        $user->is_active = 'N';
        $user->save();

        // Get current counts
        $userCount = User::where('is_active', '=', 'Y')->count();
        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'user_count' => $userCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'user_count' => $userCount,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function bulk_deactive(Request $request)
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

                $userList = User::whereIn('id', $ids)->get();
                // Update organizations
                $userUpdated = User::whereIn('id', $ids)->update([
                    'is_active' => 'N'
                ]);

                foreach ($userList as $userList) {

                    AuditLog::create([
                        'action' => "Deactivated",
                        'model' => 'User',
                        'changes' => $userList->full_name,
                        'user_guid' => Auth::user()->id,
                        'ip_address' => request()->ip(),
                    ]);
                }

                // Get current counts
                $userCount = User::where('is_active', '=', 'Y')->count();
                // Get today's stat entry (or create a new one if it doesn't exist)
                $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

                if (!$todayStats) {
                    // Create a new stat entry for today if it doesn't exist
                    Stat::create([
                        'user_count' => $userCount,
                    ]);
                } else {
                    // Update today's counts if the entry already exists
                    $todayStats->update([
                        'user_count' => $userCount,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'updated_users' => $userUpdated,
                ]);
            });
        }

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        // Fetch the company data from the database
        $user = User::with('organizations')->find($id);

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

        // Get current organizations associated with the user
        $currentOrganizations = User_organization::where('user_guid', $user->id)->pluck('org_guid')->toArray();

        // Get the new organizations from the request
        $newOrganizations = request('org_name', []); // Default to an empty array if not provided

        // Update the user details
        $user->update([
            'full_name' => request('full_name'),
            'email' => request('email'),
            'ic_number' => request('ic_number'),
            'nationality' => request('nationality') ?? 'not set',
            'gender' => request('gender'),
            'position' => request('position') ?? 'not set',
            'role_guid' => request('role_guid'),
            'race' => request('race') ?? 'not set',
        ]);

        // Update user_organizations:
        // 1. Delete organizations that are no longer associated
        foreach ($currentOrganizations as $org) {
            if (!in_array($org, $newOrganizations)) {
                User_organization::where('user_guid', $user->id)->where('org_guid', $org)->delete();
            }
        }

        // 2. Add new organizations that are not already associated
        foreach ($newOrganizations as $org) {
            if (!in_array($org, $currentOrganizations)) {
                User_organization::create([
                    'user_guid' => $user->id,
                    'org_guid' => $org,
                ]);
            }
        }

        // Return success response
        return response()->json(['message' => 'User details updated successfully']);
    }


    public function downloadTemplate()
    {
        $userOrgIds = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        if (Auth::user()->role_guid == 1) {
            // Fetch companies and roles from the database
            $companies = Organization::where('is_operation', '=', 'Y')->pluck('org_name')->toArray(); // Fetch company names
        } else {
            // Fetch companies and roles from the database
            $companies = Organization::where('is_operation', '=', 'Y')->whereIn('id', $userOrgIds)->pluck('org_name')->toArray(); // Fetch company names
        }
        $roles = Role::where('id', '!=', '1')->pluck('role_name')->toArray();            // Fetch role names


        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the main sheet
        $sheet->setCellValue('A1', 'full_name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'ic_number');
        $sheet->setCellValue('D1', 'gender');
        $sheet->setCellValue('E1', 'nationality');
        $sheet->setCellValue('F1', 'race');
        $sheet->setCellValue('G1', 'org_guid');  // Dropdown for company name
        $sheet->setCellValue('H1', 'position');
        $sheet->setCellValue('I1', 'role_guid');   // Dropdown for system role

        // Create a hidden sheet for company and role data
        $hiddenSheet = $spreadsheet->createSheet();
        $hiddenSheet->setTitle('DropdownData');

        // Populate hidden sheet with company names
        $row = 1;
        foreach ($companies as $company) {
            $hiddenSheet->setCellValue('A' . $row, $company);
            $row++;
        }

        // Populate hidden sheet with role names
        $row = 1;
        foreach ($roles as $role) {
            $hiddenSheet->setCellValue('B' . $row, $role);
            $row++;
        }

        // Define the range for the dropdown values (company names and role names)
        $companyRange = 'DropdownData!$A$1:$A$' . count($companies);
        $roleRange = 'DropdownData!$B$1:$B$' . count($roles);

        // Set data validation (dropdown) for Company (Column G)
        for ($row = 2; $row <= 100; $row++) {
            $companyCell = 'G' . $row;
            $validation = $sheet->getCell($companyCell)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($companyRange);
            $sheet->getCell($companyCell)->setDataValidation($validation);
        }

        // Set data validation (dropdown) for Role (Column I)
        for ($row = 2; $row <= 100; $row++) {
            $roleCell = 'I' . $row;
            $validation = $sheet->getCell($roleCell)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($roleRange);
            $sheet->getCell($roleCell)->setDataValidation($validation);
        }

        // Hide the dropdown data sheet (DropdownData)
        $spreadsheet->setActiveSheetIndex(0);
        $hiddenSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Save the file as a downloadable Excel file
        $fileName = 'user_registration_template.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Output the file to the browser for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        $writer->save('php://output');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        // Get current counts
        $userCount = User::where('is_active', '=', 'Y')->count();
        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'user_count' => $userCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'user_count' => $userCount,
            ]);
        }

        session()->flash('success', 'New User Successfully added!');
        return response()->json(['success' => true, 'message' => 'Users imported successfully!']);
    }

    public function view($uuid)
    {

        // $userId = Auth::user()->id;
        $data = User::with('organizations')
            ->select('*', 'users.uuid as uuid')
            ->join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.uuid', '=', $uuid)
            ->first();

        $fileCount =  Document::join('users', 'users.id', '=', 'documents.upload_by')
            ->where('users.uuid', $uuid)->count();
        $folderCount =  Folder::join('users', 'users.id', '=', 'folders.created_by')
            ->where('users.uuid', $uuid)->count();

        return view('admin.user.detail', compact(
            'fileCount',
            'folderCount',
            'data',
        ));
    }

    public function setting($uuid)
    {
        // $userId = Auth::user()->id;
        $data = User::with('organizations')
            ->select('*', 'users.uuid as uuid', 'users.id as id')
            ->join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.uuid', '=', $uuid)
            ->first();

        $fileCount =  Document::join('users', 'users.id', '=', 'documents.upload_by')
            ->where('users.uuid', $uuid)->count();
        $folderCount =  Folder::join('users', 'users.id', '=', 'folders.created_by')
            ->where('users.uuid', $uuid)->count();
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        if (Auth::user()->role_guid == 1) {
            $org = Organization::where('is_operation', '=', 'Y')->get();
        } else {
            $org = Organization::where('is_operation', '=', 'Y')->whereIn('id', $user_orgs)->get();
        }


        return view('admin.user.setting', compact(
            'fileCount',
            'folderCount',
            'data',
            'org',
        ));
    }

    public function user_setting_post(Request $request, $id)
    {
        // Validation rules
        $request->validate([
            'full_name' => 'required|string|max:255',
            'ic_number' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $id, // Ensures email is unique, except for the current user
            'race' => 'required|string|max:50',
            'nationality' => 'required|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Get the user data
        $user = User::findOrFail($id);

        // Update the user's information
        $user->update([
            'full_name' => $request->full_name,
            'ic_number' => $request->ic_number,
            'email' => $request->email,
            'race' => $request->race,
            'nationality' => $request->nationality,
        ]);

        // Get currently selected organizations
        $selectedOrganizations = $request->org_name;

        // Get current user organization IDs from the User_organization table
        $currentOrganizations = User_organization::where('user_guid', $user->id)->pluck('org_guid')->toArray();

        // Find organizations to add and remove
        $organizationsToAdd = array_diff($selectedOrganizations, $currentOrganizations);
        $organizationsToRemove = array_diff($currentOrganizations, $selectedOrganizations);

        // Delete deselected organizations
        User_organization::where('user_guid', $user->id)
            ->whereIn('org_guid', $organizationsToRemove)
            ->delete();

        // Add newly selected organizations
        foreach ($organizationsToAdd as $orgId) {
            User_organization::create([
                'user_guid' => $user->id,
                'org_guid' => $orgId,
            ]);
        }

        return redirect()->back()->with(['success' => 'Your profile details updated successfully']);
    }


    public function file(Request $request, $uuid)
    {
        $data = User::select('*', 'users.uuid as uuid', 'users.id as id')
            ->join('roles', 'roles.id', '=', 'users.role_guid')
            ->where('users.uuid', '=', $uuid)
            ->first();

        $fileCount =  Document::join('users', 'users.id', '=', 'documents.upload_by')
            ->where('users.uuid', $uuid)->count();
        $folderCount =  Folder::join('users', 'users.id', '=', 'folders.created_by')
            ->where('users.uuid', $uuid)->count();


        $query = $request->input('query');

        if ($query) {
            $fileList = Document::join('users', 'users.id', '=', 'documents.upload_by')
                ->where(function ($q) use ($query) {
                    $q->where('documents.doc_title', 'LIKE', "%{$query}%")
                        ->orWhere('documents.doc_type', 'LIKE', "%{$query}%");
                })
                ->where('users.uuid', $uuid)
                ->paginate(12);
        } else {
            $fileList = Document::join('users', 'users.id', '=', 'documents.upload_by')
                ->where('users.uuid', $uuid)->paginate(12);
        }
        return view('admin.user.file-list', compact(
            'fileCount',
            'folderCount',
            'data',
            'fileList',
        ));
    }

    public function setting_deactive($uuid)
    {
        $user = User::where('uuid', '=', $uuid)->firstOrFail();

        $user->is_active = 'N';
        $user->save();

        // Get current counts
        $userCount = User::where('is_active', '=', 'Y')->count();
        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'user_count' => $userCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'user_count' => $userCount,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
