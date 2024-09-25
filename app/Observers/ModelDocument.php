<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\document;
use Illuminate\Support\Facades\Auth;

class ModelDocument
{
    /**
     * Handle the Document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(document $document): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'File',
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the document "updated" event.
     */
    public function updated(document $document): void
    {
        AuditLog::create([
            'action' => 'Updated',
            'model' => 'File',
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the document "deleted" event.
     */
    public function deleted(document $document): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'File',
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the document "restored" event.
     */
    public function restored(document $document): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'File',
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the document "force deleted" event.
     */
    public function forceDeleted(document $document): void
    {
        AuditLog::create([
            'action' => 'Force Deleted',
            'model' => 'File',
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
