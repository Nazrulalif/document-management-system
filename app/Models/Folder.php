<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        'org_guid',
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
        // Check if the authenticated user is an admin (role_guid == 1)
        if (Auth::user()->role_guid == 1) {
            // If the user is an admin, return all child folders
            return $this->hasMany(Folder::class, 'parent_folder_guid', 'id')
                ->with('organization');
        } else {
            // For non-admin users, fetch child folders based on org_guid or admin status
            return $this->hasMany(Folder::class, 'parent_folder_guid', 'id')
                ->with('organization')
                ->where(function ($query) {
                    $query->where('folders.org_guid', Auth::user()->org_guid)
                        ->orWhereHas('creator', function ($query) {
                            $query->where('users.role_guid', '1'); // Admin users
                        });
                });
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
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_guid');
    }


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
