<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Starred_folder extends Model
{
    use HasFactory;
    protected $table = 'starred_folders';
    protected $fillable = [
        'user_guid',
        'folder_guid',
    ];
}
