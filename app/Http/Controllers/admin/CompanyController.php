<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        // $company = Organization::all(); // Fetch all users from the database
        if ($request->ajax()) {

            $data = Organization::all();

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

    public function update($id)
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

        // return response()->json(['success' => $id]);
        try {
            // Find the organization by ID and delete it
            $org = Organization::findOrFail($id);
            $org->delete();

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
        if (is_array($ids)) {
            // Perform the delete operation
            Organization::whereIn('id', $ids)->delete();
        }

        return response()->json(['success' => true]);
    }

    public function create(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_address' => 'required|string|max:255',
            'org_place' => 'required|string|max:255',
            'nature_of_business' => 'required|string|max:255',
            'org_number' => 'required|string|max:255|unique:organizations,org_number',
            'reg_date' => 'required|date',
        ], [
            'org_number.unique' => 'The company registration number have taken.',
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
