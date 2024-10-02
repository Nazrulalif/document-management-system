<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {

        return view('user.search.advance-search-user');
    }
}
