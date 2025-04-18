<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class DormantUserService
{
    public function disableDormantUsers()
    {
        $threshold = Carbon::now()->subDays(90);

        User::where('is_active', 'Y')
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '<', $threshold)
            ->update(['is_active' => "N"]);
    }
}