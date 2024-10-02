<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Starred_document;
use App\Models\Starred_folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StarredController extends Controller
{
    public function index()
    {
        $starred_folder = Starred_folder::join('folders', 'folders.id', '=', 'starred_folders.folder_guid')
            ->where('starred_folders.user_guid', Auth::user()->id)
            ->get();

        $starred_doc = Starred_document::join('documents', 'documents.id', '=', 'starred_documents.doc_guid')
            ->where('starred_documents.user_guid', Auth::user()->id)
            ->get();


        $starredFolders = Starred_folder::where('user_guid', Auth::user()->id)->pluck('folder_guid')->toArray();
        $starredDoc = Starred_document::where('user_guid', Auth::user()->id)->pluck('doc_guid')->toArray();

        return view('user.starred.starred', compact('starred_folder', 'starred_doc', 'starredFolders', 'starredDoc'));
    }
}
