<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AddPrivateCandidateRequest;
use App\Http\Requests\Api\CreateMatchmakerPackageRequest;
use App\Http\Requests\Api\CreatePrivateMatchRequestRequest;
use App\Http\Resources\MatchmakerPackageResource;
use App\Http\Resources\PrivateCandidateResource;
use App\Http\Resources\PrivateMatchRequestResource;
use App\Models\Matchmaker;
use App\Models\MatchmakerPackage;
use App\Models\PrivateCandidate;
use App\Models\PrivateMatchRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrivateMatchRequestController extends ApiController
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // ── User: hire a matchmaker ────────────────────────────────────────────

    public function store(CreatePrivateMatchRequestRequest $request): JsonResponse
    {
        $user       = $request->user();
        $matchmaker = Matchmaker::where('id', $request->matchmaker_id)
            ->where('verification_status', 'approved')
            ->where('is_active', true)
            ->first();

        if (!$matchmaker) {
            return $this->error('الوسيط غير موجود أو غير نشط.', 404);
        }

        $package = null;
        $price   = 0;

        if ($request->package_id) {
            $package = MatchmakerPackage::where('id', $request->package_id)
                ->where('matchmaker_id', $matchmaker->id)
                ->where('is_active', true)
                ->first();

            if (!$package) {
                return $this->error('الباقة المحددة غير متاحة.', 422);
            }

            $price = $package->price;
        }

        $status = $request->payment_reference ? 'active' : 'pending_payment';

        $privateRequest = PrivateMatchRequest::create([
            'user_id'             => $user->id,
            'matchmaker_id'       => $matchmaker->id,
            'package_id'          => $request->package_id,
            'status'              => $status,
            'payment_reference'   => $request->payment_reference,
            'price'               => $price,
            'personal_details'    => $request->personal_details,
            'partner_preferences' => $request->partner_preferences,
            'expires_at'          => $package ? now()->addDays($package->duration_days) : null,
        ]);

        // Notify matchmaker
        $matchmakerUser = $matchmaker->user;
        if ($matchmakerUser) {
            $this->notificationService->send(
                $matchmakerUser,
                'private_match_request',
                'طلب وساطة خاص جديد',
                'لديك طلب وساطة خاص جديد من مستخدم.',
                ['request_id' => $privateRequest->id]
            );
        }

        return $this->success(new PrivateMatchRequestResource($privateRequest->load(['matchmaker', 'package'])), 'تم إرسال طلبك بنجاح.', 201);
    }

    public function myRequests(Request $request): JsonResponse
    {
        $requests = PrivateMatchRequest::with(['matchmaker.user', 'package'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => PrivateMatchRequestResource::collection($requests->items()),
            'total'        => $requests->total(),
            'per_page'     => $requests->perPage(),
            'current_page' => $requests->currentPage(),
            'last_page'    => $requests->lastPage(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user    = $request->user();
        $pmRequest = PrivateMatchRequest::with(['matchmaker.user', 'package', 'candidates'])
            ->where(fn ($q) => $q->where('user_id', $user->id)->orWhereHas('matchmaker', fn ($mm) => $mm->where('user_id', $user->id)))
            ->find($id);

        if (!$pmRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        return $this->success(new PrivateMatchRequestResource($pmRequest));
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $request->validate(['reason_ar' => 'nullable|string|max:500']);

        $pmRequest = PrivateMatchRequest::where('user_id', $request->user()->id)->find($id);

        if (!$pmRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        if (in_array($pmRequest->status, ['completed', 'cancelled'])) {
            return $this->error('لا يمكن إلغاء هذا الطلب.', 422);
        }

        $pmRequest->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancelled_reason_ar' => $request->reason_ar,
        ]);

        // Notify matchmaker
        $matchmakerUser = $pmRequest->matchmaker->user ?? null;
        if ($matchmakerUser) {
            $this->notificationService->send(
                $matchmakerUser,
                'private_match_cancelled',
                'تم إلغاء طلب وساطة',
                'قام المستخدم بإلغاء طلب الوساطة الخاص.',
                ['request_id' => $id]
            );
        }

        return $this->success([], 'تم إلغاء الطلب بنجاح.');
    }

    public function myCandidates(Request $request, string $id): JsonResponse
    {
        $pmRequest = PrivateMatchRequest::where('user_id', $request->user()->id)->find($id);

        if (!$pmRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        $candidates = PrivateCandidate::where('request_id', $id)->latest()->get();

        return $this->success(PrivateCandidateResource::collection($candidates));
    }

    public function respondToCandidate(Request $request, string $requestId, string $candidateId): JsonResponse
    {
        $request->validate(['status' => 'required|in:accepted,rejected']);

        $pmRequest = PrivateMatchRequest::where('user_id', $request->user()->id)->find($requestId);
        if (!$pmRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        $candidate = PrivateCandidate::where('request_id', $requestId)->find($candidateId);
        if (!$candidate) {
            return $this->error('المرشح غير موجود.', 404);
        }

        $candidate->update([
            'status'       => $request->status,
            'responded_at' => now(),
        ]);

        // Notify matchmaker
        $matchmakerUser = $pmRequest->matchmaker->user ?? null;
        if ($matchmakerUser) {
            $label = $request->status === 'accepted' ? 'قبل المستخدم المرشح/ة' : 'رفض المستخدم المرشح/ة';
            $this->notificationService->send(
                $matchmakerUser,
                'candidate_response',
                'رد على مرشح',
                $label,
                ['request_id' => $requestId, 'candidate_id' => $candidateId]
            );
        }

        return $this->success(new PrivateCandidateResource($candidate), 'تم تسجيل ردك بنجاح.');
    }

    // ── Matchmaker: manage incoming requests ───────────────────────────────

    public function incomingRequests(Request $request): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $requests = PrivateMatchRequest::with(['package'])
            ->where('matchmaker_id', $matchmaker->id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => PrivateMatchRequestResource::collection($requests->items()),
            'total'        => $requests->total(),
            'per_page'     => $requests->perPage(),
            'current_page' => $requests->currentPage(),
            'last_page'    => $requests->lastPage(),
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status'   => 'required|in:active,searching,candidates_sent,completed,cancelled',
            'notes_ar' => 'nullable|string|max:1000',
        ]);

        $matchmaker = $request->get('_matchmaker');

        $pmRequest = PrivateMatchRequest::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$pmRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        $pmRequest->update([
            'status'   => $request->status,
            'notes_ar' => $request->notes_ar ?? $pmRequest->notes_ar,
        ]);

        // Notify user
        $user = $pmRequest->user;
        if ($user) {
            $statusLabels = [
                'active'          => 'تم تفعيل طلب الوساطة الخاص بك',
                'searching'       => 'بدأ الوسيط البحث عن مرشحين مناسبين',
                'candidates_sent' => 'تم إرسال مرشحين للمراجعة',
                'completed'       => 'تم إنهاء طلب الوساطة بنجاح',
                'cancelled'       => 'تم إلغاء طلب الوساطة',
            ];
            $this->notificationService->send(
                $user,
                'private_match_status',
                'تحديث طلب الوساطة',
                $statusLabels[$request->status] ?? 'تم تحديث حالة طلبك.',
                ['request_id' => $id]
            );
        }

        return $this->success(new PrivateMatchRequestResource($pmRequest), 'تم تحديث الحالة بنجاح.');
    }

    public function addCandidate(AddPrivateCandidateRequest $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $pmRequest = PrivateMatchRequest::where('matchmaker_id', $matchmaker->id)
            ->whereIn('status', ['active', 'searching'])
            ->find($id);

        if (!$pmRequest) {
            return $this->error('الطلب غير موجود أو لا يمكن إضافة مرشحين إليه.', 404);
        }

        // Enforce candidate limit
        $package = $pmRequest->package;
        if ($package) {
            $currentCount = PrivateCandidate::where('request_id', $id)->count();
            if ($currentCount >= $package->candidate_limit) {
                return $this->error('لقد وصلت إلى الحد الأقصى من المرشحين لهذه الباقة.', 422);
            }
        }

        $candidate = PrivateCandidate::create([
            'request_id'        => $id,
            'candidate_user_id' => $request->candidate_user_id,
            'candidate_details' => $request->candidate_details,
            'note_ar'           => $request->note_ar,
        ]);

        $pmRequest->update(['status' => 'candidates_sent']);

        // Notify user
        $user = $pmRequest->user;
        if ($user) {
            $this->notificationService->send(
                $user,
                'private_candidate_added',
                'مرشح جديد من وسيطك',
                'أضاف وسيطك مرشحاً جديداً لك. تفضّل بمراجعته.',
                ['request_id' => $id, 'candidate_id' => $candidate->id]
            );
        }

        return $this->success(new PrivateCandidateResource($candidate), 'تم إضافة المرشح بنجاح.', 201);
    }

    // ── Matchmaker: manage packages ────────────────────────────────────────

    public function storePackage(CreateMatchmakerPackageRequest $request): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $package = MatchmakerPackage::create(array_merge(
            $request->validated(),
            ['matchmaker_id' => $matchmaker->id]
        ));

        return $this->success(new MatchmakerPackageResource($package), 'تم إنشاء الباقة بنجاح.', 201);
    }

    public function updatePackage(CreateMatchmakerPackageRequest $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $package = MatchmakerPackage::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$package) {
            return $this->error('الباقة غير موجودة.', 404);
        }

        $package->update($request->validated());

        return $this->success(new MatchmakerPackageResource($package), 'تم تحديث الباقة بنجاح.');
    }

    public function deletePackage(Request $request, string $id): JsonResponse
    {
        $matchmaker = $request->get('_matchmaker');

        $package = MatchmakerPackage::where('matchmaker_id', $matchmaker->id)->find($id);
        if (!$package) {
            return $this->error('الباقة غير موجودة.', 404);
        }

        $package->update(['is_active' => false]);

        return $this->success([], 'تم تعطيل الباقة بنجاح.');
    }
}
