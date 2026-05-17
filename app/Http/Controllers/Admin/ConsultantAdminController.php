<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Consultant;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ConsultantAdminController extends Controller
{
    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index(Request $request)
    {
        $query = Consultant::with('user');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) =>
                $q->where('display_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
            )->orWhere('title_ar', 'like', "%{$s}%");
        }

        $data         = $query->latest()->paginate(PAGINATION_COUNT);
        $statusFilter = $request->status;
        $searchQuery  = $request->search;

        return view('admin.consultants.index', compact('data', 'statusFilter', 'searchQuery'));
    }

    public function show(string $id)
    {
        $consultant = Consultant::with(['user', 'availabilities', 'sessions', 'reviews'])->findOrFail($id);

        return view('admin.consultants.show', compact('consultant'));
    }

    public function approve(Request $request, string $id)
    {
        $consultant = Consultant::with('user')->findOrFail($id);

        $consultant->update([
            'verification_status' => 'approved',
            'verified_at'         => now(),
            'rejection_reason_ar' => null,
        ]);

        if ($consultant->user) {
            app(NotificationService::class)->send(
                $consultant->user,
                'consultant_approved',
                'تهانينا! تمت الموافقة على طلبك',
                'تمت الموافقة على طلب انضمامك كمستشار زواج موثّق.',
                ['consultant_id' => $consultant->id]
            );
        }

        return redirect()->back()->with('success', 'تمت الموافقة على المستشار بنجاح.');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate(['reason_ar' => 'required|string|max:500']);

        $consultant = Consultant::with('user')->findOrFail($id);

        $consultant->update([
            'verification_status' => 'rejected',
            'rejection_reason_ar' => $request->reason_ar,
        ]);

        if ($consultant->user) {
            app(NotificationService::class)->send(
                $consultant->user,
                'consultant_rejected',
                'نأسف، لم يتم قبول طلبك',
                'لم يتم قبول طلب انضمامك كمستشار في الوقت الحالي.',
                ['consultant_id' => $consultant->id]
            );
        }

        return redirect()->back()->with('success', 'تم رفض الطلب.');
    }

    public function toggleActive(string $id)
    {
        $consultant = Consultant::findOrFail($id);
        $consultant->update(['is_active' => !$consultant->is_active]);

        return redirect()->back()->with('success', 'تم تحديث حالة المستشار.');
    }
}
