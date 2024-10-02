<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Starred_document;
use App\Models\Starred_folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileManagerController extends Controller
{
    public function index()
    {

        $folders = Folder::with(['children', 'documents'])
            ->select('*', 'folders.uuid as uuid', 'folders.id as id')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->where(function ($query) {
                $query->where('folders.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1');
            })
            ->whereNull('folders.parent_folder_guid')
            ->get();

        // Fetch root-level documents where folder_guid is null
        $rootDocuments = Document::select('*', 'documents.uuid as uuid', 'documents.id as id')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('users.org_guid', Auth::user()->org_guid)
            ->orWhere('users.role_guid', '1')
            ->get();

        $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
        $starredDoc = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();

        return view('user.file-manager.file-manager', [
            'folders' => $folders,
            'documents' => $rootDocuments,
            'starredFolders' => $starredFolders,
            'starredDoc' => $starredDoc,
        ]);
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

    public function show_folder($uuid)
    {


        // Fetch folder with its children, documents, and the creator's user information
        $folder = Folder::with(['children', 'documents', 'creator']) // Eager load creator relationship
            ->where('folders.uuid', '=', $uuid)
            ->first();


        $path = $this->getFolderPath($folder);


        $document = Document::select('*', 'documents.id as id')
            ->join('folders', 'folders.id', '=', 'documents.folder_guid')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('folders.uuid', '=', $uuid)
            ->where(function ($query) {
                // Ensure either the document's organization GUID matches or the user is an admin
                $query->where('documents.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1');
            })
            ->get();
        $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
        $starredDoc = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();

        return view('user.file-manager.file-manager-item', [
            'folder' => $folder,
            'path' => $path,
            'documents' => $document,
            'starredFolders' => $starredFolders,
            'starredDoc' => $starredDoc,
        ]);
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
