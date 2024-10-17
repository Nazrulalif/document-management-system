<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\role;
use Illuminate\Support\Facades\Auth;

class ModelRole
{
    /**
     * Handle the role "created" event.
     */
    public function created(role $role): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'Role',
            'changes' => $role->role_name,
            'user_guid'  => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the role "updated" event.
     */
    public function updated(role $role): void
    {
        $changes = $role->getDirty();  // Get changed fields
        $original = $role->getOriginal();  // Get original values

        // Define friendly names for specific fields
        $fieldNames = [
            'role_name' => 'Role Name',
            'role_description' => 'Description',
        ];

        foreach ($changes as $key => $newValue) {
            $oldValue = $original[$key];

            // Use friendly name if available, otherwise capitalize the key
            $fieldName = $fieldNames[$key] ?? ucfirst($key);
            // Skip timestamps and other fields to ignore
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }
            $changeMessage = $role->role_name .
                " : {$fieldName} changed from '{$oldValue}' to '{$newValue}'";

            // Create individual log entry for each change
            AuditLog::create([
                'action' => 'Updated',
                'model' => 'Role',
                'changes' => $changeMessage,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
            ]);
        }
    }

    /**
     * Handle the role "deleted" event.
     */
    public function deleted(role $role): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'Role',
            'changes' => $role->role_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the role "restored" event.
     */
    public function restored(role $role): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'Role',
            'changes' => $role->role_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the role "force deleted" event.
     */
    public function forceDeleted(role $role): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'Role',
            'changes' => $role->role_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
