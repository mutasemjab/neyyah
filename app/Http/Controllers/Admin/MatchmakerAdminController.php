<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Matchmaker;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MatchmakerAdminController extends Controller
{
    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index(Request $request)
    {
        $query = Matchmaker::with('user');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) =>
                $q->where('display_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
            );
        }

        $data         = $query->latest()->paginate(PAGINATION_COUNT);
        $statusFilter = $request->status;
        $searchQuery  = $request->search;

        return view('admin.matchmakers.index', compact('data', 'statusFilter', 'searchQuery'));
    }

    public function show(string $id)
    {
        $matchmaker = Matchmaker::with(['user', 'posts', 'packages'])->findOrFail($id);

        return view('admin.matchmakers.show', compact('matchmaker'));
    }

    public function approve(Request $request, string $id)
    {
        $matchmaker = Matchmaker::with('user')->findOrFail($id);

        $matchmaker->update([
            'verification_status' => 'approved',
            'verified_at'         => now(),
            'rejection_reason_ar' => null,
        ]);

        if ($matchmaker->user) {
            app(NotificationService::class)->send(
                $matchmaker->user,
                'matchmaker_approved',
                'تهانينا! تمت الموافقة على طلبك',
                'تمت الموافقة على طلب انضمامك كوسيط زواج موثّق. يمكنك الآن نشر إعلاناتك.',
                ['matchmaker_id' => $matchmaker->id]
            );
        }

        return redirect()->back()->with('success', 'تمت الموافقة على الوسيط بنجاح.');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate(['reason_ar' => 'required|string|max:500']);

        $matchmaker = Matchmaker::with('user')->findOrFail($id);

        $matchmaker->update([
            'verification_status' => 'rejected',
            'rejection_reason_ar' => $request->reason_ar,
        ]);

        if ($matchmaker->user) {
            app(NotificationService::class)->send(
                $matchmaker->user,
                'matchmaker_rejected',
                'نأسف، لم يتم قبول طلبك',
                'لم يتم قبول طلب انضمامك كوسيط في الوقت الحالي.',
                ['matchmaker_id' => $matchmaker->id]
            );
        }

        return redirect()->back()->with('success', 'تم رفض الطلب.');
    }

    public function toggleActive(string $id)
    {
        $matchmaker = Matchmaker::findOrFail($id);
        $matchmaker->update(['is_active' => !$matchmaker->is_active]);

        return redirect()->back()->with('success', 'تم تحديث حالة الوسيط.');
    }
}
