<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\User_organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\HtmlString;

class AdvanceSearch extends Component
{
    use WithPagination;

    public $query = '';
    public $selectedType = 'file'; // Default value
    public $fileType = '';
    public $companies = [];
    public $folderCount = 0; // Store folder count
    public $fileCount = 0;   // Store file count

    public function mount()
    {
        // Load previous search parameters from session storage
        $this->query = session('search_query', '');
        $this->selectedType = session('search_type', 'file');
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

        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        $results = $this->search();

        // $companyList = Organization::all();
        $companyList = Organization::when(Auth::user()->role_guid != 1, function ($query) use ($user_orgs) {
            return $query->whereIn('id', $user_orgs);
        })->get();

        return view('livewire.advance-search', [
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
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

        // Start the query on the Folder model
        $query = Folder::select(
            'users.full_name',
            'folders.uuid',
            'folders.created_at',
            'folders.folder_name',
            'users.profile_picture',

            DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names

        )
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id') // Left join with shared_folders
            ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
            ->when($this->query, function ($query) {
                $query->where('folders.folder_name', 'like', "%{$this->query}%");
            })
            ->when($this->companies, function ($query) {
                $query->whereIn('shared_folders.org_guid', $this->companies);
            });

        // Check user role and apply additional conditions
        if (Auth::user()->role_guid == 1) {
            // Admin user can see all folders
            $folders = $query->paginate(5)->withQueryString();
        } else {
            // Non-admin user sees only their organization's folders
            $folders = $query->where(function ($query) use ($user_orgs) {
                // Only check for shared folders with the user's organizations or non-shared folders
                $query->whereIn('shared_folders.org_guid', $user_orgs) // Check for shared folders
                    ->orWhereNull('shared_folders.org_guid'); // Ensure it can return non-shared folders too
            })

                ->paginate(5)
                ->withQueryString();
        }

        // Update folder count
        $this->folderCount = $folders->total();

        // Highlight the search term in folder names
        foreach ($folders as $folder) {
            // $folder->highlighted_title = str_ireplace($this->query, '<strong>' . $this->query . '</strong>', $folder->folder_name);
            $rawName = mb_convert_encoding($folder->folder_name, 'UTF-8', 'auto');
            $query = mb_convert_encoding($this->query, 'UTF-8', 'auto');
        
            $highlighted = preg_replace_callback(
                '/' . preg_quote($query, '/') . '/i',
                fn($match) => '<strong>' . e($match[0]) . '</strong>',
                e($rawName) // Escape entire string *after* replacement logic
            );
        
            $folder->highlighted_title = new HtmlString($highlighted);
        }

        return $folders; // Return the paginated folders
    }


    private function searchFiles()
    {
        $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

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
            DB::raw('share_name.org_name as shared_orgs'), // Aggregate shared org names
        )
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('document_versions', 'documents.latest_version_guid', '=', 'document_versions.uuid')
            ->leftJoin('shared_documents', 'shared_documents.doc_guid', '=', 'documents.id') // Left join with shared_documents
            ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_documents.org_guid') // Join with organizations for shared names
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
                return $query->whereIn('shared_documents.org_guid', $this->companies);
            });

        if (Auth::user()->role_guid == 1) {
            $documents = $query->paginate(5)->withQueryString();
        } else {
            $documents = $query->where(function ($query) use ($user_orgs) {
                $query->whereIn('shared_documents.org_guid', $user_orgs)
                    ->orWhereNull('shared_documents.org_guid');
            })

                ->paginate(5)
                ->withQueryString();
        }

        $this->fileCount = $documents->total(); // Update file count

        foreach ($documents as $document) {
            $docContent = e(mb_convert_encoding($document->doc_content, 'UTF-8', 'auto'));
            $query = e(mb_convert_encoding($this->query, 'UTF-8', 'auto'));
        
            // Highlight content preview
            $position = stripos($docContent, $query);
            if ($position !== false) {
                $before = 50;
                $after = 50;
                $start = max($position - $before, 0);
                $end = min($position + strlen($query) + $after, strlen($docContent));
                $excerpt = mb_substr($docContent, $start, $end - $start, 'UTF-8');
        
                $highlightedExcerpt = preg_replace_callback(
                    '/' . preg_quote($query, '/') . '/i',
                    fn ($match) => '<mark>' . e($match[0]) . '</mark>',
                    $excerpt
                );
        
                $document->highlighted_content = new HtmlString('... ' . $highlightedExcerpt . ' ...');
            } else {
                $document->highlighted_content = new HtmlString(e(Str::limit($docContent, 200)));
            }
        
            // Highlight title
            $title = e(mb_convert_encoding($document->doc_title, 'UTF-8', 'auto'));
            $highlightedTitle = preg_replace_callback(
                '/' . preg_quote($query, '/') . '/i',
                fn ($match) => '<strong>' . e($match[0]) . '</strong>',
                $title
            );
            $document->highlighted_title = new HtmlString($highlightedTitle);
        
            // Highlight keywords
            $keywords = explode(', ', $document->doc_keyword);
            $highlightedKeywords = array_map(function ($keyword) use ($query) {
                $escaped = e($keyword);
                return new HtmlString(preg_replace_callback(
                    '/' . preg_quote($query, '/') . '/i',
                    fn ($match) => '<mark>' . e($match[0]) . '</mark>',
                    $escaped
                ));
            }, $keywords);
        
            $document->highlighted_keywords = $highlightedKeywords;
        }

        return $documents; // Return the paginated documents
    }
}
