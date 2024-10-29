<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shared_document extends Model
{
    use HasFactory;
    protected $table = 'shared_documents';
    protected $fillable = [
        'doc_guid',
        'org_guid',
    ];
}
