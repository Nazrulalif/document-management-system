<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_folder_guid');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_folder_guid', 'id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_guid', 'id');
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
