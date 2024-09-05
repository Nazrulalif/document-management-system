<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $table = 'organizations';
    protected $fillable = [
        'org_name',
        'org_number',
        'reg_date',
        'org_address',
        'org_place',
        'nature_of_business',
        'is_operation',
        'is_parent',
    ];
}
