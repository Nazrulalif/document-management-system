<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Organization;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Retrieve the organization IDs the user is associated with
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        // Check if the user is an admin (role_guid == 1) to retrieve all organizations, otherwise limit to user's organizations
        $organization = Organization::where('is_operation', 'Y')
            ->when(Auth::user()->role_guid != 1, function ($query) use ($user_orgs) {
                $query->whereIn('id', $user_orgs);
            })
            ->get();

        return view('admin.report.report', compact('organization'));
    }

    public function post(Request $request)
    {
        // Retrieve the validated input data
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $orgNames = $request->input('org_name', []);
        $contentOptions = $request->input('content', []);
        $allCompanies = $request->input('all_company', false);

        $totalCurrentStorage = $this->calculateTotalStorage();
        $totalSpace = $this->getAvailableStorage();

        // Initialize document statistics
        $docStat_pdf = 0;
        $docStat_docx = 0;
        $docStat_pptx = 0;
        $docStat_images = 0;
        $docStat_excel = 0;
        $docStat_total = 0;
        $formatted_startDate = Carbon::parse($request->input('start_date'))->format('j F Y');
        $formatted_endDate = Carbon::parse($request->input('end_date'))->format('j F Y');

        if ($allCompanies == false) {
            // Filter for specific organizations selected
            if (!empty($orgNames)) {
                $docStat_pdf = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->where('documents.doc_type', '=', 'pdf')
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();


                // Count DOCX documents
                $docStat_docx = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->whereIn('documents.doc_type', ['docx', 'doc']) // Handle both docx and doc types
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();


                // Count PPTX documents
                $docStat_pptx = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->where('documents.doc_type', '=', 'pptx')
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();


                // Count Image documents
                $docStat_images = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->whereIn('documents.doc_type', ['jpeg', 'jpg', 'png', 'gif', 'bmp']) // List of image types
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();

                // Count Excel documents
                $docStat_excel = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->whereIn('documents.doc_type', ['xlsx', 'csv'])
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();

                // Count Total documents
                $docStat_total = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->where(function ($query) use ($orgNames, $startDate, $endDate) {
                        $query->whereIn('shared_documents.org_guid', $orgNames)
                            ->whereBetween('documents.created_at', [$startDate, $endDate]);
                    })
                    ->count();


                $user_login = DB::table('audit_logs')
                    ->select(
                        'users.full_name',              // User name
                        'roles.role_name',              // Role name
                        'audit_logs.action',            // Action (Login/Logout)
                        'audit_logs.ip_address',        // IP Address
                        'audit_logs.created_at'         // Timestamp
                    )
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')          // Join audit logs with users
                    ->join('roles', 'roles.id', '=', 'users.role_guid')               // Join users with roles
                    ->join('user_organizations', 'user_organizations.user_guid', '=', 'users.id') // Join user organizations
                    ->whereIn('user_organizations.org_guid', $orgNames)               // Filter by organization from user_organizations
                    ->whereIn('audit_logs.action', ['Login', 'Logout'])               // Filter for login/logout actions
                    ->whereBetween('audit_logs.created_at', [$startDate, $endDate])   // Filter by date range
                    ->groupBy('users.full_name', 'roles.role_name', 'audit_logs.action', 'audit_logs.ip_address', 'audit_logs.created_at') // Group to remove duplicates
                    ->orderBy('audit_logs.created_at', 'desc')                        // Order by timestamp
                    ->get();


                $doc_access = DB::table('audit_logs')
                    ->select(
                        'users.full_name',              // User name
                        'roles.role_name',              // Role name
                        'audit_logs.action',            // Action (Login/Logout)
                        'audit_logs.ip_address',        // IP Address
                        'audit_logs.created_at',
                        'audit_logs.changes',
                        'audit_logs.model'
                    )
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')  // Join audit logs with users
                    ->join('roles', 'roles.id', '=', 'users.role_guid')     // Join users with roles
                    ->join('user_organizations', 'user_organizations.user_guid', '=', 'users.id') // Join user organizations
                    ->where(function ($query) use ($orgNames) {
                        $query->where('user_organizations.org_guid', $orgNames); // Include modifications by users in specified organizations
                    })             // Filter by organization
                    ->whereIn('audit_logs.action', ['Created', 'Updated', 'Deactivate', 'Deleted'])     // Filter for login/logout actions
                    ->whereBetween('audit_logs.created_at', [$startDate, $endDate]) // Filter by date range
                    ->orderBy('audit_logs.created_at', 'desc')              // Order by timestamp
                    ->get();
            }
        } else {
            // All companies' document statistics
            $docStat_pdf = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'pdf')
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            // For DOCX and DOC documents
            $docStat_docx = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->whereIn('documents.doc_type', ['docx', 'doc']) // Handle both docx and doc
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            // For PPTX documents
            $docStat_pptx = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'pptx')
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            // For image documents (specify image types if necessary)
            $docStat_images = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'images') // Adjust to handle various image types
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            // For Excel documents (XLSX and CSV)
            $docStat_excel = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->whereIn('documents.doc_type', ['xlsx', 'csv'])
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            // Total document count
            $docStat_total = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->join('organizations', 'organizations.id', '=', 'shared_documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();


            // Query to get the activity log for login and logout actions
            $user_login = DB::table('audit_logs')
                ->select(
                    'users.full_name',              // User name
                    'roles.role_name',              // Role name
                    'audit_logs.action',            // Action (Login/Logout)
                    'audit_logs.ip_address',        // IP Address
                    'audit_logs.created_at',
                    // Timestamp
                )
                ->join('users', 'users.id', '=', 'audit_logs.user_guid')  // Join audit logs with users
                ->join('roles', 'roles.id', '=', 'users.role_guid')     // Join users with roles
                ->whereIn('audit_logs.action', ['Login', 'Logout'])     // Filter for login/logout actions
                ->whereBetween('audit_logs.created_at', [$startDate, $endDate]) // Filter by date range
                ->orderBy('audit_logs.created_at', 'desc')              // Order by timestamp
                ->get();

            $doc_access = DB::table('audit_logs')
                ->select(
                    'users.full_name',              // User name
                    'roles.role_name',              // Role name
                    'audit_logs.action',            // Action (Login/Logout)
                    'audit_logs.ip_address',        // IP Address
                    'audit_logs.created_at',
                    'audit_logs.changes',
                    'audit_logs.model'
                )
                ->join('users', 'users.id', '=', 'audit_logs.user_guid')  // Join audit logs with users
                ->join('roles', 'roles.id', '=', 'users.role_guid')     // Join users with roles
                ->whereIn('audit_logs.action', ['Created', 'Updated', 'Deactivate', 'Deleted'])     // Filter for login/logout actions
                ->whereBetween('audit_logs.created_at', [$startDate, $endDate]) // Filter by date range
                ->orderBy('audit_logs.created_at', 'desc')              // Order by timestamp
                ->get();
        }

        return view('admin.report.generated', compact(
            'totalCurrentStorage',
            'totalSpace',
            'startDate',
            'endDate',
            'docStat_pdf',
            'docStat_docx',
            'docStat_pptx',
            'docStat_images',
            'docStat_excel',
            'docStat_total',
            'formatted_startDate',
            'formatted_endDate',
            'user_login',
            'doc_access',
            'contentOptions'
        ));
    }


    public function calculateTotalStorage()
    {
        $totalCurrentStorage = documentVersion::sum('file_size'); // Total size in bytes

        // Optionally convert to MB or GB
        $totalCurrentStorageMB = $totalCurrentStorage / (1024 * 1024); // Convert to MB

        return $totalCurrentStorageMB;
    }

    public function getAvailableStorage()
    {
        // Get total disk space (in bytes)
        $totalSpace = disk_total_space(storage_path()); // Adjust the path as needed
        // Calculate available space
        $availableSpace = $totalSpace;
        // Optionally convert to MB
        return $availableSpace / (1024 * 1024); // Convert to MB
    }
}
