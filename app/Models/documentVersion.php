<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class documentVersion extends Model
{
    use HasFactory;
    protected $table = 'documert_version';
    protected $fillable = [
        'id',
        'uuid',
        'doc_guid',
        'version_number',
        'file_path',
        'change_description',
        'created_by',
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
