<?php

namespace App\Http\Controllers\Api;

use App\Models\ProfileVisit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileVisitController extends ApiController
{
    /**
     * Record a profile visit.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'visited_user_id' => ['required', 'string', 'exists:users,id'],
        ], [
            'visited_user_id.exists' => 'المستخدم غير موجود.',
        ]);

        $auth    = $request->user();
        $visited = $request->visited_user_id;

        // Don't record self-visits
        if ($visited === $auth->id) {
            return $this->success(null);
        }

        // Don't record if the visitor has anonymous browsing enabled
        if ($auth->privacySettings?->anonymous_browsing ?? false) {
            return $this->success(null);
        }

        ProfileVisit::create([
            'visitor_id'      => $auth->id,
            'visited_user_id' => $visited,
        ]);

        return $this->success(null);
    }

    /**
     * Get users who visited the authenticated user's profile.
     */
    public function received(Request $request): JsonResponse
    {
        $auth = $request->user();

        $visits = ProfileVisit::with(['visitor.privacySettings'])
            ->where('visited_user_id', $auth->id)
            // Exclude visitors with anonymous_browsing = true
            ->where(function ($q) {
                $q->whereDoesntHave('visitor.privacySettings')
                  ->orWhereHas('visitor.privacySettings', fn ($q) => $q->where('anonymous_browsing', false));
            })
            ->latest('visited_at')
            ->get()
            ->unique('visitor_id') // one entry per visitor (most recent)
            ->values()
            ->map(fn (ProfileVisit $v) => [
                'id'      => $v->id,
                'visitor' => $v->visitor ? [
                    'id'           => $v->visitor->id,
                    'display_name' => $v->visitor->display_name ?: 'مستخدم',
                    'age'          => $v->visitor->birth_date
                        ? Carbon::parse($v->visitor->birth_date)->age
                        : null,
                    'city'         => $v->visitor->city,
                    'is_verified'  => (bool) $v->visitor->is_verified,
                ] : null,
                'visited_at' => $v->visited_at->toIso8601String(),
            ]);

        return $this->success($visits);
    }
}
