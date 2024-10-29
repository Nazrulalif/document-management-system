<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Starred_document;
use App\Models\Starred_folder;
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

            $folders = Folder::with(['children', 'documents'])
                ->select(
                    '*',
                    'folders.uuid as uuid',
                    'folders.id as id',
                    'folders.folder_name as item_name',
                    DB::raw('NULL as doc_type')
                )
                ->join('users', 'users.id', '=', 'folders.created_by')
                ->join('organizations', 'organizations.id', '=', 'folders.org_guid')
                ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id') // Join with the shared_folders table
                ->where(function ($query) {
                    $query->where('folders.org_guid', Auth::user()->org_guid) // Check if folders belong to user's organization
                        ->orWhere('users.role_guid', '1'); // Allow access for users with role_id = 1
                })
                ->where(function ($query) {
                    $query->orWhere('shared_folders.org_guid', Auth::user()->org_guid) // Check for shared folders
                        ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
                })
                ->whereNull('folders.parent_folder_guid')
                ->get()
                ->map(function ($folder) use ($starredFolders) {
                    $folder->is_starred = in_array($folder->id, $starredFolders);
                    return $folder;
                });

            // Fetch documents and add `is_starred` field
            $rootDocuments = Document::select(
                '*',
                'documents.uuid as uuid',
                'documents.id as id',
                'documents.doc_title as item_name',
                'documents.doc_type as doc_type'
            )
                ->join('users', 'users.id', '=', 'documents.upload_by')
                ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Join with the shared_folder table
                ->where(function ($query) {
                    $query->where('documents.org_guid', Auth::user()->org_guid) // Belongs to user's organization
                        ->orWhere('users.role_guid', '1'); // Or role_guid is 1 (admin access)
                })
                ->where(function ($query) {
                    $query->where('shared_documents.org_guid', Auth::user()->org_guid) // Check for shared documents within the allowed set
                        ->orWhereNull('shared_documents.org_guid'); // Allow non-shared documents as well
                })
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

        return view('user.file-manager.file-manager');
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
                    'item_name' => $childFolder->folder_name,
                    'org_name' => $childFolder->organization->org_name, // Ensure the relation exists
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
                'documents.doc_type as doc_type'
            )
                ->join('folders', 'folders.id', '=', 'documents.folder_guid')
                ->join('users', 'users.id', '=', 'documents.upload_by')
                ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
                ->where('folders.uuid', '=', $uuid)
                ->where(function ($query) {
                    // Ensure either the document's organization GUID matches or the user is an admin
                    $query->where('documents.org_guid', Auth::user()->org_guid)
                        ->orWhere('users.role_guid', '1');
                })
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
