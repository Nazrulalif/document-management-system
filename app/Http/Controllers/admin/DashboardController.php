<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Stat;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // $company = Organization::all(); // Fetch all users from the database
        if ($request->ajax()) {

            if (Auth::user()->role_guid == 1) {
                $data = AuditLog::select('*', 'audit_logs.created_at as created_at')
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')
                    ->orderBy('audit_logs.id', 'desc')
                    ->get();
            } else {
                // Get the authenticated user's organization IDs
                // Get the authenticated user's organization IDs
                $userOrgIds = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

                // Retrieve audit logs for users in the same organizations and group by user ID and action
                $data = AuditLog::select(
                    'audit_logs.user_guid',
                    'users.full_name',
                    'audit_logs.action',
                    'audit_logs.ip_address',
                    'audit_logs.model',
                    'audit_logs.changes',
                    DB::raw('COUNT(audit_logs.id) as action_count'),
                    DB::raw('GROUP_CONCAT(audit_logs.created_at ORDER BY audit_logs.created_at DESC) as created_dates') // Aggregate created_at if needed
                )
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')
                    ->join('user_organizations', 'user_organizations.user_guid', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_guid')
                    ->whereIn('user_organizations.org_guid', $userOrgIds) // Filter by organization IDs
                    ->orwhere('users.role_guid', '1') // Filter by organization IDs
                    ->groupBy(
                        'audit_logs.user_guid',
                        'users.full_name',
                        'audit_logs.action',
                        'audit_logs.ip_address',
                        'audit_logs.model',
                        'audit_logs.changes' // Ensure all selected non-aggregated columns are here
                    )
                    ->orderBy('created_dates', 'desc') // Adjust ordering if needed
                    ->get();
            }



            // Format the date and time for each record
            $formatted_data = $data->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
                return $item;
            });

            return DataTables::of($formatted_data)
                ->make(true);
        }

        // $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        if (Auth::user()->role_guid == 1) {
            $fileCount = Document::count();
            $folderCount = Folder::count();
            $userCount = User::where('is_active', '=', 'Y')->count();
            $orgCount = Organization::where('is_operation', '=', 'Y')->count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::where('org_guid', '0')->whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'org_guid' =>  '0',
                    'file_count' => $fileCount,
                    'folder_count' => $folderCount,
                    'user_count' => $userCount,
                    'org_count' => $orgCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'file_count' => $fileCount,
                    'folder_count' => $folderCount,
                    'user_count' => $userCount,
                    'org_count' => $orgCount,
                ]);
            }

            // Get yesterday's stat entry
            $previousStats = Stat::where('org_guid', '0')->orderBy('created_at', 'desc')->skip(1)->first();
            $todayLogin = AuditLog::where('action', '=', 'login')->whereDate('created_at', Carbon::today())->count();
        } else {
            $userOrgIds = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

            $fileCount = Document::join('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                ->whereIn('shared_documents.org_guid', $userOrgIds)
                ->count();

            $folderCount = Folder::join('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                ->whereIn('shared_folders.org_guid', $userOrgIds)
                ->count();

            $userCount = User::select('users.*', 'roles.role_name as role_name')
                ->where('users.is_active', '=', 'Y')
                ->whereHas('organizations', function ($query) use ($userOrgIds) {
                    $query->whereIn('organizations.id', $userOrgIds);
                })
                ->with('organizations')
                ->count();

            $orgCount = Organization::where('is_operation', '=', 'Y')->count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereIn('org_guid', $userOrgIds)->whereDate('created_at', Carbon::today())->first();

            // Get yesterday's stat entry
            $previousStats = Stat::whereIn('org_guid', $userOrgIds)
                ->orderBy('created_at', 'desc')->skip(1)->first();

            $todayLogin = 0;
        }

        // Default previous counts to 0 if no entry is found for yesterday
        $previousFileCount = $previousStats ? $previousStats->file_count : 0;
        $previousFolderCount = $previousStats ? $previousStats->folder_count : 0;
        $previousUserCount = $previousStats ? $previousStats->user_count : 0;
        $previousOrgCount = $previousStats ? $previousStats->org_count : 0;

        // Compare today's counts with yesterday's counts
        $fileTrend = $fileCount > $previousFileCount ? 'up' : ($fileCount < $previousFileCount ? 'down' : 'same');
        $folderTrend = $folderCount > $previousFolderCount ? 'up' : ($folderCount < $previousFolderCount ? 'down' : 'same');
        $userTrend = $userCount > $previousUserCount ? 'up' : ($userCount < $previousUserCount ? 'down' : 'same');
        $orgTrend = $orgCount > $previousOrgCount ? 'up' : ($orgCount < $previousOrgCount ? 'down' : 'same');

        //count today login
        $totalCurrentStorage = $this->calculateTotalStorage();
        $totalSpace = $this->getAvailableStorage();
        return view('admin.index', compact(
            'fileCount',
            'folderCount',
            'userCount',
            'orgCount',
            'fileTrend',
            'folderTrend',
            'userTrend',
            'orgTrend',
            'todayLogin',
            'totalCurrentStorage',
            'totalSpace'
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
        // Get used space
        // Calculate available space
        $availableSpace = $totalSpace;

        // Optionally convert to MB
        return $availableSpace / (1024 * 1024); // Convert to MB
    }
}
