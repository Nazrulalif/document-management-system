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
        AuditLog::create([
            'action' => 'Updated',
            'model' => 'Folder',
            'changes' => $folder->folder_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
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
