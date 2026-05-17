<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApplyMatchmakerRequest;
use App\Http\Resources\MatchmakerPackageResource;
use App\Http\Resources\MatchmakerResource;
use App\Models\Matchmaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchmakerController extends ApiController
{
    /**
     * Browse all verified matchmakers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Matchmaker::with('user')
            ->where('verification_status', 'approved')
            ->where('is_active', true);

        if ($request->filled('city')) {
            $query->whereHas('user', fn ($q) => $q->where('city', $request->city));
        }

        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', $request->min_rating);
        }

        $matchmakers = $query->orderByDesc('success_cases')
            ->orderByDesc('rating_avg')
            ->paginate(15);

        return $this->success([
            'data'         => MatchmakerResource::collection($matchmakers->items()),
            'total'        => $matchmakers->total(),
            'per_page'     => $matchmakers->perPage(),
            'current_page' => $matchmakers->currentPage(),
            'last_page'    => $matchmakers->lastPage(),
        ]);
    }

    /**
     * View a single matchmaker's profile and packages.
     */
    public function show(string $id): JsonResponse
    {
        $matchmaker = Matchmaker::with(['user', 'packages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('verification_status', 'approved')
            ->where('is_active', true)
            ->find($id);

        if (!$matchmaker) {
            return $this->error('الوسيط غير موجود.', 404);
        }

        return $this->success([
            'matchmaker' => new MatchmakerResource($matchmaker),
            'packages'   => MatchmakerPackageResource::collection($matchmaker->packages),
        ]);
    }

    /**
     * Apply to become a matchmaker.
     */
    public function apply(ApplyMatchmakerRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->matchmakerProfile()->exists()) {
            return $this->error('لديك طلب وساطة مسجّل مسبقاً.', 422);
        }

        $matchmaker = Matchmaker::create([
            'user_id'             => $user->id,
            'bio_ar'              => $request->bio_ar,
            'specializations'     => $request->specializations,
            'years_experience'    => $request->years_experience,
            'verification_status' => 'pending',
        ]);

        return $this->success(new MatchmakerResource($matchmaker), 'تم تقديم طلبك بنجاح. سيتم مراجعته قريباً.', 201);
    }

    /**
     * Get my matchmaker profile.
     */
    public function myProfile(Request $request): JsonResponse
    {
        $matchmaker = $request->user()->matchmakerProfile;

        if (!$matchmaker) {
            return $this->error('لا يوجد ملف وسيط مرتبط بحسابك.', 404);
        }

        return $this->success(new MatchmakerResource($matchmaker->load('user')));
    }
}
