<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $table = 'audit_logs';
    protected $fillable = [
        'user_guid',
        'model',
        'doc_guid',
        'action',
        'changes',
        'document_guid',
        'ip_address',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'id');
    }
}
