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
    public function created(Document $document): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'File',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        $changes = $document->getDirty();  // Get changed fields
        $original = $document->getOriginal();  // Get original values

        // Define friendly names for specific fields
        $fieldNames = [
            'doc_title' => 'Title',
            'doc_description' => 'Description',
            'doc_summary' => 'Summary',
            'doc_author' => 'Author',
            'doc_keyword' => 'Keyword',
            'version_limit' => 'Version Limit',
            // Add more fields if needed
        ];

        foreach ($changes as $key => $newValue) {
            $oldValue = $original[$key];

            // Use friendly name if available, otherwise capitalize the key
            $fieldName = $fieldNames[$key] ?? ucfirst($key);
            // Skip timestamps and other fields to ignore
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }

            if (in_array($key, ['latest_version_guid'])) {
                continue;
            }

            $changeMessage = $document->doc_title .
                " : {$fieldName} changed from '{$oldValue}' to '{$newValue}'";

            // Create individual log entry for each change
            AuditLog::create([
                'action' => 'Updated',
                'model' => 'File',
                'doc_guid' => $document->id,
                'changes' => $changeMessage,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
            ]);
        }
    }


    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'File',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'File',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        AuditLog::create([
            'action' => 'Force Deleted',
            'model' => 'File',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }
}
