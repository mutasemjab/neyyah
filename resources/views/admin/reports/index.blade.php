@extends("layouts.admin")

@section('title', __('messages.Reports'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Reports') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Reports') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{-- Tabs --}}
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === 'unreviewed' ? 'active' : '' }}"
                               href="{{ route('admin.reports.index') }}">
                                {{ __('messages.Unreviewed') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === 'reviewed' ? 'active' : '' }}"
                               href="{{ route('admin.reports.index', ['status' => 'reviewed']) }}">
                                {{ __('messages.Is_Reviewed') }}
                            </a>
                        </li>
                    </ul>

                    {{-- Reason filter --}}
                    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-3">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="reason" class="form-control">
                                    <option value="">{{ __('messages.Report_Reason') }} — {{ __('messages.All') }}</option>
                                    @foreach (['fake_profile','inappropriate_content','harassment','spam','underage','other'] as $r)
                                        <option value="{{ $r }}" {{ $reasonFilter === $r ? 'selected' : '' }}>
                                            {{ __('messages.' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $r)))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('messages.Filter_by_Status') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Reporter') }}</th>
                                    <th>{{ __('messages.Reported_User') }}</th>
                                    <th>{{ __('messages.Report_Reason') }}</th>
                                    <th>{{ __('messages.Is_Reviewed') }}</th>
                                    <th>{{ __('messages.Created') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ $report->reporter->profile->display_name ?? $report->reporter->phone }}</td>
                                    <td>{{ $report->reportedUser->profile->display_name ?? $report->reportedUser->phone }}</td>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ $report->reason }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($report->is_reviewed)
                                            <span class="badge badge-success">{{ __('messages.Is_Reviewed') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('messages.Unreviewed') }}</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $report->created_at->format('Y-m-d') }}</small></td>
                                    <td>
                                        <a href="{{ route('admin.reports.show', $report->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> {{ __('messages.Show') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('messages.No_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $data->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
