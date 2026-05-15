<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\IdentityVerification;
use Illuminate\Http\Request;

class IdentityVerificationController extends Controller
{
    /** @return Admin */
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

        $data = IdentityVerification::with('user.profile')
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

        $verification = IdentityVerification::with('user.profile')->findOrFail($id);

        return view('admin.verifications.show', compact('verification'));
    }

    public function approve(int $id)
    {
        if (!$this->admin()->can('verifications-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $verification = IdentityVerification::findOrFail($id);
        $verification->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);
        $verification->user->update(['is_verified' => true]);

        return redirect()->route('admin.verifications.index')
            ->with('success', __('messages.Verification_Approved'));
    }

    public function reject(Request $request, int $id)
    {
        if (!$this->admin()->can('verifications-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        IdentityVerification::findOrFail($id)->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at'      => now(),
        ]);

        return redirect()->route('admin.verifications.index')
            ->with('success', __('messages.Verification_Rejected'));
    }
}
