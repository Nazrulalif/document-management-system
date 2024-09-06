<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $fillable = [
        'id',
        'uuid',
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
