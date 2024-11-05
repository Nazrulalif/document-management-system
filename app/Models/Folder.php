<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Folder extends Model
{
    use HasFactory;
    protected $table = 'folders';
    protected $fillable = [
        'id',
        'uuid',
        'folder_name',
        'created_by',
        'parent_folder_guid',
        'is_meeting',
        'is_all_company',

    ];

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_folder_guid');
    }

    public function children()
    {
        $query = $this->hasMany(Folder::class, 'parent_folder_guid', 'id');

        if (Auth::user()->role_guid == 1) {
            // Admin: Return all child folders, including shared ones.
            return $query
                ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                ->select(
                    'folders.*',
                    DB::raw('MAX(IF(shared_folders.folder_guid IS NOT NULL, 1, 0)) as is_shared'), // Check shared status
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.org_name SEPARATOR "\n") as shared_orgs'), // Aggregate shared org names
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.id SEPARATOR ",") as shared_orgs_guid'), // Aggregate shared org names
                )
                ->groupBy('folders.id') // Group to avoid duplicates
                ->orderBy('folders.created_at', 'DESC'); // Order by newest first

        } else {
            $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

            // Non-admin: Limit by org_guid or shared folders involving the user's org.
            return $query
                ->leftJoin('shared_folders', 'shared_folders.folder_guid', '=', 'folders.id')
                ->leftJoin('organizations as share_name', 'share_name.id', '=', 'shared_folders.org_guid')
                ->where(function ($query) use ($user_orgs) {
                    $query->whereIn('shared_folders.org_guid', $user_orgs);
                })
                ->select(
                    'folders.*',
                    DB::raw('MAX(IF(shared_folders.folder_guid IS NOT NULL, 1, 0)) as is_shared'), // Check shared status
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.org_name SEPARATOR "\n") as shared_orgs'), // Aggregate shared org names
                    DB::raw('GROUP_CONCAT(DISTINCT share_name.id SEPARATOR ",") as shared_orgs_guid'), // Aggregate shared org names

                )
                ->groupBy('folders.id') // Group to avoid duplicates
                ->orderBy('folders.created_at', 'DESC'); // Order by newest first

        }
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_guid', 'id');
    }
    // Define relationship with the creator (user)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // public function organization()
    // {
    //     return $this->belongsTo(Organization::class, 'org_guid');
    // }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Use 'uuid' for route model binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
