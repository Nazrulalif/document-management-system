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
        'folder_parent_guid',
        'is_meeting',
    ];

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
