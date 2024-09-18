<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class documentVersion extends Model
{
    use HasFactory;
    protected $table = 'document_versions';
    protected $fillable = [
        'id',
        'uuid',
        'change_title',
        'doc_guid',
        'version_number',
        'file_path',
        'change_description',
        'created_by',
        'doc_content',

    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_guid', 'id');
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
