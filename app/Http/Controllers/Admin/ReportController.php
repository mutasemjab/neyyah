<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
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
        if (!$this->admin()->can('reports-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $statusFilter = $request->input('status', 'pending');
        $reasonFilter = $request->reason;

        $query = Report::with('reporter.profile', 'reportedUser.profile')
            ->where('status', $statusFilter);

        if ($request->filled('reason')) {
            $query->where('reason', $reasonFilter);
        }

        $data = $query->latest()->paginate(PAGINATION_COUNT);

        return view('admin.reports.index', compact('data', 'statusFilter', 'reasonFilter'));
    }

    public function show(int $id)
    {
        if (!$this->admin()->can('reports-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $report = Report::with('reporter.profile', 'reportedUser.profile')->findOrFail($id);

        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, int $id)
    {
        if (!$this->admin()->can('reports-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'status'      => 'required|in:pending,reviewed,actioned,dismissed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        Report::findOrFail($id)->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', __('messages.Report_Updated'));
    }
}
