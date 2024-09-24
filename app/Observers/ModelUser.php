<?php

namespace App\Observers;

use App\Models\AuditLog;
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
            'changes' => json_encode($user->getAttributes()),
            'user_guid' => Auth::user()->id,
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
        AuditLog::create([
            'action' =>  $action,
            'model' => 'User',
            'changes' => json_encode($user->getAttributes()),
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the user "deleted" event.
     */
    public function deleted(user $user): void
    {
        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'User',
            'changes' => json_encode($user->getAttributes()),
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
            'changes' => json_encode($user->getAttributes()),
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
            'changes' => json_encode($user->getAttributes()),
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
