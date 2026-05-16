<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\MarriageRequest;
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

        $query = MarriageRequest::with('fromUser', 'toUser');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data         = $query->latest('sent_at')->paginate(PAGINATION_COUNT);
        $statusFilter = $request->status;

        return view('admin.match-requests.index', compact('data', 'statusFilter'));
    }

    public function show(string $id)
    {
        if (!$this->admin()->can('match-requests-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $matchRequest = MarriageRequest::with('fromUser', 'toUser', 'conversation')
            ->findOrFail($id);

        return view('admin.match-requests.show', compact('matchRequest'));
    }
}
