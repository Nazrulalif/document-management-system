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
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // $company = Organization::all(); // Fetch all users from the database
        if ($request->ajax()) {

            $data = AuditLog::select('*', 'audit_logs.created_at as created_at')
                ->join('users', 'users.id', '=', 'audit_logs.user_guid')
                ->orderBy('audit_logs.id', 'desc')
                ->get();

            // Format the date and time for each record
            $formatted_data = $data->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
                return $item;
            });

            return DataTables::of($formatted_data)
                ->addIndexColumn()
                ->editColumn('changes', function ($row) {
                    // Check if 'changes' contains valid JSON data
                    $changes = json_decode($row->changes, true);

                    if (is_array($changes)) {
                        // Initialize empty result strings and arrays
                        $result = '';
                        $companyNames = [];
                        $userNames = [];

                        // Handle case where $changes is an array of objects
                        if (isset($changes[0]) && is_array($changes[0])) {
                            foreach ($changes as $change) {
                                if (!empty($change['org_name'])) {
                                    $companyNames[] = $change['org_name']; // Collect company names
                                }
                                if (!empty($change['full_name'])) {
                                    $userNames[] = $change['full_name']; // Collect user names
                                }
                                if (!empty($change['doc_title'])) {
                                    $userNames[] = $change['doc_title']; // Collect user names
                                }
                            }
                        } else {
                            // Single object case
                            if (!empty($changes['org_name'])) {
                                $companyNames[] = $changes['org_name'];
                            }
                            if (!empty($changes['full_name'])) {
                                $userNames[] = $changes['full_name'];
                            }
                            if (!empty($changes['role_name'])) {
                                $roleName[] = $changes['role_name'];
                            }
                            if (!empty($changes['folder_name'])) {
                                $folder_name[] = $changes['folder_name'];
                            }
                            if (!empty($changes['doc_title'])) {
                                $doc_title[] = $changes['doc_title'];
                            }
                        }

                        // Construct the result string
                        if (!empty($companyNames)) {
                            $result .=  implode(', ', $companyNames); // Join company names
                        }
                        if (!empty($userNames)) {
                            $result .= ($result ? ', ' : '') . implode(', ', $userNames); // Join user names
                        }
                        if (!empty($roleName)) {
                            $result .= ($result ? ', ' : '') . implode(', ', $roleName); // Join user names
                        }
                        if (!empty($folder_name)) {
                            $result .= ($result ? ', ' : '') . implode(', ', $folder_name); // Join user names
                        }
                        if (!empty($doc_title)) {
                            $result .= ($result ? ', ' : '') . implode(', ', $doc_title); // Join user names
                        }

                        // Return the constructed string or a fallback if empty
                        return !empty($result) ? $result : '-';
                    }

                    // If JSON is null or invalid, return a fallback message
                    return '-';
                })
                ->make(true);
        }


        // Get current counts
        $fileCount = Document::count();
        $folderCount = Folder::count();
        $userCount = User::where('is_active', '=', 'Y')->count();
        $orgCount = Organization::where('is_operation', '=', 'Y')->count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
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
        $yesterdayStats = Stat::whereDate('created_at', Carbon::yesterday())->first();

        // Default previous counts to 0 if no entry is found for yesterday
        $previousFileCount = $yesterdayStats ? $yesterdayStats->file_count : 0;
        $previousFolderCount = $yesterdayStats ? $yesterdayStats->folder_count : 0;
        $previousUserCount = $yesterdayStats ? $yesterdayStats->user_count : 0;
        $previousOrgCount = $yesterdayStats ? $yesterdayStats->org_count : 0;

        // Compare today's counts with yesterday's counts
        $fileTrend = $fileCount > $previousFileCount ? 'up' : ($fileCount < $previousFileCount ? 'down' : 'same');
        $folderTrend = $folderCount > $previousFolderCount ? 'up' : ($folderCount < $previousFolderCount ? 'down' : 'same');
        $userTrend = $userCount > $previousUserCount ? 'up' : ($userCount < $previousUserCount ? 'down' : 'same');
        $orgTrend = $orgCount > $previousOrgCount ? 'up' : ($orgCount < $previousOrgCount ? 'down' : 'same');

        //count today login
        $todayLogin = AuditLog::where('action', '=', 'login')->whereDate('created_at', Carbon::today())->count();
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
