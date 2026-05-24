<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            $last = session('admin_last_activity');

            if ($last && now()->diffInMinutes($last) >= 30) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')
                    ->with('status', 'Session expirée après 30 minutes d\'inactivité.');
            }

            session(['admin_last_activity' => now()]);
        }

        return $next($request);
    }
}
