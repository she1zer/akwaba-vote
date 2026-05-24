<?php

namespace App\Services;

use App\Models\LogAdmin;
use Illuminate\Support\Facades\Auth;

class AdminLogger
{
    public static function log(string $action, ?string $detail = null): void
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return;
        }

        LogAdmin::query()->create([
            'admin_id' => $admin->id,
            'action' => $action,
            'detail' => $detail,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
