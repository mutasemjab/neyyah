<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestController extends Controller
{
    /** @return Admin */
    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index()
    {
        if (!$this->admin()->can('interests-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $data = UserInterest::select('label', DB::raw('count(*) as users_count'))
            ->groupBy('label')
            ->orderByDesc('users_count')
            ->paginate(PAGINATION_COUNT);

        return view('admin.interests.index', compact('data'));
    }

    public function destroy(Request $request, string $label)
    {
        if (!$this->admin()->can('interests-delete')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        UserInterest::where('label', $label)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.interests.index')
            ->with('success', __('messages.Interest_Deleted'));
    }
}
