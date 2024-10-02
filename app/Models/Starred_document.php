<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Starred_document extends Model
{
    use HasFactory;
    protected $table = 'starred_documents';
    protected $fillable = [
        'user_guid',
        'doc_guid',
    ];
}
