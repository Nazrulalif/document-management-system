<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User_organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SearchBar extends Component
{
    public $query = '';
    public $folderResults = [];
    public $documentResults = [];

    // public function mount()
    // {
    //     // Initialize the query with the current request query parameter, if it exists
    //     $this->query = request()->get('query', '');
    // }
    public function updatedQuery()
    {
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        // Query for folders
        $this->folderResults = Folder::select('folders.*')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id') // Left join with shared_folders
            ->where(function ($query) use ($user_orgs) {
                // Apply org_guid filter and folder name search query
                $query->where(function ($subQuery) use ($user_orgs) {
                    $subQuery->whereIn('shared_folders.org_guid', $user_orgs)
                        ->orWhereNull('shared_folders.org_guid'); // Include non-shared folders
                })
                    ->where('folders.folder_name', 'like', "%{$this->query}%");
            })
            ->take(5) // Limit to 5 results
            ->get();


        $this->documentResults = Document::select('documents.*')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
            ->where(function ($query) use ($user_orgs) {
                $query->where(function ($subQuery) use ($user_orgs) {
                    $subQuery->whereIn('shared_documents.org_guid', $user_orgs)
                        ->orWhereNull('shared_documents.org_guid'); // Include non-shared documents
                })
                    ->where('documents.doc_title', 'like', "%{$this->query}%");
            })
            ->take(5) // Limit to 5 results
            ->get();
    }
    public function render()
    {
        return view('livewire.search-bar');
    }
}
