<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SendMarriageRequestRequest;
use App\Http\Resources\MarriageRequestResource;
use App\Models\Conversation;
use App\Models\MarriageRequest;
use App\Models\User;
use App\Services\CoinService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends ApiController
{
    public function __construct(
        private CoinService $coinService,
        private NotificationService $notificationService
    ) {
    }

    /**
     * Get incoming marriage requests.
     */
    public function incoming(Request $request): JsonResponse
    {
        $user     = $request->user();
        $requests = MarriageRequest::with(['fromUser.profileImages', 'fromUser.privacySettings'])
            ->where('to_user_id', $user->id)
            ->latest('sent_at')
            ->paginate(15);

        return $this->success([
            'data'         => MarriageRequestResource::collection($requests->items()),
            'total'        => $requests->total(),
            'per_page'     => $requests->perPage(),
            'current_page' => $requests->currentPage(),
            'last_page'    => $requests->lastPage(),
        ]);
    }

    /**
     * Get sent marriage requests.
     */
    public function sent(Request $request): JsonResponse
    {
        $user     = $request->user();
        $requests = MarriageRequest::with(['toUser.profileImages', 'toUser.privacySettings'])
            ->where('from_user_id', $user->id)
            ->latest('sent_at')
            ->paginate(15);

        return $this->success([
            'data'         => MarriageRequestResource::collection($requests->items()),
            'total'        => $requests->total(),
            'per_page'     => $requests->perPage(),
            'current_page' => $requests->currentPage(),
            'last_page'    => $requests->lastPage(),
        ]);
    }

    /**
     * Show a specific marriage request.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user        = $request->user();
        $mrRequest   = MarriageRequest::with(['fromUser.profileImages', 'toUser.profileImages'])
            ->find($id);

        if (!$mrRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        // Must be a participant
        if ($mrRequest->from_user_id !== $user->id && $mrRequest->to_user_id !== $user->id) {
            return $this->error('غير مصرح لك بالوصول إلى هذا الطلب.', 403);
        }

        return $this->success(new MarriageRequestResource($mrRequest));
    }

    /**
     * Send a marriage request.
     */
    public function store(SendMarriageRequestRequest $request): JsonResponse
    {
        $auth     = $request->user();
        $toUserId = $request->to_user_id;

        $toUser = User::find($toUserId);
        if (!$toUser) {
            return $this->error('المستخدم المحدد غير موجود.', 404);
        }

        // Must be opposite gender
        if ($auth->gender && $toUser->gender && $auth->gender === $toUser->gender) {
            return $this->error('لا يمكنك إرسال طلب لشخص من نفس الجنس.', 422);
        }

        // Check for duplicate
        $existing = MarriageRequest::where('from_user_id', $auth->id)
            ->where('to_user_id', $toUserId)
            ->first();

        if ($existing) {
            return $this->error('لقد أرسلت طلباً لهذا الشخص من قبل.', 422);
        }

        // Spend 2 coins
        $spent = $this->coinService->spend($auth, 2, 'إرسال طلب زواج');
        if (!$spent) {
            return $this->error('رصيدك من العملات غير كافٍ لإرسال طلب الزواج.', 402);
        }

        $mrRequest = MarriageRequest::create([
            'from_user_id'          => $auth->id,
            'to_user_id'            => $toUserId,
            'reason_for_interest'   => $request->reason_for_interest,
            'life_goals'            => $request->life_goals,
            'marriage_expectations' => $request->marriage_expectations,
            'status'                => 'pending',
        ]);

        // Send notification to recipient
        $this->notificationService->send(
            $toUser,
            'new_marriage_request',
            'طلب زواج جديد',
            'لديك طلب زواج جديد من ' . ($auth->display_name ?? 'مستخدم'),
            ['request_id' => $mrRequest->id, 'from_user_id' => $auth->id]
        );

        return $this->success(new MarriageRequestResource($mrRequest), 'تم إرسال طلب الزواج بنجاح.', 201);
    }

    /**
     * Accept a marriage request.
     */
    public function accept(Request $request, string $id): JsonResponse
    {
        $user      = $request->user();
        $mrRequest = MarriageRequest::find($id);

        if (!$mrRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        if ($mrRequest->to_user_id !== $user->id) {
            return $this->error('غير مصرح لك بقبول هذا الطلب.', 403);
        }

        if ($mrRequest->status !== 'pending') {
            return $this->error('لا يمكن قبول هذا الطلب.', 422);
        }

        $mrRequest->update([
            'status'       => 'accepted',
            'responded_at' => now(),
        ]);

        // Create conversation
        $conversation = Conversation::create([
            'user1_id'   => $mrRequest->from_user_id,
            'user2_id'   => $mrRequest->to_user_id,
            'request_id' => $mrRequest->id,
            'stage'      => 'taaaruf',
        ]);

        // Notify sender
        $fromUser = User::find($mrRequest->from_user_id);
        if ($fromUser) {
            $this->notificationService->send(
                $fromUser,
                'request_accepted',
                'تم قبول طلبك',
                'قبلت ' . ($user->display_name ?? 'المستخدمة') . ' طلب زواجك',
                ['request_id' => $mrRequest->id, 'conversation_id' => $conversation->id]
            );
        }

        return $this->success([
            'request'      => new MarriageRequestResource($mrRequest),
            'conversation_id' => $conversation->id,
        ], 'تم قبول طلب الزواج بنجاح.');
    }

    /**
     * Reject a marriage request.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $user      = $request->user();
        $mrRequest = MarriageRequest::find($id);

        if (!$mrRequest) {
            return $this->error('الطلب غير موجود.', 404);
        }

        if ($mrRequest->to_user_id !== $user->id) {
            return $this->error('غير مصرح لك برفض هذا الطلب.', 403);
        }

        if ($mrRequest->status !== 'pending') {
            return $this->error('لا يمكن رفض هذا الطلب.', 422);
        }

        $mrRequest->update([
            'status'       => 'declined',
            'responded_at' => now(),
        ]);

        return $this->success(new MarriageRequestResource($mrRequest), 'تم رفض طلب الزواج.');
    }
}
