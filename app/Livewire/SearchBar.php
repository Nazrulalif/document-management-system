<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Folder;
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
        // Query for folders
        $this->folderResults = Folder::select('folders.*')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->where(function ($query) {
                // First check if org_guid matches the user's org_guid
                $query->where('folders.org_guid', Auth::user()->org_guid)
                    // Then, apply the folder name search query
                    ->where('folders.folder_name', 'like', "%{$this->query}%");
            })
            ->orWhere(function ($query) {
                // Allow users with role_guid = 1 to see all results
                $query->where('users.role_guid', '1')
                    ->where('folders.folder_name', 'like', "%{$this->query}%");
            })
            ->take(5) // Limit to 5 results
            ->get();

        // Query for documents
        $this->documentResults = Document::select('documents.*')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->where(function ($query) {
                // First check if org_guid matches the user's org_guid
                $query->where('documents.org_guid', Auth::user()->org_guid)
                    // Then, apply the document title search query
                    ->where('documents.doc_title', 'like', "%{$this->query}%");
            })
            ->orWhere(function ($query) {
                // Allow users with role_guid = 1 to see all results
                $query->where('users.role_guid', '1')
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
