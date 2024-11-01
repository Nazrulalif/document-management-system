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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
            $starredDocs = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();

            if (Auth::user()->role_guid == 1) {

                $folders = Folder::with(['children', 'documents'])
                    ->select(
                        'folders.*',
                        'folders.uuid as uuid',
                        'folders.id as id',
                        'folders.folder_name as item_name',
                        'organizations.org_name as org_name', // Org name for the folder
                        DB::raw('GROUP_CONCAT(DISTINCT share_name.org_name SEPARATOR "\n") as shared_orgs'), // Aggregate shared org names
                        DB::raw('GROUP_CONCAT(DISTINCT share_name.id SEPARATOR ",") as shared_orgs_guid'), // Aggregate shared org names
                        'users.full_name as full_name',
                        DB::raw('NULL as doc_type'),
                        DB::raw('MAX(IF(shared_folders.folder_guid IS NOT NULL, 1, 0)) as is_shared') // Handle duplicates
                    )
                    ->join('users', 'users.id', '=', 'folders.created_by') // Join with the users table
                    ->join('organizations', 'organizations.id', '=', 'folders.org_guid') // Join with the organization's folder creator
                    ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                    ->whereNull('folders.parent_folder_guid') // Ensure top-level folders
                    ->groupBy('folders.id') // Group by folder ID to avoid duplicates
                    ->orderBy('folders.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($folder) use ($starredFolders) {
                        $folder->is_starred = in_array($folder->id, $starredFolders);
                        return $folder;
                    });


                // Fetch documents and add `is_starred` field
                $rootDocuments = Document::select(
                    'documents.*',
                    'documents.uuid as uuid',
                    'documents.id as id',
                    'documents.doc_title as item_name',
                    'documents.doc_type as doc_type',
                    'organizations.org_name as org_name', // Org name for the folder
                    'users.full_name as full_name',
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.org_name SEPARATOR "\n") as shared_orgs'), // Aggregate shared org names
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.id SEPARATOR ",") as shared_orgs_guid'), // Aggregate shared org names
                    DB::raw('MAX(IF(shared_documents.doc_guid IS NOT NULL, 1, 0)) as is_shared') // Handle duplicates
                )
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid') // Join with organizations for shared names
                    ->whereNull('documents.folder_guid') // Only root-level documents
                    ->groupBy('documents.id') // Group by document ID to avoid duplicates
                    ->orderBy('documents.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        $document->is_starred = in_array($document->id, $starredDocs); // Check if the document is starred
                        return $document;
                    });
            } else {

                // Fetch all folders with necessary joins and add `is_starred` field
                $folders = Folder::with(['children', 'documents'])
                    ->select(
                        'folders.*',
                        'folders.uuid as uuid',
                        'folders.id as id',
                        'folders.folder_name as item_name',
                        'organizations.org_name as org_name', // Org name for the folder
                        'users.full_name as full_name',
                        DB::raw('NULL as doc_type'),
                        DB::raw('IF(shared_folders.folder_guid IS NOT NULL, 1, 0) as is_shared'),
                        DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names
                        DB::raw('share_name.id as shared_orgs_guid'), // Aggregate shared org names

                    )
                    ->join('users', 'users.id', '=', 'folders.created_by')
                    ->join('organizations', 'organizations.id', '=', 'folders.org_guid')
                    ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                    ->where(function ($query) {
                        $query->orWhere('shared_folders.org_guid', Auth::user()->org_guid) // Check for shared folders
                            ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
                    })
                    ->whereNull('folders.parent_folder_guid') // Ensure only top-level folders are fetched
                    ->orderBy('folders.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($folder) use ($starredFolders) {
                        $folder->is_starred = in_array($folder->id, $starredFolders);
                        return $folder;
                    });

                // Fetch documents and add `is_starred` field
                $rootDocuments = Document::select(
                    'documents.*',
                    'documents.uuid as uuid',
                    'documents.id as id',
                    'documents.doc_title as item_name',
                    'documents.doc_type as doc_type',
                    'organizations.org_name as org_name', // Org name for the folder
                    'users.full_name as full_name',
                    DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names
                )
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Join with the shared_folder table
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid') // Join with organizations for shared names
                    ->where(function ($query) {
                        $query->where('shared_documents.org_guid', Auth::user()->org_guid) // Check for shared documents within the allowed set
                            ->orWhereNull('shared_documents.org_guid'); // Allow non-shared documents as well
                    })
                    ->whereNull('documents.folder_guid')
                    ->orderBy('documents.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        $document->is_starred = in_array($document->id, $starredDocs);
                        return $document;
                    });
            }


            // Merge folders and documents
            $data = $folders->concat($rootDocuments);

            // Return data via DataTables
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        $company = Organization::all();


        return view('admin.file-manager.file-manager', compact('company'));
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
        $folder_id = Folder::where('uuid', $uuid)->first();
        // Fetch the main folder with its children and documents
        $folder = Folder::with(['children', 'documents', 'creator'])
            ->select(
                '*',
                'folders.uuid as uuid',
                'folders.id as id',
                'folders.folder_name as item_name',
                DB::raw('NULL as doc_type')
            )
            ->where('uuid', $uuid)
            ->first();
        if (!$folder) {
            return redirect()->route('fileManager.index')->with('error', 'Folder not found or has been deleted.');
        }
        if ($request->ajax()) {
            // Fetch starred folders and documents for the authenticated user
            $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
            $starredDocs = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();


            // Fetch the main folder with its children and documents
            $folder = Folder::with(['children', 'documents', 'creator'])
                ->select(
                    '*',
                    'folders.uuid as uuid',
                    'folders.id as id',
                    'folders.folder_name as item_name',
                    DB::raw('NULL as doc_type')
                )
                ->where('uuid', $uuid)
                ->first();



            if (!$folder) {
                return response()->json(['error' => 'Folder not found'], 404);
            }

            // Map the main folder to include the starred status
            $folder->is_starred = in_array($folder->id, $starredFolders);

            // Prepare the children folders
            $subfolders = $folder->children->map(function ($childFolder) use ($starredFolders) {
                return [
                    'id' => $childFolder->id,
                    'uuid' => $childFolder->uuid,
                    'shared_orgs' => $childFolder->shared_orgs,
                    'shared_orgs_guid' => $childFolder->shared_orgs_guid,
                    'item_name' => $childFolder->folder_name,
                    'org_name' => $childFolder->organization->org_name, // Ensure the relation exists
                    'full_name' => $childFolder->creator->full_name,
                    'doc_type' => null,
                    'is_starred' => in_array($childFolder->id, $starredFolders),
                ];
            });

            if (Auth::user()->role_guid == 1) {
                // Fetch documents related to the folder
                $documents = Document::select(
                    '*',
                    'documents.uuid as uuid',
                    'documents.id as id',
                    'documents.doc_title as item_name',
                    'documents.doc_type as doc_type',
                    DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names
                    DB::raw('share_name.id as shared_orgs_guid'), // Aggregate shared org names

                )
                    ->join('folders', 'folders.id', '=', 'documents.folder_guid')
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid')
                    ->where('folders.uuid', '=', $uuid)
                    ->orderBy('documents.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        $document->is_starred = in_array($document->id, $starredDocs);
                        return $document;
                    });
            } else {
                // Fetch documents related to the folder
                $documents = Document::select(
                    '*',
                    'documents.uuid as uuid',
                    'documents.id as id',
                    'documents.doc_title as item_name',
                    'documents.doc_type as doc_type',
                    DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names

                )
                    ->join('folders', 'folders.id', '=', 'documents.folder_guid')
                    ->join('users', 'users.id', '=', 'documents.upload_by')
                    ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                    ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
                    ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid')
                    ->where('folders.uuid', '=', $uuid)
                    ->where(function ($query) {
                        $query->where('shared_documents.org_guid', Auth::user()->org_guid) // Check for shared documents within the allowed set
                            ->orWhereNull('shared_documents.org_guid'); // Allow non-shared documents as well
                    })
                    ->orderBy('documents.created_at', 'DESC') // Order by newest first
                    ->get()
                    ->map(function ($document) use ($starredDocs) {
                        $document->is_starred = in_array($document->id, $starredDocs);
                        return $document;
                    });
            }



            // Merge subfolders and documents
            $data = $subfolders->concat($documents);

            // Return data via DataTables
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        $folder = Folder::with(['children', 'documents', 'creator'])
            ->select(
                '*',
                'folders.uuid as uuid',
                'folders.id as id',
                'folders.folder_name as item_name',
                DB::raw('NULL as doc_type')
            )
            ->where('uuid', $uuid)
            ->first();

        $path = $this->getFolderPath($folder);
        $company = Organization::all();
        $folder_shared_id = shared_folder::where('folder_guid', $folder_id->id)->first();

        // Passing the folder to the view
        return view('admin.file-manager.file-manager-item', compact('uuid', 'folder_id', 'path', 'company', 'folder_shared_id'));
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
            'new_folder_name' => 'required',
        ]);

        try {

            $folder = Folder::create([
                'folder_name' => $request->new_folder_name,
                'parent_folder_guid' => $request->new_folder_id,
                'created_by' => Auth::user()->id,
                'org_guid' => Auth::user()->org_guid,
                'is_meeting' => 'N',
                'is_all_company' => 'N',
            ]);

            $orgNames = $request->input('org_name') ? $request->input('org_name') : Auth::user()->org_guid;

            if (!empty($orgNames)) {
                $folder = Folder::where('id', $folder->id)->first();
                $folder->update(['is_all_company' => 'N']);
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
        // Get all documents related to this folder
        $documents = Document::join('document_versions', 'document_versions.doc_guid', '=', 'documents.id')
            ->where('documents.folder_guid', '=', $folder->id)
            ->get();

        foreach ($documents as $doc) {
            $filePath = $doc->file_path;

            // Delete the file from storage if it exists
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Delete associated document versions
            DocumentVersion::where('doc_guid', $doc->id)->delete();

            // Delete the document itself
            Document::join('document_versions', 'document_versions.doc_guid', '=', 'documents.id')
                ->where('documents.folder_guid', '=', $folder->id)->delete();
        }

        // Find all subfolders of the current folder
        $subfolders = Folder::where('parent_folder_guid', $folder->id)->get();

        // Recursively delete each subfolder and its contents
        foreach ($subfolders as $subfolder) {
            $this->deleteFolderContents($subfolder); // Recursive call to delete subfolder contents
            $subfolder->delete(); // Delete the subfolder itself
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
        $validatedData = $request->validate([
            'folder_name' => 'required|string|max:255',
        ]);

        try {
            // Update the folder name
            $folder->update([
                'folder_name' => $validatedData['folder_name'],
            ]);

            // Retrieve existing shared organizations for the main folder
            $existingSharedOrg = shared_folder::where('folder_guid', $id)->first();

            // New organization(s) from the request or fallback to user's org
            $newSharedOrg = $request->input('org_name_edit') ?? Auth::user()->org_guid;

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
                ['org_guid' => $newOrgGuid]        // Update org_guid
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
                ['org_guid' => $newOrgGuid] // Update org_guid
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
        $validatedData = $request->validate([
            'edit_file' => 'required|string|max:255',
        ]);

        try {
            // Update the folder name
            $file->update([
                'doc_title' => $validatedData['edit_file'],
            ]);

            // Retrieve existing shared organizations
            $existingSharedOrgs = shared_document::where('doc_guid', $id)->first();

            // New organizations from the request
            $newSharedOrgs = $request->input('org_name_edit') ? $request->input('org_name_edit') : Auth::user()->org_guid;

            $existingSharedOrgs->update([
                'org_guid' => $newSharedOrgs
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
        $documents = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->whereIn('documents.id', $ids)
            ->get();

        // Iterate over each document
        foreach ($documents as $document) {
            $filePath = $document->file_path;
            AuditLog::create([
                'action' => "Deleted",
                'model' => 'File',
                'changes' => $document->doc_title,
                'user_guid' => Auth::user()->id,
                'ip_address' => request()->ip(),
            ]);
            // Assuming $filePath is relative to the 'public' disk (e.g., 'uploads/myfile.pdf')
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath); // Delete the file from 'public' disk
            }

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

        $document = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->where('documents.id', '=', $id)
            ->first();

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
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath); // Delete the file from 'public' disk
                }

                // Delete the version record from the database
                $version->delete();
            }

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

        $orgNames = $request->input('org_name_file') ? $request->input('org_name_file') : Auth::user()->org_guid;

        // Handle the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize(); // Size in bytes


            // Define folder based on file extension
            $folder = $this->getFolderByFileType($extension); // Separate function to get the folder

            // Store the file in the corresponding folder in 'storage/app/uploads/{folder}'
            $filePath = $file->storeAs('uploads/' . $folder, $uniqueFileName, 'public');

            $folder_id = $request->folder_id;

            // Initialize the document content variable
            $docContent = null;

            // If the file is a PDF, extract its content
            if ($extension === 'pdf') {
                try {
                    $pdfParser = new Parser();
                    $pdf = $pdfParser->parseFile(public_path('storage/' . $filePath));
                    $docContent = $pdf->getText(); // Extract the text from the PDF
                } catch (\Exception $e) {
                    $docContent = "Sorry, unable to extract the text";
                }
            }

            // If the file is a DOCX, extract its content using the method defined below
            if ($extension === 'docx') {
                try {
                    $docContent = $this->extractTextFromDocx(public_path('storage/' . $filePath)); // Extract text from DOCX
                } catch (\Exception $e) {
                    $docContent = "Sorry, unable to extract the text";
                }
            }


            // Handle Excel file
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                try {
                    $docContent = $this->extractTextFromExcel(public_path('storage/' . $filePath)); // Extract text from Excel

                } catch (\Exception $e) {
                    $docContent = "Sorry, unable to extract the text";
                }
            }

            // Check if OCR content was sent for images
            if (in_array($extension, ['jpeg', 'jpg', 'png']) && $request->has('ocr_content')) {
                try {
                    $docContent = $request->input('ocr_content'); // Use the extracted OCR content from the request

                } catch (\Exception $e) {
                    $docContent = "Sorry, unable to extract the text";
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
                'org_guid' => Auth::user()->org_guid,
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
