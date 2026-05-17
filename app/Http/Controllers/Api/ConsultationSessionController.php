<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\BookSessionRequest;
use App\Http\Requests\Api\SubmitSessionReviewRequest;
use App\Http\Resources\ConsultationSessionResource;
use App\Http\Resources\SessionReviewResource;
use App\Models\Consultant;
use App\Models\ConsultationSession;
use App\Models\SessionReview;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationSessionController extends ApiController
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // ── User: book a session ───────────────────────────────────────────────

    public function book(BookSessionRequest $request, string $consultantId): JsonResponse
    {
        $user       = $request->user();
        $consultant = Consultant::where('verification_status', 'approved')
            ->where('is_active', true)
            ->find($consultantId);

        if (!$consultant) {
            return $this->error('المستشار غير موجود.', 404);
        }

        // Prevent booking own profile
        if ($consultant->user_id === $user->id) {
            return $this->error('لا يمكنك حجز جلسة مع نفسك.', 422);
        }

        // Check for time conflict on same date for this consultant
        $conflict = ConsultationSession::where('consultant_id', $consultantId)
            ->where('session_date', $request->session_date)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(fn ($inner) =>
                      $inner->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time)
                  );
            })->exists();

        if ($conflict) {
            return $this->error('هذا الوقت محجوز مسبقاً. يرجى اختيار وقت آخر.', 422);
        }

        $status = $request->payment_reference ? 'confirmed' : 'pending_payment';

        $session = ConsultationSession::create([
            'user_id'           => $user->id,
            'consultant_id'     => $consultantId,
            'session_date'      => $request->session_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'meeting_type'      => $request->meeting_type,
            'status'            => $status,
            'payment_reference' => $request->payment_reference,
            'price'             => $consultant->session_price,
        ]);

        // Notify consultant
        $consultantUser = $consultant->user;
        if ($consultantUser) {
            $this->notificationService->send(
                $consultantUser,
                'session_booked',
                'حجز جلسة جديدة',
                'تم حجز جلسة استشارية معك بتاريخ ' . $request->session_date . '.',
                ['session_id' => $session->id]
            );
        }

        return $this->success(new ConsultationSessionResource($session->load('consultant.user')), 'تم الحجز بنجاح.', 201);
    }

    public function mySessions(Request $request): JsonResponse
    {
        $user     = $request->user();
        $sessions = ConsultationSession::with(['consultant.user', 'review'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => ConsultationSessionResource::collection($sessions->items()),
            'total'        => $sessions->total(),
            'per_page'     => $sessions->perPage(),
            'current_page' => $sessions->currentPage(),
            'last_page'    => $sessions->lastPage(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user    = $request->user();
        $session = ConsultationSession::with(['consultant.user', 'review'])
            ->where(fn ($q) =>
                $q->where('user_id', $user->id)
                  ->orWhereHas('consultant', fn ($c) => $c->where('user_id', $user->id))
            )
            ->find($id);

        if (!$session) {
            return $this->error('الجلسة غير موجودة.', 404);
        }

        return $this->success(new ConsultationSessionResource($session));
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $request->validate(['reason_ar' => 'nullable|string|max:500']);

        $user    = $request->user();
        $session = ConsultationSession::where('user_id', $user->id)->find($id);

        if (!$session) {
            return $this->error('الجلسة غير موجودة.', 404);
        }

        if (in_array($session->status, ['completed', 'cancelled', 'ongoing'])) {
            return $this->error('لا يمكن إلغاء هذه الجلسة.', 422);
        }

        $session->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancelled_reason_ar' => $request->reason_ar,
        ]);

        // Notify consultant
        $consultantUser = $session->consultant->user ?? null;
        if ($consultantUser) {
            $this->notificationService->send(
                $consultantUser,
                'session_cancelled',
                'تم إلغاء حجز الجلسة',
                'قام المستخدم بإلغاء الجلسة الاستشارية.',
                ['session_id' => $id]
            );
        }

        return $this->success([], 'تم إلغاء الجلسة بنجاح.');
    }

    public function submitReview(SubmitSessionReviewRequest $request, string $id): JsonResponse
    {
        $user    = $request->user();
        $session = ConsultationSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->find($id);

        if (!$session) {
            return $this->error('لا يمكن تقييم هذه الجلسة. تأكد من أنها مكتملة.', 422);
        }

        if ($session->review()->exists()) {
            return $this->error('لقد قيّمت هذه الجلسة من قبل.', 422);
        }

        $review = SessionReview::create([
            'session_id'    => $id,
            'user_id'       => $user->id,
            'consultant_id' => $session->consultant_id,
            'rating'        => $request->rating,
            'review_ar'     => $request->review_ar,
        ]);

        // Recalculate consultant rating
        $consultant    = $session->consultant;
        $newCount      = $consultant->ratings_count + 1;
        $newAvg        = (($consultant->rating_avg * $consultant->ratings_count) + $request->rating) / $newCount;
        $consultant->update(['rating_avg' => $newAvg, 'ratings_count' => $newCount]);

        // Notify consultant
        $consultantUser = $consultant->user ?? null;
        if ($consultantUser) {
            $this->notificationService->send(
                $consultantUser,
                'session_reviewed',
                'تقييم جديد',
                'حصلت على تقييم جديد ' . $request->rating . '/5.',
                ['session_id' => $id, 'review_id' => $review->id]
            );
        }

        return $this->success(new SessionReviewResource($review), 'شكراً على تقييمك!', 201);
    }

    // ── Consultant: manage own sessions ───────────────────────────────────

    public function consultantSessions(Request $request): JsonResponse
    {
        $consultant = $request->get('_consultant');

        $sessions = ConsultationSession::with(['review'])
            ->where('consultant_id', $consultant->id)
            ->latest()
            ->paginate(15);

        return $this->success([
            'data'         => ConsultationSessionResource::collection($sessions->items()),
            'total'        => $sessions->total(),
            'per_page'     => $sessions->perPage(),
            'current_page' => $sessions->currentPage(),
            'last_page'    => $sessions->lastPage(),
        ]);
    }

    public function updateSessionStatus(Request $request, string $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:confirmed,ongoing,completed,cancelled']);

        $consultant = $request->get('_consultant');

        $session = ConsultationSession::where('consultant_id', $consultant->id)->find($id);
        if (!$session) {
            return $this->error('الجلسة غير موجودة.', 404);
        }

        $session->update(['status' => $request->status]);

        // Notify user
        $user = $session->user;
        if ($user) {
            $labels = [
                'confirmed'  => 'تم تأكيد حجز جلستك الاستشارية.',
                'ongoing'    => 'بدأت جلستك الاستشارية.',
                'completed'  => 'تمت جلستك الاستشارية بنجاح. شاركنا رأيك.',
                'cancelled'  => 'تم إلغاء جلستك الاستشارية.',
            ];
            $this->notificationService->send(
                $user,
                'session_status_updated',
                'تحديث الجلسة الاستشارية',
                $labels[$request->status] ?? 'تم تحديث حالة جلستك.',
                ['session_id' => $id]
            );
        }

        return $this->success(new ConsultationSessionResource($session), 'تم تحديث الحالة بنجاح.');
    }
}
