<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\GuidedQuestion;
use Illuminate\Http\Request;

class GuidedQuestionController extends Controller
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
        if (!$this->admin()->can('guided-questions-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $query = GuidedQuestion::withCount('answers');

        if ($request->filled('category')) {
            $query->where('category_ar', $request->category);
        }

        $data       = $query->orderBy('sort_order')->paginate(PAGINATION_COUNT);
        $categories = GuidedQuestion::distinct()->pluck('category_ar')->filter()->sort()->values();

        return view('admin.guided-questions.index', compact('data', 'categories'));
    }

    public function create()
    {
        if (!$this->admin()->can('guided-questions-add')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        return view('admin.guided-questions.create');
    }

    public function store(Request $request)
    {
        if (!$this->admin()->can('guided-questions-add')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'text_ar'     => 'required|string',
            'hint_ar'     => 'nullable|string|max:255',
            'category_ar' => 'nullable|string|max:60',
            'sort_order'  => 'integer|min:0',
        ]);

        GuidedQuestion::create([
            'text_ar'     => $request->text_ar,
            'hint_ar'     => $request->hint_ar,
            'category_ar' => $request->category_ar,
            'sort_order'  => $request->input('sort_order', 0),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.guided-questions.index')
            ->with('success', __('messages.Question_Created'));
    }

    public function edit(string $id)
    {
        if (!$this->admin()->can('guided-questions-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $question = GuidedQuestion::findOrFail($id);

        return view('admin.guided-questions.edit', compact('question'));
    }

    public function update(Request $request, string $id)
    {
        if (!$this->admin()->can('guided-questions-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'text_ar'     => 'required|string',
            'hint_ar'     => 'nullable|string|max:255',
            'category_ar' => 'nullable|string|max:60',
            'sort_order'  => 'integer|min:0',
        ]);

        GuidedQuestion::findOrFail($id)->update([
            'text_ar'     => $request->text_ar,
            'hint_ar'     => $request->hint_ar,
            'category_ar' => $request->category_ar,
            'sort_order'  => $request->input('sort_order', 0),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.guided-questions.index')
            ->with('success', __('messages.Question_Updated'));
    }

    public function destroy(Request $request, string $id)
    {
        if (!$this->admin()->can('guided-questions-delete')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        GuidedQuestion::findOrFail($id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.guided-questions.index')
            ->with('success', __('messages.Question_Deleted'));
    }
}
