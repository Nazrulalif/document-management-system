<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\folder;
use Illuminate\Support\Facades\Auth;

class ModelFolder
{
    /**
     * Handle the folder "created" event.
     */
    public function created(folder $folder): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'Folder',
            'changes' => $folder->folder_name,
            'user_guid'  => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the folder "updated" event.
     */
    public function updated(folder $folder): void
    {
        $changes = $folder->getDirty();  // Get changed fields
        $original = $folder->getOriginal();  // Get original values

        // Define friendly names for specific fields
        $fieldNames = [
            'folder_Name' => 'Folder name',
        ];

        foreach ($changes as $key => $newValue) {
            $oldValue = $original[$key];

            // Use friendly name if available, otherwise capitalize the key
            $fieldName = $fieldNames[$key] ?? ucfirst($key);
            // Skip timestamps and other fields to ignore
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }
            $changeMessage = $folder->folder_name .
                " : {$fieldName} changed from '{$oldValue}' to '{$newValue}'";

            // Create individual log entry for each change
            AuditLog::create([
                'action' => 'Updated',
                'model' => 'Folder',
                'changes' => $changeMessage,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
            ]);
        }
    }

    /**
     * Handle the folder "deleted" event.
     */
    public function deleted(folder $folder): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'Folder',
            'changes' => $folder->folder_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the folder "restored" event.
     */
    public function restored(folder $folder): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'Folder',
            'changes' => $folder->folder_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the folder "force deleted" event.
     */
    public function forceDeleted(folder $folder): void
    {
        AuditLog::create([
            'action' => 'Force Deleted',
            'model' => 'Folder',
            'changes' => $folder->folder_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
