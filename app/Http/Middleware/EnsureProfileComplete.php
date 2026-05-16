<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || $user->completion_pct < 0.50) {
            return response()->json([
                'status'  => 'error',
                'message' => 'أكمل ملفك الشخصي أولاً',
                'data'    => [
                    'completion_pct' => $user ? $user->completion_pct : 0,
                ],
            ], 403); 
        }

        return $next($request);
    }
}
