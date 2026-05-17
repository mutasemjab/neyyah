<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationSession;
use App\Models\SessionReview;
use Illuminate\Http\Request;

class ConsultationAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsultationSession::with(['user', 'consultant.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('meeting_type')) {
            $query->where('meeting_type', $request->meeting_type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) =>
                $q->where('display_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")
            );
        }

        $data            = $query->latest()->paginate(PAGINATION_COUNT);
        $statusFilter    = $request->status;
        $meetingFilter   = $request->meeting_type;
        $searchQuery     = $request->search;

        return view('admin.consultations.index', compact('data', 'statusFilter', 'meetingFilter', 'searchQuery'));
    }

    public function show(string $id)
    {
        $session = ConsultationSession::with(['user', 'consultant.user', 'review'])->findOrFail($id);

        return view('admin.consultations.show', compact('session'));
    }

    public function reviews(Request $request)
    {
        $reviews = SessionReview::with(['user', 'consultant.user', 'session'])
            ->latest()
            ->paginate(PAGINATION_COUNT);

        return view('admin.consultations.reviews', compact('reviews'));
    }
}
