@extends("layouts.admin")

@section('title', __('messages.Users'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Users') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Users') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('messages.Search') }}..."
                                    value="{{ $searchQuery ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('messages.Filter_by_Status') }}</option>
                                    <option value="pending"   {{ ($statusFilter ?? '') === 'pending'   ? 'selected' : '' }}>{{ __('messages.Pending') }}</option>
                                    <option value="active"    {{ ($statusFilter ?? '') === 'active'    ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                                    <option value="suspended" {{ ($statusFilter ?? '') === 'suspended' ? 'selected' : '' }}>{{ __('messages.Suspended') }}</option>
                                    <option value="banned"    {{ ($statusFilter ?? '') === 'banned'    ? 'selected' : '' }}>{{ __('messages.Banned') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> {{ __('messages.Search') }}
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">
                                    {{ __('messages.All') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Display_Name') }}</th>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <th>{{ __('messages.Gender') }}</th>
                                    <th>{{ __('messages.Status') }}</th>
                                    <th>{{ __('messages.Is_Verified') }}</th>
                                    <th>{{ __('messages.Completion_Pct') }}</th>
                                    <th>{{ __('messages.Joined') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <strong>{{ $user->display_name ?? '—' }}</strong>
                                    </td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($user->gender === 'male')
                                            <span class="badge badge-info">{{ __('messages.Male') }}</span>
                                        @elseif ($user->gender === 'female')
                                            <span class="badge badge-danger">{{ __('messages.Female') }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active'    => 'success',
                                                'pending'   => 'warning',
                                                'suspended' => 'secondary',
                                                'banned'    => 'danger',
                                                'deleted'   => 'dark',
                                            ];
                                            $color = $statusColors[$user->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $color }}">
                                            {{ __('messages.' . ucfirst($user->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($user->is_verified)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height:6px; min-width:80px;">
                                            <div class="progress-bar bg-success"
                                                 style="width:{{ round($user->completion_pct * 100) }}%">
                                            </div>
                                        </div>
                                        <small>{{ round($user->completion_pct * 100) }}%</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> {{ __('messages.Show') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        {{ __('messages.No_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
