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
        'doc_title',
        'doc_description',
        'doc_summary',
        'doc_type',
        'doc_author',
        'doc_keyword',
        'upload_by',
        'folder_guid',
        'org_guid',
        'latest_version_guid',
        'version_limit',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_guid', 'id');
    }

    public function latestVersion()
    {
        return $this->belongsTo(DocumentVersion::class, 'latest_version_guid');
    }

    public function documentVersions()
    {
        return $this->hasMany(DocumentVersion::class, 'doc_guid', 'id');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class, 'doc_guid');
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
