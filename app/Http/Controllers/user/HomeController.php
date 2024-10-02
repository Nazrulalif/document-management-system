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
            ->where(function ($query) {
                $query->where('folders.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1');
            })
            ->orderBy('folders.id', 'DESC')
            ->take(5)
            ->get();

        $document = Document::select('*', 'documents.uuid as uuid', 'documents.id as id')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('users.org_guid', Auth::user()->org_guid)
            ->orWhere('users.role_guid', '1')
            ->orderBy('documents.id', 'DESC')
            ->take(10)
            ->get();
        return view('user.index', compact('folder', 'document'));
    }
}
