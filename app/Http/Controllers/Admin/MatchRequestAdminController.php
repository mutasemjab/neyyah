<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\MatchRequest;
use Illuminate\Http\Request;

class MatchRequestAdminController extends Controller
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
        if (!$this->admin()->can('match-requests-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $query = MatchRequest::with('fromUser.profile', 'toUser.profile');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data         = $query->latest()->paginate(PAGINATION_COUNT);
        $statusFilter = $request->status;

        return view('admin.match-requests.index', compact('data', 'statusFilter'));
    }

    public function show(int $id)
    {
        if (!$this->admin()->can('match-requests-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $matchRequest = MatchRequest::with('fromUser.profile', 'toUser.profile', 'conversation')
            ->findOrFail($id);

        return view('admin.match-requests.show', compact('matchRequest'));
    }
}
