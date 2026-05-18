<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\IdentityVerification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index(Request $request)
    {
        if (!$this->admin()->can('verifications-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $statusFilter = $request->input('status', 'pending');

        $data = IdentityVerification::with('user')
            ->where('status', $statusFilter)
            ->latest()
            ->paginate(PAGINATION_COUNT);

        return view('admin.verifications.index', compact('data', 'statusFilter'));
    }

    public function show(int $id)
    {
        if (!$this->admin()->can('verifications-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $verification = IdentityVerification::with('user')->findOrFail($id);

        return view('admin.verifications.show', compact('verification'));
    }

    public function document(int $id, string $type)
    {
        if (!$this->admin()->can('verifications-index')) {
            abort(403);
        }

        $verification = IdentityVerification::findOrFail($id);

        $path = match ($type) {
            'front'  => $verification->document_front_path,
            'back'   => $verification->document_back_path,
            'selfie' => $verification->selfie_path,
            default  => null,
        };

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function approve(int $id)
    {
        if (!$this->admin()->can('verifications-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $verification = IdentityVerification::with('user')->findOrFail($id);
        $verification->update([
            'status'      => 'approved',
            'reviewed_by' => $this->admin()->id,
            'reviewed_at' => now(),
        ]);
        $verification->user->update(['is_verified' => true]);

        $this->notifications->send(
            $verification->user,
            'verification_approved',
            'تم قبول هويتك ✓',
            'تهانينا! تم التحقق من هويتك بنجاح وأصبح حسابك موثقاً.',
            ['status' => 'approved']
        );

        return redirect()->route('admin.verifications.index')
            ->with('success', __('messages.Verification_Approved'));
    }

    public function reject(Request $request, int $id)
    {
        if (!$this->admin()->can('verifications-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $verification = IdentityVerification::with('user')->findOrFail($id);
        $verification->update([
            'status'           => 'rejected',
            'reviewed_by'      => $this->admin()->id,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at'      => now(),
        ]);

        $this->notifications->send(
            $verification->user,
            'verification_rejected',
            'لم يتم قبول طلب التوثيق',
            'السبب: ' . $request->rejection_reason . ' — يمكنك إعادة التقديم.',
            ['status' => 'rejected', 'reason' => $request->rejection_reason]
        );

        return redirect()->route('admin.verifications.index')
            ->with('success', __('messages.Verification_Rejected'));
    }
}
