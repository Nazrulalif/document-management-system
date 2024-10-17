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
            'user_guid'  => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        $changes = $organization->getDirty();  // Get changed fields
        $original = $organization->getOriginal();  // Get original values

        // Define friendly names for specific fields
        $fieldNames = [
            'org_name' => 'Company name',
            'org_number' => 'Company number',
            'reg_date' => 'Register date',
            'org_address' => 'Address',
            'org_place' => 'State',
            'nature_of_business' => 'Nature of business',
            'org_logo' => 'Logo',
        ];

        foreach ($changes as $key => $newValue) {
            // Skip timestamps and other fields to ignore
            if (in_array($key, ['created_at', 'updated_at', 'is_operation'])) {
                continue;
            }

            // Use friendly name if available, otherwise capitalize the key
            $fieldName = $fieldNames[$key] ?? ucfirst($key);

            // Handle special case for logo changes
            if ($key === 'org_logo') {
                $changeMessage = $organization->org_name . " : Logo Changed";
            } else {
                $oldValue = $original[$key];
                $changeMessage = $organization->org_name .
                    " : {$fieldName} changed from '{$oldValue}' to '{$newValue}'";
            }

            // Create individual log entry for each change
            AuditLog::create([
                'action' => 'Updated',
                'model' => 'Company',
                'changes' => $changeMessage,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
            ]);
        }
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
