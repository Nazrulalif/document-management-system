<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
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

    public function view($uuid)
    {
        $data = Organization::where('uuid', $uuid)->firstOrFail();
        return view('admin.company.view-company', [
            'data' => $data,
        ]);
    }
}
