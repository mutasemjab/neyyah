@extends("layouts.admin")

@section('title', 'الوسطاء')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.matchmakers.index') }}" class="mb-0">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('messages.Search') }}..."
                                    value="{{ $searchQuery ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('messages.Filter_by_Status') }}</option>
                                    <option value="pending"  {{ ($statusFilter ?? '') === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ ($statusFilter ?? '') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                    <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> {{ __('messages.Search') }}</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.matchmakers.index') }}" class="btn btn-secondary w-100">{{ __('messages.All') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>الوسطاء</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('messages.Display_Name') }}</th>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <th>التخصصات</th>
                                    <th>الخبرة (سنوات)</th>
                                    <th>التقييم</th>
                                    <th>الحالات الناجحة</th>
                                    <th>الحالة</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $matchmaker)
                                <tr>
                                    <td><strong>{{ $matchmaker->user->display_name ?? '—' }}</strong></td>
                                    <td>{{ $matchmaker->user->phone ?? '—' }}</td>
                                    <td>
                                        @if ($matchmaker->specializations)
                                            @foreach (array_slice($matchmaker->specializations, 0, 2) as $spec)
                                                <span class="badge badge-light border">{{ $spec }}</span>
                                            @endforeach
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $matchmaker->years_experience }}</td>
                                    <td>
                                        <span class="text-warning"><i class="fas fa-star"></i></span>
                                        {{ number_format($matchmaker->rating_avg, 1) }}
                                        <small class="text-muted">({{ $matchmaker->ratings_count }})</small>
                                    </td>
                                    <td><span class="badge badge-success">{{ $matchmaker->success_cases }}</span></td>
                                    <td>
                                        @php
                                            $colors = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                                            $labels = ['approved' => 'موافق عليه', 'pending' => 'قيد المراجعة', 'rejected' => 'مرفوض'];
                                        @endphp
                                        <span class="badge badge-{{ $colors[$matchmaker->verification_status] ?? 'secondary' }}">
                                            {{ $labels[$matchmaker->verification_status] ?? $matchmaker->verification_status }}
                                        </span>
                                        @if (!$matchmaker->is_active)
                                            <span class="badge badge-secondary ml-1">معطّل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.matchmakers.show', $matchmaker->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">{{ __('messages.No_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $data->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
