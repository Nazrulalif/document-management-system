<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $fillable = [
        'doc_name',
        'doc_description',
        'doc_summary',
        'doc_type',
        'doc_author',
        'upload_by',
        'folder_guid',
        'org_guid',
        'tag_guid',
        'lates_version_guid',
    ];
}
