<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $table = 'folders';
    protected $fillable = [
        'folder_name',
        'created_by',
        'org_guid',
        'folder_parent_guid',
        'is_meeting',
    ];
}
