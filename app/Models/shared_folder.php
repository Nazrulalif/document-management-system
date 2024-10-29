<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shared_folder extends Model
{
    use HasFactory;
    protected $table = 'shared_folders';
    protected $fillable = [
        'folder_guid',
        'org_guid',
    ];
}
