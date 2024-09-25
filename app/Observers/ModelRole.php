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
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the role "updated" event.
     */
    public function updated(role $role): void
    {
        AuditLog::create([
            'action' => 'Updated',
            'model' => 'Role',
            'changes' => $role->role_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
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
