<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {

        // $folder = Folder::all();
        $folder = Folder::select('*', 'folders.uuid as uuid', 'folders.id as id')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id') // Join with the shared_folder table
            ->where(function ($query) {
                $query->where('folders.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1');
            })
            ->where(function ($query) {
                $query->orWhere('shared_folders.org_guid', Auth::user()->org_guid) // Check for shared folders
                    ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
            })
            ->orderBy('folders.id', 'DESC')
            ->take(5)
            ->get();

        $document = Document::select('*', 'documents.uuid as uuid', 'documents.id as id', 'documents.created_at as created_at',)
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
            ->orderBy('documents.id', 'DESC')
            ->take(10)
            ->get();
        return view('user.index', compact('folder', 'document'));
    }
}
