<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documentVersion extends Model
{
    use HasFactory;
    protected $table = 'documert_version';
    protected $fillable = [
        'doc_guid',
        'version_number',
        'file_path',
        'change_description',
        'created_by',
    ];
}
