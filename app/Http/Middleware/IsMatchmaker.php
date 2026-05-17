<?php

namespace App\Http\Middleware;

use App\Models\Matchmaker;
use Closure;
use Illuminate\Http\Request;

class IsMatchmaker
{
    public function handle(Request $request, Closure $next)
    {
        $matchmaker = Matchmaker::where('user_id', $request->user()->id)
            ->where('verification_status', 'approved')
            ->where('is_active', true)
            ->first();

        if (!$matchmaker) {
            return response()->json([
                'status'  => 'error',
                'message' => 'هذه الخاصية متاحة للوسطاء الموثّقين فقط.',
                'data'    => [],
            ], 403);
        }

        $request->merge(['_matchmaker' => $matchmaker]);

        return $next($request);
    }
}
