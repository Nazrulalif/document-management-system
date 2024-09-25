<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\organization;
use Illuminate\Support\Facades\Auth;

class ModelOrganization
{
    /**
     * Handle the organization "created" event.
     */
    public function created(organization $organization): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'Company',
            'changes' => $organization->org_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the organization "updated" event.
     */
    public function updated(organization $organization): void
    {
        $action = 'Updated';

        if ($organization->is_operation == 'N') { // Assuming you have a method to check if deactivated
            $action = 'Deactivated';
        }

        AuditLog::create([
            'action' => $action,
            'model' => 'Company',
            'changes' => $organization->org_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the organization "deleted" event.
     */
    public function deleted(organization $organization): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'Company',
            'changes' => $organization->org_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the organization "restored" event.
     */
    public function restored(organization $organization): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'Company',
            'changes' => $organization->org_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the organization "force deleted" event.
     */
    public function forceDeleted(organization $organization): void
    {
        AuditLog::create([
            'action' => 'Force Deleted',
            'model' => 'Company',
            'changes' => $organization->org_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
