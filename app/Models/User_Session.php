<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Session extends Model
{
    use HasFactory;
    protected $table = 'user_sessions';
    protected $fillable = [
        'user_id',
        'session_id',
        'created_at',
        'updated_at',
    ];
}
