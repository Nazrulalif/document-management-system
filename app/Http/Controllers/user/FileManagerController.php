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

            $folders = Folder::with(['children', 'documents', 'sharedOrganizations']) // Eager load shared organizations
                ->select('folders.*', 'folders.uuid as uuid', 'folders.id as id', 'folders.folder_name as item_name', 'users.full_name as full_name')
                ->join('users', 'users.id', '=', 'folders.created_by')
                ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                ->where(function ($query) use ($user_orgs) {
                    $query->whereIn('shared_folders.org_guid', $user_orgs)
                        ->orWhereNull('shared_folders.org_guid');
                })
                ->whereNull('folders.parent_folder_guid')
                ->groupBy('folders.id')
                ->orderBy('folders.created_at', 'DESC')
                ->get()
                ->map(function ($folder) use ($starredFolders) {
                    // Aggregating the organization names and GUIDs
                    $folder->shared_orgs = $folder->sharedOrganizations->pluck('org_name')->implode("\n");
                    $folder->shared_orgs_guid = $folder->sharedOrganizations->pluck('id')->implode(",");
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
                    $query->whereIn('shared_documents.org_guid', $user_orgs)
                        ->orWhereNull('shared_documents.org_guid');
                })
                ->whereNull('documents.folder_guid')
                ->orderBy('documents.created_at', 'DESC')
                ->get()
                ->map(function ($document) use ($starredDocs) {
                    // Aggregating the organization names and GUIDs
                    $document->shared_orgs = $document->sharedOrganizations->pluck('org_name')->implode("\n");
                    $document->shared_orgs_guid = $document->sharedOrganizations->pluck('id')->implode(",");
                    // Check if the document is starred
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
        $folder = Folder::where('uuid', $uuid)->with(['children', 'documents', 'creator'])->first();

        if (!$folder) {
            return redirect()->route('file-manager.user')->with('error', 'Folder not found or has been deleted.');
        }
        if ($request->ajax()) {
            // Fetch starred folders and documents for the authenticated user
            $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
            $starredDocs = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();

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
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
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
