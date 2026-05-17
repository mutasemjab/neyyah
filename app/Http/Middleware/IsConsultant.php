<?php

namespace App\Http\Middleware;

use App\Models\Consultant;
use Closure;
use Illuminate\Http\Request;

class IsConsultant
{
    public function handle(Request $request, Closure $next)
    {
        $consultant = Consultant::where('user_id', $request->user()->id)
            ->where('verification_status', 'approved')
            ->where('is_active', true)
            ->first();

        if (!$consultant) {
            return response()->json([
                'status'  => 'error',
                'message' => 'هذه الخاصية متاحة للمستشارين الموثّقين فقط.',
                'data'    => [],
            ], 403);
        }

        $request->merge(['_consultant' => $consultant]);

        return $next($request);
    }
}
