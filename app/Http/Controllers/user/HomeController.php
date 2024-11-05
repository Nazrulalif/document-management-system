<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use App\Models\User_organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        // $folder = Folder::all();
        $folder = Folder::select('*', 'folders.uuid as uuid', 'folders.id as id')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
            ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
            ->where(function ($query) use ($user_orgs) {
                // Only check for shared folders with the user's organizations or non-shared folders
                $query->whereIn('shared_folders.org_guid', $user_orgs) // Check for shared folders
                    ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
            })
            ->orderBy('folders.id', 'DESC')
            ->take(5)
            ->get();

        $document = Document::select('*', 'documents.uuid as uuid', 'documents.id as id', 'documents.created_at as created_at',)
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Join with the shared_folder table
            ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid') // Join with organizations for shared names
            ->where(function ($query) use ($user_orgs) {
                $query->whereIn('shared_documents.org_guid', $user_orgs)
                    ->orWhereNull('shared_documents.org_guid');
            })
            ->orderBy('documents.id', 'DESC')
            ->take(10)
            ->get();
        return view('user.index', compact('folder', 'document'));
    }
}
