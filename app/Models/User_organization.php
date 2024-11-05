<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_organization extends Model
{
    use HasFactory;
    protected $table = 'user_organizations';
    protected $fillable = [
        'org_guid',
        'user_guid',
    ];
}
