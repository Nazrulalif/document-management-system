<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Role;
use App\Models\user;
use Illuminate\Support\Facades\Auth;

class ModelUser
{
    /**
     * Handle the user "created" event.
     */
    public function created(user $user): void
    {
        AuditLog::create([
            'action' => 'Created',
            'model' => 'User',
            'changes' => $user->full_name,
            'user_guid'  => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the user "updated" event.
     */
    public function updated(user $user): void
    {
        $action = 'Updated';

        if ($user->is_active == 'N') { // Assuming you have a method to check if deactivated
            $action = 'Deactivated';
        }
        $changes = $user->getDirty();  // Get changed fields
        $original = $user->getOriginal();  // Get original values

        // Define friendly names for specific fields
        $fieldNames = [
            'full_name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'ic_number' => 'IC Number',
            'race' => 'Race',
            'nationality' => 'Nationality',
            'gender' => 'Gender',
            'role_guid' => 'Role',
            'org_guid' => 'Company',
            'profile_picture' => 'Profile Picture',
        ];

        foreach ($changes as $key => $newValue) {
            // Skip timestamps and other fields to ignore
            if (in_array($key, ['created_at', 'updated_at', 'password', 'is_change_password ', 'login_method'])) {
                continue;
            }

            // Use friendly name if available, otherwise capitalize the key
            $fieldName = $fieldNames[$key] ?? ucfirst($key);

            // Handle special case for logo changes
            if ($key === 'profile_picture') {
                $changeMessage = $user->full_name . " : Profile picture Changed";
            } elseif ($key === 'org_guid') {
                $oldValue = $original[$key];
                $old_company =  Organization::where('id', $oldValue)->first();
                $new_company =  Organization::where('id', $newValue)->first();

                $changeMessage = $user->full_name .
                    " : {$fieldName} changed from '{$old_company->org_name}' to '{$new_company->org_name}'";
            } elseif ($key === 'role_guid') {
                $oldValue = $original[$key];
                $old_role =  Role::where('id', $oldValue)->first();
                $new_role =  Role::where('id', $newValue)->first();

                $changeMessage = $user->full_name .
                    " : {$fieldName} changed from '{$old_role->role_name}' to '{$new_role->role_name}'";
            } elseif ($key === 'is_active') {
                $changeMessage = $user->full_name;
            } else {
                $oldValue = $original[$key];
                $changeMessage = $user->full_name .
                    " : {$fieldName} changed from '{$oldValue}' to '{$newValue}'";
            }

            // Create individual log entry for each change
            AuditLog::create([
                'action' => $action,
                'model' => 'User',
                'changes' => $changeMessage,
                'user_guid' => Auth::check() ? Auth::user()->id : null,
                'ip_address' => request()->ip(),
            ]);
        }
    }

    /**
     * Handle the user "deleted" event.
     */
    public function deleted(user $user): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'User',
            'changes' => $user->full_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the user "restored" event.
     */
    public function restored(user $user): void
    {
        AuditLog::create([
            'action' => 'Restored',
            'model' => 'User',
            'changes' => $user->full_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the user "force deleted" event.
     */
    public function forceDeleted(user $user): void
    {
        AuditLog::create([
            'action' => 'Force Delete',
            'model' => 'User',
            'changes' => $user->full_name,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
