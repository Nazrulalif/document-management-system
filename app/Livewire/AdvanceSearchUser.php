<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class AdvanceSearchUser extends Component
{
    use WithPagination;

    public $query = '';
    public $selectedType = 'folder'; // Default value
    public $fileType = '';
    public $companies = [];
    public $folderCount = 0; // Store folder count
    public $fileCount = 0;   // Store file count

    public function mount()
    {
        // Load the search query from the request
        $this->query = request()->query('query', $this->query);

        // Load previous search parameters from session storage
        // $this->query = session('search_query', '');
        $this->selectedType = session('search_type', 'folder');
        $this->fileType = session('file_type', '');
        $this->companies = session('companies', []);
    }

    public function updatedQuery()
    {
        $this->resetPage(); // Reset pagination when query updates
        // Store query in session storage
        session(['search_query' => $this->query]);
    }

    public function updatedSelectedType()
    {
        $this->resetPage(); // Reset pagination when type updates
        session(['search_type' => $this->selectedType]);
    }

    public function updatedFileType()
    {
        $this->resetPage(); // Reset pagination when file type updates
        session(['file_type' => $this->fileType]);
    }

    public function updatedCompanies()
    {
        $this->resetPage(); // Reset pagination when companies updates
        session(['companies' => $this->companies]);
    }

    public function render()
    {
        $results = $this->search();

        $companyList = Organization::all();

        return view('livewire.advance-search-user', [
            'results' => $results,
            'companyList' => $companyList,
            'folderCount' => $this->folderCount,
            'fileCount' => $this->fileCount,
        ]);
    }

    public function search()
    {
        if ($this->selectedType === 'file') {
            return $this->searchFiles();
        } elseif ($this->selectedType === 'folder') {
            return $this->searchFolders();
        }

        return collect(); // Return an empty collection if not searching for files or folders
    }

    private function searchFolders()
    {
        // Start the query on the Folder model
        $query = Folder::select(
            'users.full_name',
            'folders.uuid',
            'folders.created_at',
            'folders.folder_name',
            'organizations.org_name'
        )
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->join('organizations', 'organizations.id', '=', 'folders.org_guid')
            ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id') // Left join with shared_folders
            ->when($this->query, function ($query) {
                $query->where('folders.folder_name', 'like', "%{$this->query}%");
            })
            ->when($this->companies, function ($query) {
                $query->whereIn('folders.org_guid', $this->companies);
            });

        // Check user role and apply additional conditions
        if (Auth::user()->role_guid == 1) {
            // Admin user can see all folders
            $folders = $query->paginate(5)->withQueryString();
        } else {
            // Non-admin user sees only their organization's folders
            $folders = $query->where(function ($query) {
                $query->where('folders.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1'); // Include folders created by admins
            })
                ->where(function ($query) {
                    $query->orWhere('shared_folders.org_guid', Auth::user()->org_guid) // Check for shared folders
                        ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
                })
                ->paginate(5)
                ->withQueryString();
        }

        // Update folder count
        $this->folderCount = $folders->total();

        // Highlight the search term in folder names
        foreach ($folders as $folder) {
            $folder->highlighted_title = str_ireplace($this->query, '<strong>' . $this->query . '</strong>', $folder->folder_name);
        }

        return $folders; // Return the paginated folders
    }


    private function searchFiles()
    {
        $query = Document::select(
            'users.full_name',
            'users.profile_picture',
            'documents.upload_by',
            'documents.latest_version_guid',
            'documents.doc_type',
            'documents.created_at',
            'documents.doc_title',
            'documents.doc_keyword',
            'document_versions.doc_content',
            'organizations.org_name'
        )
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->join('document_versions', 'documents.latest_version_guid', '=', 'document_versions.uuid')
            ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
            ->when($this->query, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('documents.doc_title', 'like', "%{$this->query}%")
                        ->orWhere('document_versions.doc_content', 'like', "%{$this->query}%")
                        ->orWhere('documents.doc_keyword', 'like', "%{$this->query}%");
                });
            })
            ->when($this->fileType, function ($query) {
                return $query->where('documents.doc_type', $this->fileType);
            })
            ->when($this->companies, function ($query) {
                return $query->whereIn('documents.org_guid', $this->companies);
            });

        if (Auth::user()->role_guid == 1) {
            $documents = $query->paginate(5)->withQueryString();
        } else {
            // Non-admin user sees only their organization's folders
            $documents = $query->where(function ($query) {
                $query->where('documents.org_guid', Auth::user()->org_guid)
                    ->orWhere('users.role_guid', '1'); // Include folders created by admins
            })
                ->where(function ($query) {
                    $query->where('shared_documents.org_guid', Auth::user()->org_guid) // Check for shared documents
                        ->orWhereNull('shared_documents.org_guid'); // Allow non-shared documents
                })
                ->paginate(5)
                ->withQueryString();
        }

        $this->fileCount = $documents->total(); // Update file count

        foreach ($documents as $document) {
            $content = mb_convert_encoding($document->doc_content,  'UTF-8', 'auto');
            $query = mb_convert_encoding($this->query,  'UTF-8', 'auto');

            $position = stripos($content, $query);

            if ($position !== false) {
                $before = 50;
                $after = 50;
                $start = max($position - $before, 0);
                $end = min($position + strlen($query) + $after, strlen($content));
                $excerpt = mb_substr($content, $start, $end - $start, 'UTF-8');
                $highlightedExcerpt = str_ireplace($query, '<mark>' . $query . '</mark>', $excerpt);
                $document->highlighted_content = '... ' . $highlightedExcerpt . ' ...';
            } else {
                $document->highlighted_content = Str::limit($content, 200);
            }

            $title = mb_convert_encoding($document->doc_title, 'UTF-8', 'auto');
            $document->highlighted_title = str_ireplace($query, '<strong>' . $query . '</strong>', $title);

            $keywords = explode(', ', $document->doc_keyword);
            $highlightedKeywords = array_map(function ($keyword) use ($query) {
                return str_ireplace($query, '<mark>' . $query . '</mark>', trim($keyword));
            }, $keywords);
            $document->highlighted_keywords = $highlightedKeywords;
        }

        return $documents; // Return the paginated documents
    }
}
