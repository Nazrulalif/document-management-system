<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;
    protected $fillable = ['file_count', 'folder_count', 'user_count', 'org_count'];
}
