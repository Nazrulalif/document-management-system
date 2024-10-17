<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;
    protected $table = 'organizations';
    protected $fillable = [
        'uuid',
        'org_name',
        'org_number',
        'reg_date',
        'org_address',
        'org_place',
        'nature_of_business',
        'is_operation',
        'is_parent',
        'org_logo',
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
