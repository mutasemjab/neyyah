<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UpdateMatchFiltersRequest;
use App\Http\Resources\MatchFilterResource;
use App\Http\Resources\MatchSuggestionResource;
use App\Models\MatchDismissal;
use App\Models\MatchFilter;
use App\Models\MatchView;
use App\Models\User;
use App\Services\CoinService;
use App\Services\MatchingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchingController extends ApiController
{
    public function __construct(
        private MatchingService $matchingService,
        private CoinService $coinService
    ) {
    }

    /**
     * Get match suggestions for authenticated user.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $auth    = $request->user();
        $filters = $auth->matchFilters;

        // Determine opposite gender
        $oppositeGender = $auth->gender === 'male' ? 'female' : 'male';

        // Get dismissed user IDs
        $dismissedIds = MatchDismissal::where('user_id', $auth->id)
            ->pluck('dismissed_user_id')
            ->toArray();

        // Build query
        $query = User::with(['profileImages', 'interests', 'intentCard', 'privacySettings'])
            ->where('id', '!=', $auth->id)
            ->whereNotNull('gender')
            ->where('gender', $oppositeGender)
            ->whereNotNull('birth_date')
            ->whereNotIn('id', $dismissedIds)
            ->whereNull('deleted_at');

        // Apply age filter
        $minAge  = $filters?->min_age ?? 18;
        $maxAge  = $filters?->max_age ?? 45;
        $maxDate = Carbon::now()->subYears($minAge)->toDateString();
        $minDate = Carbon::now()->subYears($maxAge)->toDateString();

        $query->whereBetween('birth_date', [$minDate, $maxDate]);

        // Apply city filter
        $cities = $filters?->cities ?? [];
        if (!empty($cities)) {
            $query->whereIn('city', $cities);
        }

        // Apply religiosity filter
        $religiosityLevels = $filters?->religiosity_levels ?? [];
        if (!empty($religiosityLevels)) {
            $query->whereIn('religiosity_level', $religiosityLevels);
        }

        // Apply education filter
        $educationLevels = $filters?->education_levels ?? [];
        if (!empty($educationLevels)) {
            $query->whereIn('education_level', $educationLevels);
        }

        // Apply no_smokers filter
        if ($filters?->no_smokers) {
            $query->where('is_smoker', false);
        }

        // Apply marriage_timelines filter
        $marriageTimelines = $filters?->marriage_timelines ?? [];
        if (!empty($marriageTimelines)) {
            $query->whereIn('marriage_timeline', $marriageTimelines);
        }

        // Get candidates and score them
        $candidates = $query->orderByDesc('seriousness_score')
            ->limit(50)
            ->get();

        $suggestions = $candidates->map(function (User $candidate) use ($auth) {
            $compatibility = $this->matchingService->calculateCompatibility($auth, $candidate);
            $proximity     = $this->matchingService->getProximityLabel($auth, $candidate);

            return [
                'id'                    => $candidate->id,
                'profile'               => $candidate,
                'compatibility_score'   => $compatibility['score'],
                'compatibility_factors' => $compatibility['factors'],
                'proximity'             => $proximity,
                'status'                => 'suggested',
                'suggested_at'          => now()->toIso8601String(),
            ];
        })->sortByDesc('compatibility_score')->values();

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $total   = $suggestions->count();
        $items   = $suggestions->slice(($page - 1) * $perPage, $perPage)->values();

        return $this->success([
            'data'         => MatchSuggestionResource::collection($items),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ]);
    }

    /**
     * Record a profile view and spend 1 coin.
     */
    public function view(Request $request, string $userId): JsonResponse
    {
        $auth = $request->user();

        $target = User::find($userId);
        if (!$target) {
            return $this->error('المستخدم غير موجود.', 404);
        }

        // Spend 1 coin
        $spent = $this->coinService->spend($auth, 1, 'عرض ملف شخصي');
        if (!$spent) {
            return $this->error('رصيدك من العملات غير كافٍ.', 402);
        }

        MatchView::create([
            'viewer_id' => $auth->id,
            'viewed_id' => $userId,
        ]);

        return $this->success([], 'تم تسجيل المشاهدة بنجاح.');
    }

    /**
     * Dismiss a user from suggestions.
     */
    public function dismiss(Request $request, string $userId): JsonResponse
    {
        $auth = $request->user();

        MatchDismissal::updateOrCreate([
            'user_id'           => $auth->id,
            'dismissed_user_id' => $userId,
        ], [
            'dismissed_at' => now(),
        ]);

        return $this->success([], 'تم تجاهل المستخدم بنجاح.');
    }

    /**
     * Get current user's match filters.
     */
    public function getFilters(Request $request): JsonResponse
    {
        $user    = $request->user();
        $filters = $user->matchFilters;

        if (!$filters) {
            // Return defaults
            return $this->success([
                'min_age'            => 18,
                'max_age'            => 45,
                'cities'             => [],
                'religiosity_levels' => [],
                'education_levels'   => [],
                'no_smokers'         => true,
                'max_distance_km'    => null,
                'marriage_timelines' => [],
                'updated_at'         => null,
            ]);
        }

        return $this->success(new MatchFilterResource($filters));
    }

    /**
     * Update or create match filters.
     */
    public function updateFilters(UpdateMatchFiltersRequest $request): JsonResponse
    {
        $user = $request->user();

        $filters = $user->matchFilters()->updateOrCreate(
            ['user_id' => $user->id],
            array_merge($request->validated(), ['updated_at' => now()])
        );

        return $this->success(new MatchFilterResource($filters), 'تم تحديث فلاتر المطابقة بنجاح.');
    }
}
