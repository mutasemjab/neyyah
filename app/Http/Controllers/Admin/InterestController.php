<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Interest;
use Illuminate\Http\Request;

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

        $data = Interest::latest()->paginate(PAGINATION_COUNT);

        return view('admin.interests.index', compact('data'));
    }

    public function create()
    {
        if (!$this->admin()->can('interests-add')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        return view('admin.interests.create');
    }

    public function store(Request $request)
    {
        if (!$this->admin()->can('interests-add')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'name_ar'   => 'required|string|max:40',
            'name_en'   => 'nullable|string|max:40',
            'icon'      => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        Interest::create([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
            'icon'      => $request->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.interests.index')
            ->with('success', __('messages.Interest_Created'));
    }

    public function edit(int $id)
    {
        if (!$this->admin()->can('interests-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $interest = Interest::findOrFail($id);

        return view('admin.interests.edit', compact('interest'));
    }

    public function update(Request $request, int $id)
    {
        if (!$this->admin()->can('interests-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'name_ar'   => 'required|string|max:40',
            'name_en'   => 'nullable|string|max:40',
            'icon'      => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        Interest::findOrFail($id)->update([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
            'icon'      => $request->icon,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.interests.index')
            ->with('success', __('messages.Interest_Updated'));
    }

    public function destroy(Request $request, int $id)
    {
        if (!$this->admin()->can('interests-delete')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        Interest::findOrFail($id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.interests.index')
            ->with('success', __('messages.Interest_Deleted'));
    }
}
