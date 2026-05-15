<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastActive
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();

            if (!$user->last_active_at || now()->diffInMinutes($user->last_active_at) > 10) {
                $user->timestamps = false;
                $user->update(['last_active_at' => now()]);
                $user->timestamps = true;
            }
        }

        return $response;
    }
}
