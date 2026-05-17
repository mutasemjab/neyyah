<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateMatchRequest;
use Illuminate\Http\Request;

class PrivateRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PrivateMatchRequest::with(['user', 'matchmaker.user', 'package']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) =>
                $q->where('display_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")
            );
        }

        $data         = $query->latest()->paginate(PAGINATION_COUNT);
        $statusFilter = $request->status;
        $searchQuery  = $request->search;

        return view('admin.private-requests.index', compact('data', 'statusFilter', 'searchQuery'));
    }

    public function show(string $id)
    {
        $pmRequest = PrivateMatchRequest::with(['user', 'matchmaker.user', 'package', 'candidates'])->findOrFail($id);

        return view('admin.private-requests.show', compact('pmRequest'));
    }
}
