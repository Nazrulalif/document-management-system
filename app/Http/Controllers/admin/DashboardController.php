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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $data = AuditLog::select('*', 'audit_logs.created_at as created_at')
                    ->join('users', 'users.id', '=', 'audit_logs.user_guid')
                    ->where('users.org_guid', Auth::user()->org_guid)
                    ->orderBy('audit_logs.id', 'desc')
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
            $fileCount = Document::join('users', 'users.id', '=', 'documents.upload_by')
                ->where('documents.org_guid', Auth::user()->org_guid)
                ->orwhere('users.role_guid', '1')
                ->count();

            $folderCount = Folder::join('users', 'users.id', '=', 'folders.created_by')
                ->where('folders.org_guid', Auth::user()->org_guid)
                ->orwhere('users.role_guid', '1')
                ->count();

            $userCount = User::where('is_active', '=', 'Y')->where('org_guid', Auth::user()->org_guid)->count();
            $orgCount = Organization::where('is_operation', '=', 'Y')->count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::where('org_guid', Auth::user()->org_guid)->whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'org_guid' =>  Auth::user()->org_guid,
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
            $previousStats = Stat::where('org_guid', Auth::user()->org_guid)
                ->orderBy('created_at', 'desc')->skip(1)->first();

            $todayLogin = AuditLog::join('users', 'users.id', '=', 'audit_logs.user_guid')
                ->where('audit_logs.action', '=', 'login')
                ->where('users.org_guid', Auth::user()->org_guid)
                ->whereDate('audit_logs.created_at', Carbon::today())->count();
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
