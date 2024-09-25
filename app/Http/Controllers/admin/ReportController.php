<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $organization = Organization::where('is_operation', '=', 'Y')->get();
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
                // Example logic for selected companies
                $docStat_pdf = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->where('documents.doc_type', '=', 'pdf')
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();

                $docStat_docx = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->whereIn('documents.doc_type', ['docx', 'doc']) // Handle docx and doc
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();

                $docStat_pptx = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->where('documents.doc_type', '=', 'pptx')
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();

                $docStat_images = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->where('documents.doc_type', '=', 'images') // List image types
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();
                $docStat_excel = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->whereIn('documents.doc_type', ['xlsx', 'csv',])
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();

                $docStat_total = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->where('organizations.is_operation', '=', 'Y')
                    ->whereIn('documents.org_guid', $orgNames)
                    ->whereBetween('documents.created_at', [$startDate, $endDate])
                    ->count();
                $docStat_total = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->whereIn('documents.org_guid', $orgNames)
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
                        'audit_logs.created_at'         // Timestamp
                    )
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')  // Join audit logs with users
                    ->join('roles', 'roles.id', '=', 'users.role_guid')     // Join users with roles
                    ->whereIn('users.org_guid', $orgNames)                  // Filter by organization
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
                    ->whereIn('users.org_guid', $orgNames)                  // Filter by organization
                    ->whereIn('audit_logs.action', ['Created', 'Updated', 'Deactivate', 'Deleted'])     // Filter for login/logout actions
                    ->whereBetween('audit_logs.created_at', [$startDate, $endDate]) // Filter by date range
                    ->orderBy('audit_logs.created_at', 'desc')              // Order by timestamp
                    ->get();
            }
        } else {
            // All companies' document statistics
            $docStat_pdf = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'pdf')
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            $docStat_docx = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->whereIn('documents.doc_type', ['docx', 'doc']) // Handle docx and doc
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            $docStat_pptx = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'pptx')
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            $docStat_images = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->where('documents.doc_type', '=', 'images') // List image types
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            $docStat_excel = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('organizations.is_operation', '=', 'Y')
                ->whereIn('documents.doc_type', ['xlsx', 'csv',])
                ->whereBetween('documents.created_at', [$startDate, $endDate])
                ->count();

            $docStat_total = Document::join('organizations', 'organizations.id', '=', 'documents.org_guid')
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
