<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Role;
use App\Models\shared_document;
use App\Models\shared_folder;
use App\Models\Starred_document;
use App\Models\Starred_folder;
use App\Models\Stat;
use App\Models\User;
use App\Models\User_organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory; // For Word documents
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch starred folders and documents for the authenticated user
            $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)
                ->pluck('folder_guid')
                ->toArray();
            $starredDocs = Starred_document::where('user_guid', Auth::user()->id)
                ->pluck('doc_guid')
                ->toArray();

            if (Auth::user()->role_guid == 1) {
                // Fetch folders and documents
                $folders = Folder::select('folders.uuid as uuid', 'folders.id as id', 'folders.folder_name as item_name', 'users.full_name as full_name')
                    ->join('users', 'users.id', '=', 'folders.created_by')
                    ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                    ->whereNull('folders.parent_folder_guid')
                    // ->groupBy('folders.id')
                    ->orderBy('folders.created_at', 'DESC')
                    ->get()
                    ->map(function ($folder) use ($starredFolders) {
                        // Aggregating the organization names and GUIDs
                        $folder->shared_orgs = $folder->sharedOrganizations->pluck('org_name')->implode("\n");
                        $folder->shared_orgs_guid = $folder->sharedOrganizations->pluck('id')->implode(',');
                        // Check if the folder is starred
                        $folder->is_starred = in_array($folder->id, $starredFolders);
                        $folder->doc_type = null;
                        return $folder;
                    });

                $rootDocuments = Document::with('sharedOrganizations') // Eager load shared organizations
                    ->select('documents.*', 'documents.uuid as uuid', 'documents.id as id', 'documents.doc_title as item_name', 'users.full_name as full_name')
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid')
                    ->whereNull('documents.folder_guid')
                    ->orderBy('documents.created_at', 'DESC')
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        // Aggregating the organization names and GUIDs
                        $document->shared_orgs = $document->sharedOrganizations->pluck('org_name')->implode("\n");
                        $document->shared_orgs_guid = $document->sharedOrganizations->pluck('id')->implode(',');
                        // Check if the document is starred
                        $document->is_starred = in_array($document->id, $starredDocs);
                        return $document;
                    });
            } else {
                // User's organization IDs
                $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

                $folders = Folder::with(['children', 'documents', 'sharedOrganizations']) // Eager load shared organizations
                    ->select('folders.*', 'folders.uuid as uuid', 'folders.id as id', 'folders.folder_name as item_name', 'users.full_name as full_name')
                    ->join('users', 'users.id', '=', 'folders.created_by')
                    ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                    ->where(function ($query) use ($user_orgs) {
                        $query->whereIn('shared_folders.org_guid', $user_orgs)->orWhereNull('shared_folders.org_guid');
                    })
                    ->whereNull('folders.parent_folder_guid')
                    // ->groupBy('folders.id')
                    ->orderBy('folders.created_at', 'DESC')
                    ->get()
                    ->map(function ($folder) use ($starredFolders) {
                        // Aggregating the organization names and GUIDs
                        $folder->shared_orgs = $folder->sharedOrganizations->pluck('org_name')->implode("\n");
                        $folder->shared_orgs_guid = $folder->sharedOrganizations->pluck('id')->implode(',');
                        // Check if the folder is starred
                        $folder->is_starred = in_array($folder->id, $starredFolders);
                        $folder->doc_type = null;

                        return $folder;
                    });

                $rootDocuments = Document::with('sharedOrganizations') // Eager load shared organizations
                    ->select('documents.*', 'documents.uuid as uuid', 'documents.id as id', 'documents.doc_title as item_name', 'users.full_name as full_name')
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid')
                    ->where(function ($query) use ($user_orgs) {
                        $query->whereIn('shared_documents.org_guid', $user_orgs)->orWhereNull('shared_documents.org_guid');
                    })
                    ->whereNull('documents.folder_guid')
                    ->orderBy('documents.created_at', 'DESC')
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        // Aggregating the organization names and GUIDs
                        $document->shared_orgs = $document->sharedOrganizations->pluck('org_name')->implode("\n");
                        $document->shared_orgs_guid = $document->sharedOrganizations->pluck('id')->implode(',');
                        // Check if the document is starred
                        $document->is_starred = in_array($document->id, $starredDocs);
                        return $document;
                    });
            }

            // Merge folders and documents
            $data = $folders->concat($rootDocuments);

            // Return data via DataTables
            return DataTables::of($data)->addIndexColumn()->make(true);
        }

        // Retrieve full organization records instead of just IDs
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)
            ->join('organizations', 'user_organizations.org_guid', '=', 'organizations.id')
            ->where('organizations.is_operation', 'Y')
            ->select('organizations.id', 'organizations.org_name')
            ->get();

        if (Auth::user()->role_guid == 1) {
            $company = Organization::where('is_operation', 'Y')->get();
        } else {
            $company = User_organization::join('organizations', 'organizations.id', '=', 'user_organizations.org_guid')
                ->where('user_organizations.user_guid', Auth::user()->id)
                ->get();
        }

        return view('admin.file-manager.file-manager', compact('company', 'user_orgs'));
    }

    //original
    public function star(Request $request)
    {
        $type = $request->input('type');
        $starred = false; // Initialize starred to false

        if ($type == 'folder') {
            // Check if the folder is already starred
            $isStarred = Starred_folder::where('user_guid', Auth::user()->id)
                ->where('folder_guid', $request->id)
                ->exists();

            if ($isStarred) {
                // Unstar the folder
                Starred_folder::where('user_guid', Auth::user()->id)
                    ->where('folder_guid', $request->id)
                    ->delete();
            } else {
                // Star the folder
                Starred_folder::create([
                    'user_guid' => Auth::user()->id,
                    'folder_guid' => $request->id,
                ]);
                $starred = true; // Set starred to true
            }
        } elseif ($type == 'document') {
            // Check if the folder is already starred
            $isStarred = Starred_document::where('user_guid', Auth::user()->id)
                ->where('doc_guid', $request->id)
                ->exists();

            if ($isStarred) {
                // Unstar the folder
                Starred_document::where('user_guid', Auth::user()->id)
                    ->where('doc_guid', $request->id)
                    ->delete();
            } else {
                // Star the folder
                Starred_document::create([
                    'user_guid' => Auth::user()->id,
                    'doc_guid' => $request->id,
                ]);
                $starred = true; // Set starred to true
            }
        }

        return response()->json(['success' => true, 'starred' => $starred]);
    }

    public function show_folder(Request $request, $uuid)
    {
        $folder = Folder::where('uuid', $uuid)
            ->with(['children', 'documents', 'creator'])
            ->first();

        $sharedOrgIds = shared_folder::where('folder_guid', $folder->id)
            ->pluck('org_guid'); // Use org_guid directly
        
        $userOrgIds = User_organization::where('user_guid', Auth::user()->id)
            ->pluck('org_guid'); // Same, just the GUIDs
        
        // Check if any intersection exists
        if (Auth::user()->role_guid != 1 && $sharedOrgIds->intersect($userOrgIds)->isEmpty()) {
            return redirect()->route('fileManager.index')->with('error', 'You do not have permission to access this folder.');
        }
        
        

        if (!$folder) {
            return redirect()->route('fileManager.index')->with('error', 'Folder not found or has been deleted.');
        }

        if ($request->ajax()) {
            // Fetch starred folders and documents for the authenticated user
            $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)
                ->pluck('folder_guid')
                ->toArray();
            $starredDocs = Starred_document::where('user_guid', Auth::user()->id)
                ->pluck('doc_guid')
                ->toArray();

            // Prepare the children folders
            $subfolders = $folder->children->map(function ($childFolder) use ($starredFolders) {
                return [
                    'id' => $childFolder->id,
                    'uuid' => $childFolder->uuid,
                    'shared_orgs' => $childFolder->shared_org_names, // Accessor provides concatenated org names
                    'shared_orgs_guid' => $childFolder->shared_org_ids, // Accessor provides concatenated org IDs
                    'item_name' => $childFolder->folder_name,
                    'full_name' => $childFolder->creator->full_name,
                    'doc_type' => null,
                    'is_starred' => in_array($childFolder->id, $starredFolders),
                ];
            });

            // Fetch documents related to the folder, adjusted for user roles
            $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

            $documentsQuery = Document::with(['folder', 'uploadBy', 'sharedOrganizations'])
                ->whereHas('folder', function ($query) use ($uuid) {
                    $query->where('uuid', $uuid);
                })
                ->orderBy('created_at', 'DESC');

            if (Auth::user()->role_guid != 1) {
                $documentsQuery->whereHas('sharedOrganizations', function ($query) use ($user_orgs) {
                    $query->whereIn('organizations.id', $user_orgs);
                });
            }

            $documents = $documentsQuery->get()->map(function ($document) use ($starredDocs) {
                return [
                    'id' => $document->id,
                    'uuid' => $document->uuid,
                    'full_name' => $document->full_name, // Full name of uploader from accessor
                    'shared_orgs' => $document->shared_org_names, // Concatenated organization names
                    'item_name' => $document->doc_title,
                    'doc_type' => $document->doc_type,
                    'shared_orgs_guid' => $document->shared_org_ids,
                    'latest_version_guid' => $document->latest_version_guid,
                    'is_starred' => in_array($document->id, $starredDocs),
                ];
            });

            // Merge subfolders and documents
            $data = $subfolders->concat($documents);

            // Return data via DataTables
            return DataTables::of($data)->addIndexColumn()->make(true);
        }

        // Additional data for non-AJAX requests
        $folder_id = Folder::where('uuid', $uuid)->first();
        $path = $this->getFolderPath($folder);
        $company = Organization::all();
        $folder_shared_id = Shared_folder::where('folder_guid', $folder->id)->first();
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)
            ->join('organizations', 'user_organizations.org_guid', '=', 'organizations.id')
            ->select('organizations.id', 'organizations.org_name')
            ->get();

        // Passing the folder to the view
        return view('admin.file-manager.file-manager-item', compact('uuid', 'folder', 'folder_id', 'path', 'company', 'folder_shared_id', 'user_orgs'));
    }

    public function getFolderPath(Folder $folder)
    {
        $path = [];

        // Loop to traverse back to the root folder
        while ($folder) {
            array_unshift($path, $folder); // Prepend to maintain correct order
            $folder = $folder->parent;
        }

        return $path;
    }

    public function create(Request $request)
    {
        $request->validate([
            'new_folder_name' =>  [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\.\-\s]+$/', // allows letters, numbers, dot, dash, underscore, and space
            ],
        ],
        [
            'new_folder_name.regex' => 'Folder name can only contain letters, numbers, dot, dash, underscore, and space.',
        ]);

        $rawInput = $request->new_folder_name;
        $decoded = html_entity_decode(urldecode($rawInput), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        try {
            $folder = Folder::create([
                'folder_name' => $decoded,
                'parent_folder_guid' => $request->new_folder_id,
                'created_by' => Auth::user()->id,
                'is_meeting' => 'N',
            ]);

            $orgNames = $request->input('org_name');

            if (!empty($orgNames)) {
                $folder = Folder::where('id', $folder->id)->first();
                // Create a shared folder entry for each organization
                shared_folder::create([
                    'folder_guid' => $folder->id,
                    'org_guid' => $orgNames,
                ]);
            }

            $folderCount = Folder::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'folder_count' => $folderCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'folder_count' => $folderCount,
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to Upload folder. Please try again.']);
        }
    }

    public function destroy($id)
    {
        // Find the folder by ID
        $folder = Folder::find($id);

        if ($folder) {
            // Recursively delete all documents, files, and subfolders
            $this->deleteFolderContents($folder);

            // Finally, delete the folder itself
            $folder->delete();

            // Get the updated folder count
            $folderCount = Folder::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'folder_count' => $folderCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'folder_count' => $folderCount,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Folder and its contents deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Folder not found']);
    }

    private function deleteFolderContents($folder)
    {
        try {
            $documents = Document::with('versions')->where('folder_guid', $folder->id)->get();

            foreach ($documents as $doc) {
                foreach ($doc->versions as $version) {
                    if ($version->file_path && Storage::exists($version->file_path)) {
                        Storage::delete($version->file_path);
                    }
                }

                $doc->versions()->delete();
                Starred_document::where('doc_guid', $doc->id)->delete();

                $doc->delete();
            }

            shared_folder::where('folder_guid', $folder->id)->delete();

            $subfolders = Folder::where('parent_folder_guid', $folder->id)->get();

            foreach ($subfolders as $subfolder) {
                $this->deleteFolderContents($subfolder);
                $subfolder->delete();
            }
        } catch (\Throwable $e) {
            Log::error('Error deleting folder contents: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'An error occurred while deleting the folder contents.');
        }
    }

    public function rename(Request $request, $id)
    {
        // Retrieve the folder by ID
        $folder = Folder::find($id);

        if (!$folder) {
            return response()->json(['success' => false, 'message' => 'Folder not found.']);
        }

        // Validate the request
        // $validatedData = $request->validate([
        //     'folder_name' => 'required|string|max:255',
        // ]);

        $validatedData = $request->validate([
            'folder_name' =>  [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\.\-\s]+$/', // allows letters, numbers, dot, dash, underscore, and space
            ],
        ],
        [
            'folder_name.regex' => 'Folder name can only contain letters, numbers, dot, dash, underscore, and space.',
        ]);

        $rawInput = $request->folder_name;
        $decoded = html_entity_decode(urldecode($rawInput), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        try {
            // Update the folder name
            $folder->update([
                'folder_name' => $decoded,
            ]);

            // Retrieve existing shared organizations for the main folder
            $existingSharedOrg = shared_folder::where('folder_guid', $id)->first();

            // New organization(s) from the request or fallback to user's org
            $newSharedOrg = $request->input('org_name_edit');

            // Update the shared organization for the main folder
            $existingSharedOrg->update(['org_guid' => $newSharedOrg]);

            // Recursively update subfolders
            $this->updateSubfolderSharedOrgs($id, $newSharedOrg);
            $this->updateSharedFiles($id, $newSharedOrg);

            return response()->json(['success' => true, 'message' => 'Folder and subfolders updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update folder. Please try again.']);
        }
    }

    /**
     * Recursively update shared organizations for all child folders.
     */
    private function updateSubfolderSharedOrgs($parentId, $newOrgGuid)
    {
        // Get all direct child folders of the given folder
        $subfolders = Folder::where('parent_folder_guid', $parentId)->get();

        foreach ($subfolders as $subfolder) {
            // Update or create shared organization for each subfolder in shared_folders table
            shared_folder::updateOrCreate(
                ['folder_guid' => $subfolder->id], // Match on folder_guid
                ['org_guid' => $newOrgGuid], // Update org_guid
            );

            // Update or create shared organization for files in shared_files table

            // Recursively update the shared organizations for the subfolder's children
            $this->updateSubfolderSharedOrgs($subfolder->id, $newOrgGuid);
            $this->updateSharedFiles($subfolder->id, $newOrgGuid);
        }
    }

    /**
     * Update or create shared organization for files in shared_files table.
     */
    private function updateSharedFiles($folderId, $newOrgGuid)
    {
        // Get all files related to the given folder
        $files = Document::where('folder_guid', $folderId)->get();

        foreach ($files as $file) {
            // Update or create shared organization entry for each file in shared_files table
            shared_document::updateOrCreate(
                ['doc_guid' => $file->id], // Match on file_guid
                ['org_guid' => $newOrgGuid], // Update org_guid
            );
        }
    }

    public function rename_file(Request $request, $id)
    {
        // Retrieve the folder by ID
        $file = Document::find($id);

        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        // Validate the request
        // $validatedData = $request->validate([
        //     'edit_file' => 'required|string|max:255',
        // ]);

        $validatedData = $request->validate([
            'edit_file' =>  [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\.\-\s]+$/', // allows letters, numbers, dot, dash, underscore, and space
            ],
        ],
        [
            'edit_file.regex' => 'Folder name can only contain letters, numbers, dot, dash, underscore, and space.',
        ]);

        $rawInput = $request->edit_file;
        $decoded = html_entity_decode(urldecode($rawInput), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        try {
            // Update the folder name
            $file->update([
                'doc_title' => $decoded,
            ]);

            // Retrieve existing shared organizations
            $existingSharedOrgs = shared_document::where('doc_guid', $id)->first();

            // New organizations from the request
            $newSharedOrgs = $request->input('org_name_edit');

            $existingSharedOrgs->update([
                'org_guid' => $newSharedOrgs,
            ]);

            return response()->json(['success' => true, 'message' => 'File updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update file. Please try again.']);
        }
    }

    public function deleteSelected(Request $request)
    {
        // Get the array of selected folder IDs from the request
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No folders selected for deletion']);
        }

        // Find all folders that match the selected IDs
        $folders = Folder::whereIn('id', $ids)->get();

        // AuditLog::create([
        //     'action' => "Deleted",
        //     'model' => 'Folder',
        //     'changes' => json_encode($folders),
        //     'user_guid' => Auth::user()->id,
        //     'ip_address' => request()->ip(),
        // ]);

        foreach ($folders as $folder) {
            // Recursively delete folder contents (files, documents, subfolders)
            $this->deleteFolderContents($folder);

            // Finally, delete the folder itself
            $folder->delete();
        }

        $folderCount = Folder::count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'folder_count' => $folderCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'folder_count' => $folderCount,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Selected folders and their contents deleted successfully']);
    }

    public function file_deleteSelected(Request $request)
    {
        // Get the array of selected document IDs
        $ids = $request->input('ids');

        // Fetch all document versions for the given IDs
        $documents = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')->whereIn('documents.id', $ids)->get();

        // Iterate over each document
        foreach ($documents as $document) {
            $filePath = $document->file_path;
            AuditLog::create([
                'action' => 'Deleted',
                'model' => 'File',
                'changes' => $document->doc_title,
                'user_guid' => Auth::user()->id,
                'ip_address' => request()->ip(),
            ]);
            // Assuming $filePath is relative to the 'public' disk (e.g., 'uploads/myfile.pdf')
            if (Storage::exists($filePath)) {
                Storage::delete($filePath); // Delete the file from 'public' disk
            }

            Starred_document::where('doc_guid', $document->doc_guid)->delete();

            // Find the document in the documents table and delete it
            Document::where('id', $document->doc_guid)->delete();
        }

        // Get current counts
        $fileCount = Document::count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'file_count' => $fileCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'file_count' => $fileCount,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Selected files deleted successfully']);
    }

    public function destroy_file($id)
    {
        $document = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')->where('documents.id', '=', $id)->first();

        $documentId = Document::find($id);

        // Check if the document exists
        if ($document) {
            // Get the file path in storage
            // Get all versions of the document using the document's ID or any identifier that links to versions
            $allVersions = documentVersion::where('doc_guid', '=', $documentId->id)->get();

            // Loop through all versions and delete associated files
            foreach ($allVersions as $version) {
                $filePath = $version->file_path;

                // Assuming $filePath is relative to the 'public' disk (e.g., 'uploads/myfile.pdf')
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath); // Delete the file from 'public' disk
                }

                // Delete the version record from the database
                $version->delete();
            }

            Starred_document::where('doc_guid', $id)->delete();

            // Now delete the main document record from the database
            Document::where('id', $id)->delete();

            // Get current counts
            $fileCount = Document::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'file_count' => $fileCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'file_count' => $fileCount,
                ]);
            }

            // Redirect back with a success message
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'File not found']);
    }

    // upload and extract file

    public function upload_file(Request $request)
    {
        // Validate the file input
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,pptx,xlsx,csv|max:102400', // 100MB Max
        ]);

        $orgNames = $request->input('org_name_file');

        // Handle the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize(); // Size in bytes

            // Define folder based on file extension
            $folder = $this->getFolderByFileType($extension); // Separate function to get the folder

            // $filePath = $file->storeAs('uploads/' . $folder, $uniqueFileName, 'public');
            $filePath = 'uploads/' . $folder . '/' . $uniqueFileName;
            Storage::put($filePath, file_get_contents($file));

            $folder_id = $request->folder_id;

            // Initialize the document content variable
            $docContent = null;

            // If the file is a PDF, extract its content
            if ($extension === 'pdf') {
                try {
                    $pdfParser = new Parser();
                    $fileContent = file_get_contents($file);
                    // $pdf = $pdfParser->parseFile(public_path('storage/' . $filePath));
                    $pdf = $pdfParser->parseContent($fileContent);
                    $docContent = $pdf->getText(); // Extract the text from the PDF
                } catch (\Exception $e) {
                    $docContent = 'Sorry, unable to extract the text';
                }
            }

            // If the file is a DOCX, extract its content using the method defined below
            if ($extension === 'docx') {
                try {
                    $docContent = $this->extractTextFromDocx($file); // Extract text from DOCX
                } catch (\Exception $e) {
                    $docContent = 'Sorry, unable to extract the text';
                }
            }

            // Handle Excel file
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                try {
                    $docContent = $this->extractTextFromExcel($file); // Extract text from Excel
                } catch (\Exception $e) {
                    $docContent = 'Sorry, unable to extract the text';
                }
            }

            // Check if OCR content was sent for images
            if (in_array($extension, ['jpeg', 'jpg', 'png']) && $request->has('ocr_content')) {
                try {
                    $docContent = $request->input('ocr_content'); // Use the extracted OCR content from the request
                } catch (\Exception $e) {
                    $docContent = 'Sorry, unable to extract the text';
                }
            }

            $documentVersion = documentVersion::create([
                'doc_guid' => null,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'doc_content' => $docContent, // Store the extracted document content
                'created_by' => Auth::user()->id,
                'version_number' => 'v1.0',
                'change_description' => 'Not set',
            ]);

            // Store file information in the database
            $newFile = Document::create([
                'doc_name' => $uniqueFileName,
                'doc_title' => $originalName,
                'folder_guid' => $folder_id,
                'doc_type' => $folder,
                'upload_by' => Auth::user()->id,
                'doc_description' => 'Not set',
                'doc_summary' => 'Not set',
                'doc_author' => 'Not set',
                'doc_keyword' => 'Not set',
                'version_limit' => '5',
                'latest_version_guid' => $documentVersion->uuid,
            ]);

            $documentVersion->doc_guid = $newFile->id;
            $documentVersion->save();

            if (!empty($orgNames)) {
                // Create a shared folder entry for each organization
                shared_document::create([
                    'doc_guid' => $newFile->id,
                    'org_guid' => $orgNames,
                ]);
            }
            // Get current counts
            $fileCount = Document::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'file_count' => $fileCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'file_count' => $fileCount,
                ]);
            }

            return back()->with('success', 'File uploaded successfully.');
        }

        return back()->with('error', 'No file selected.');
    }

    //extract docx
    public function extractTextFromDocx($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            $text .= $this->extractTextFromSection($section);
        }

        return $text;
    }

    //extract docx
    private function extractTextFromSection($element)
    {
        $text = '';

        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof TextRun) {
                $text .= $this->extractTextFromTextRun($childElement);
            } elseif ($childElement instanceof Text) {
                $text .= $childElement->getText();
            }
        }

        return $text;
    }

    //extract docx
    private function extractTextFromTextRun(TextRun $textRun)
    {
        $text = '';

        foreach ($textRun->getElements() as $textElement) {
            if ($textElement instanceof Text) {
                $text .= $textElement->getText();
            }
        }

        return $text;
    }

    // extract excel
    public function extractTextFromExcel($filePath)
    {
        $spreadsheet = SpreadsheetIOFactory::load($filePath);
        $text = '';

        // Loop through each sheet in the spreadsheet
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            // Loop through each row in the sheet
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells in the row

                foreach ($cellIterator as $cell) {
                    $text .= $cell->getValue() . ' '; // Append the cell's value
                }

                $text .= "\n"; // Newline for each row
            }
        }

        return $text;
    }

    /**
     * Get the folder name based on file type (extension)
     *
     * @param string $extension
     * @return string
     */
    private function getFolderByFileType($extension)
    {
        // Map extensions to specific folder names
        $folders = [
            'pdf' => 'pdf',
            'doc' => 'doc',
            'docx' => 'docx',
            'jpg' => 'images',
            'jpeg' => 'images',
            'png' => 'images',
            'pptx' => 'pptx',
            'csv' => 'csv',
            'xlsx' => 'xlsx',
        ];

        // Return the appropriate folder or default to 'other' if not found
        return isset($folders[$extension]) ? $folders[$extension] : 'other';
    }
}
