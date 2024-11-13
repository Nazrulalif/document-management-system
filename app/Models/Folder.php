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

    public function sharedOrganizations()
    {
        return $this->belongsToMany(Organization::class, 'shared_folders', 'folder_guid', 'org_guid');
    }
    // Folder.php
    public function sharedFolders()
    {
        return $this->hasMany(shared_folder::class, 'folder_guid', 'id');
    }


    // Define an accessor to get shared org names as concatenated string
    public function getSharedOrgNamesAttribute()
    {
        return $this->sharedOrganizations->pluck('org_name')->join("\n");
    }

    // Define an accessor to get shared org ids as concatenated string
    public function getSharedOrgIdsAttribute()
    {
        return $this->sharedOrganizations->pluck('id')->join(",");
    }


    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_folder_guid');
    }

    // public function children()
    // {
    //     $query = $this->hasMany(Folder::class, 'parent_folder_guid', 'id');

    //     if (Auth::user()->role_guid == 1) {
    //         // Admin: Return all child folders, including shared ones.
    //         return $query
    //             ->with(['sharedOrganizations' => function ($q) {
    //                 $q->select('organizations.id', 'org_name'); // Retrieve only necessary columns
    //             }])
    //             ->orderBy('created_at', 'DESC') // Order by newest first
    //             ->get()
    //             ->map(function ($folder) {
    //                 // Aggregate shared organization names and IDs
    //                 $folder->shared_orgs = $folder->sharedOrganizations->pluck('org_name')->unique()->join("\n");
    //                 $folder->shared_orgs_guid = $folder->sharedOrganizations->pluck('id')->unique()->join(",");
    //                 return $folder;
    //             });
    //     } else {
    //         $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

    //         // Non-admin: Limit by org_guid or shared folders involving the user's org.
    //         return $query
    //             ->with(['sharedOrganizations' => function ($q) {
    //                 $q->select('organizations.id', 'org_name');
    //             }])
    //             ->whereHas('sharedOrganizations', function ($q) use ($user_orgs) {
    //                 $q->whereIn('org_guid', $user_orgs);
    //             })
    //             ->orderBy('created_at', 'DESC')
    //             ->get()
    //             ->map(function ($folder) {
    //                 // Aggregate shared organization names and IDs
    //                 $folder->shared_orgs = $folder->sharedOrganizations->pluck('org_name')->unique()->join("\n");
    //                 $folder->shared_orgs_guid = $folder->sharedOrganizations->pluck('id')->unique()->join(",");
    //                 return $folder;
    //             });
    //     }
    // }

    public function children()
    {
        $query = $this->hasMany(Folder::class, 'parent_folder_guid', 'id')
            ->with(['sharedFolders', 'sharedOrganizations']);

        if (Auth::user()->role_guid == 1) {
            // Admin: Get all child folders
            return $query->orderBy('created_at', 'DESC');
        } else {
            $user_orgs = User_organization::where('user_guid', Auth::user()->id)->pluck('org_guid');

            // Non-admin: Filter based on user's organizations
            return $query->whereHas('sharedOrganizations', function ($query) use ($user_orgs) {
                $query->whereIn('organizations.id', $user_orgs);
            })
                ->orderBy('created_at', 'DESC');
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
