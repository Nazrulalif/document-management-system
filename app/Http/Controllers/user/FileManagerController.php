<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\shared_folder;
use App\Models\Starred_document;
use App\Models\Starred_folder;
use App\Models\User_organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

            $folders = Folder::with(['children', 'documents'])
                ->select(
                    'folders.*',
                    'folders.uuid as uuid',
                    'folders.id as id',
                    'folders.folder_name as item_name',
                    'users.full_name as full_name',
                    DB::raw('NULL as doc_type'),
                    DB::raw('IF(shared_folders.folder_guid IS NOT NULL, 1, 0) as is_shared'),
                    DB::raw('GROUP_CONCAT(share_name.org_name) as shared_orgs'), // Aggregate shared org names
                    DB::raw('GROUP_CONCAT(share_name.id) as shared_orgs_guid') // Aggregate shared org IDs
                )
                ->join('users', 'users.id', '=', 'folders.created_by')
                ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                ->where(function ($query) use ($user_orgs) {
                    // Only check for shared folders with the user's organizations or non-shared folders
                    $query->whereIn('shared_folders.org_guid', $user_orgs) // Check for shared folders
                        ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
                })
                ->whereNull('folders.parent_folder_guid') // Ensure only top-level folders are fetched
                ->groupBy('folders.id') // Group by folder ID for aggregate functions
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
                'users.full_name as full_name',
                DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names
                DB::raw('share_name.id as shared_orgs_guid'), // Aggregate shared org names


            )
                ->join('users', 'users.id', '=', 'documents.upload_by')
                ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Join with the shared_folder table
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid') // Join with organizations for shared names
                ->where(function ($query) use ($user_orgs) {
                    $query->whereIn('shared_documents.org_guid', $user_orgs)
                        ->orWhereNull('shared_documents.org_guid');
                })
                ->whereNull('documents.folder_guid')
                ->orderBy('documents.created_at', 'DESC') // Order by newest first
                ->get()
                ->map(function ($document) use ($starredDocs) {
                    $document->is_starred = in_array($document->id, $starredDocs);
                    return $document;
                });


            // Merge folders and documents
            $data = $folders->concat($rootDocuments);

            // Return data via DataTables
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        $company = User_organization::join('organizations', 'organizations.id', '=', 'user_organizations.org_guid')
            ->where('user_organizations.user_guid', Auth::user()->id)
            ->get();
        return view('user.file-manager.file-manager', compact('company'));
    }

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
            return redirect()->route('file-manager.user')->with('error', 'Folder not found or has been deleted.');
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
            $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');


            // Prepare the children folders
            $subfolders = $folder->children->map(function ($childFolder) use ($starredFolders) {
                return [
                    'id' => $childFolder->id,
                    'uuid' => $childFolder->uuid,
                    'shared_orgs' => $childFolder->shared_orgs,
                    'shared_orgs_guid' => $childFolder->shared_orgs_guid,
                    'item_name' => $childFolder->folder_name,
                    // 'org_name' => $childFolder->organization->org_name, // Ensure the relation exists
                    'full_name' => $childFolder->creator->full_name,
                    'doc_type' => null,
                    'is_starred' => in_array($childFolder->id, $starredFolders),
                ];
            });


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
                ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid')
                ->where('folders.uuid', '=', $uuid)
                ->where(function ($query) use ($user_orgs) {
                    $query->whereIn('shared_documents.org_guid', $user_orgs);
                })
                ->orderBy('documents.created_at', 'DESC') // Order by newest first
                ->get()
                ->map(function ($document) use ($starredDocs) {
                    $document->is_starred = in_array($document->id, $starredDocs);
                    return $document;
                });



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


        // Passing the folder to the view
        return view('user.file-manager.file-manager-item', compact('uuid', 'folder_id', 'path'));
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
}
