<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateMatchmakerPostRequest;
use App\Http\Requests\Api\SendMatchmakerInterestRequest;
use App\Http\Resources\MatchmakerInterestResource;
use App\Http\Resources\MatchmakerPostResource;
use App\Models\Matchmaker;
use App\Models\MatchmakerInterest;
use App\Models\MatchmakerPost;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchmakerPostController extends ApiController
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // ── Public: browse posts ───────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $query = MatchmakerPost::with(['matchmaker.user'])
            ->whereHas('matchmaker', fn ($q) => $q->where('verification_status', 'approved')->where('is_active', true))
            ->where('is_active', true);

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('min_age')) {
            $query->where('age_from', '>=', $request->min_age);
        }

        if ($request->filled('max_age')) {
            $query->where('age_to', '<=', $request->max_age);
        }

        if ($request->filled('religiosity_level')) {
            $query->where('religiosity_level', $request->religiosity_level);
        }

        $posts = $query->latest()->paginate(20);

        return $this->success([
            'data'         => MatchmakerPostResource::collection($posts->items()),
            'total'        => $posts->total(),
            'per_page'     => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page'    => $posts->lastPage(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $post = MatchmakerPost::with(['matchmaker.user', 'interests' => fn ($q) => $q->where('user_id', $request->user()->id)])
            ->whereHas('matchmaker', fn ($q) => $q->where('verification_status', 'approved'))
            ->where('is_active', true)
            ->find($id);

        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        return $this->success(new MatchmakerPostResource($post));
    }

    // ── User: send interest ────────────────────────────────────────────────

    public function sendInterest(SendMatchmakerInterestRequest $request, string $id): JsonResponse
    {
        $user = $request->user();

        $post = MatchmakerPost::whereHas('matchmaker', fn ($q) => $q->where('verification_status', 'approved'))
            ->where('is_active', true)
            ->find($id);

        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        $existing = MatchmakerInterest::where('post_id', $id)->where('user_id', $user->id)->first();
        if ($existing) {
            return $this->error('لقد أرسلت اهتمامك بهذا المنشور من قبل.', 422);
        }

        $interest = MatchmakerInterest::create([
            'post_id' => $id,
            'user_id' => $user->id,
            'note_ar' => $request->note_ar,
            'status'  => 'pending',
        ]);

        // Increment cached counter
        $post->increment('interests_count');

        // Notify matchmaker
        $matchmaker = $post->matchmaker->load('user');
        if ($matchmaker->user) {
            $this->notificationService->send(
                $matchmaker->user,
                'matchmaker_interest',
                'اهتمام جديد بمنشورك',
                'أبدى مستخدم اهتمامه بأحد منشوراتك.',
                ['post_id' => $id, 'interest_id' => $interest->id]
            );
        }

        return $this->success(new MatchmakerInterestResource($interest), 'تم إرسال اهتمامك بنجاح.', 201);
    }

    public function myInterests(Request $request): JsonResponse
    {
        $interests = MatchmakerInterest::with('post.matchmaker.user')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => MatchmakerInterestResource::collection($interests->items()),
            'total'        => $interests->total(),
            'per_page'     => $interests->perPage(),
            'current_page' => $interests->currentPage(),
            'last_page'    => $interests->lastPage(),
        ]);
    }

    // ── Matchmaker: manage own posts ───────────────────────────────────────

    public function store(CreateMatchmakerPostRequest $request): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $post = MatchmakerPost::create(array_merge(
            $request->validated(),
            ['matchmaker_id' => $matchmaker->id]
        ));

        return $this->success(new MatchmakerPostResource($post), 'تم نشر الإعلان بنجاح.', 201);
    }

    public function update(CreateMatchmakerPostRequest $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $post = MatchmakerPost::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        $post->update($request->validated());

        return $this->success(new MatchmakerPostResource($post), 'تم تحديث المنشور بنجاح.');
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $post = MatchmakerPost::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        $post->delete();

        return $this->success([], 'تم حذف المنشور بنجاح.');
    }

    public function myPosts(Request $request): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $posts = MatchmakerPost::where('matchmaker_id', $matchmaker->id)->latest()->paginate(15);

        return $this->success([
            'data'         => MatchmakerPostResource::collection($posts->items()),
            'total'        => $posts->total(),
            'per_page'     => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page'    => $posts->lastPage(),
        ]);
    }

    public function postInterests(Request $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $post = MatchmakerPost::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        $interests = MatchmakerInterest::with('user')
            ->where('post_id', $id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => MatchmakerInterestResource::collection($interests->items()),
            'total'        => $interests->total(),
            'per_page'     => $interests->perPage(),
            'current_page' => $interests->currentPage(),
            'last_page'    => $interests->lastPage(),
        ]);
    }

    public function respondToInterest(Request $request, string $postId, string $interestId): JsonResponse
    {
        $request->validate(['status' => 'required|in:approved,rejected,matched']);

        $matchmaker = $request->get('_matchmaker');

        $post = MatchmakerPost::where('matchmaker_id', $matchmaker->id)->find($postId);
        if (!$post) {
            return $this->error('المنشور غير موجود.', 404);
        }

        $interest = MatchmakerInterest::where('post_id', $postId)->find($interestId);
        if (!$interest) {
            return $this->error('طلب الاهتمام غير موجود.', 404);
        }

        $interest->update([
            'status'       => $request->status,
            'responded_at' => now(),
        ]);

        // Notify user
        $user = $interest->user;
        if ($user) {
            $titleMap = ['approved' => 'تمت الموافقة على اهتمامك', 'rejected' => 'طلب اهتمامك', 'matched' => 'تهانينا! تم التوفيق'];
            $bodyMap  = ['approved' => 'قبل الوسيط طلبك وسيتواصل معك.', 'rejected' => 'لم تتم الموافقة على طلب اهتمامك في هذه المرة.', 'matched' => 'تم التوفيق بنجاح بينك وبين المرشح/ة.'];
            $this->notificationService->send(
                $user,
                'matchmaker_interest_response',
                $titleMap[$request->status],
                $bodyMap[$request->status],
                ['post_id' => $postId, 'interest_id' => $interestId]
            );
        }

        if ($request->status === 'matched') {
            $post->matchmaker->increment('success_cases');
        }

        return $this->success(new MatchmakerInterestResource($interest), 'تم تحديث الرد بنجاح.');
    }
}
