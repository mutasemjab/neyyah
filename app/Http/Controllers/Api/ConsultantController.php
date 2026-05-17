<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApplyConsultantRequest;
use App\Http\Requests\Api\SetAvailabilityRequest;
use App\Http\Resources\ConsultantAvailabilityResource;
use App\Http\Resources\ConsultantResource;
use App\Models\Consultant;
use App\Models\ConsultantAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultantController extends ApiController
{
    // ── Public: browse consultants ─────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $query = Consultant::with('user')
            ->where('verification_status', 'approved')
            ->where('is_active', true);

        if ($request->filled('specialization')) {
            $query->whereJsonContains('specializations', $request->specialization);
        }

        if ($request->filled('meeting_type')) {
            $query->whereJsonContains('meeting_types', $request->meeting_type);
        }

        if ($request->filled('max_price')) {
            $query->where('session_price', '<=', $request->max_price);
        }

        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', $request->min_rating);
        }

        $consultants = $query->orderByDesc('rating_avg')
            ->orderByDesc('ratings_count')
            ->paginate(15);

        return $this->success([
            'data'         => ConsultantResource::collection($consultants->items()),
            'total'        => $consultants->total(),
            'per_page'     => $consultants->perPage(),
            'current_page' => $consultants->currentPage(),
            'last_page'    => $consultants->lastPage(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $consultant = Consultant::with(['user', 'availabilities'])
            ->where('verification_status', 'approved')
            ->where('is_active', true)
            ->find($id);

        if (!$consultant) {
            return $this->error('المستشار غير موجود.', 404);
        }

        return $this->success(new ConsultantResource($consultant));
    }

    public function availability(string $id): JsonResponse
    {
        $consultant = Consultant::where('verification_status', 'approved')
            ->where('is_active', true)
            ->find($id);

        if (!$consultant) {
            return $this->error('المستشار غير موجود.', 404);
        }

        $slots = ConsultantAvailability::where('consultant_id', $id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return $this->success(ConsultantAvailabilityResource::collection($slots));
    }

    // ── Apply / My profile ─────────────────────────────────────────────────

    public function apply(ApplyConsultantRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->consultantProfile()->exists()) {
            return $this->error('لديك ملف مستشار مسجّل مسبقاً.', 422);
        }

        $consultant = Consultant::create([
            'user_id'             => $user->id,
            'title_ar'            => $request->title_ar,
            'specializations'     => $request->specializations,
            'years_experience'    => $request->years_experience,
            'session_price'       => $request->session_price,
            'meeting_types'       => $request->meeting_types,
            'bio_ar'              => $request->bio_ar,
            'verification_status' => 'pending',
        ]);

        return $this->success(new ConsultantResource($consultant), 'تم تقديم طلبك بنجاح. سيتم مراجعته قريباً.', 201);
    }

    public function myProfile(Request $request): JsonResponse
    {
        $consultant = $request->user()->consultantProfile;

        if (!$consultant) {
            return $this->error('لا يوجد ملف مستشار مرتبط بحسابك.', 404);
        }

        return $this->success(new ConsultantResource($consultant->load(['user', 'availabilities'])));
    }

    // ── Consultant: manage availability ───────────────────────────────────

    public function storeAvailability(SetAvailabilityRequest $request): JsonResponse
    {
        $consultant = $request->get('_consultant');

        // Check for overlapping slots on same day
        $overlap = ConsultantAvailability::where('consultant_id', $consultant->id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(fn ($inner) =>
                      $inner->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time)
                  );
            })->exists();

        if ($overlap) {
            return $this->error('يتداخل هذا الوقت مع توافر آخر محدد مسبقاً.', 422);
        }

        $slot = ConsultantAvailability::create([
            'consultant_id' => $consultant->id,
            'day_of_week'   => $request->day_of_week,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
        ]);

        return $this->success(new ConsultantAvailabilityResource($slot), 'تم إضافة وقت التوافر بنجاح.', 201);
    }

    public function destroyAvailability(Request $request, string $id): JsonResponse
    {
        $consultant = $request->get('_consultant');

        $slot = ConsultantAvailability::where('consultant_id', $consultant->id)->find($id);
        if (!$slot) {
            return $this->error('الوقت غير موجود.', 404);
        }

        $slot->delete();

        return $this->success([], 'تم حذف وقت التوافر بنجاح.');
    }
}
