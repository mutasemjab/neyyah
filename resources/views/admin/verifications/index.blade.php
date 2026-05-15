@extends("layouts.admin")

@section('title', __('messages.Identity_Verifications'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Identity_Verifications') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Identity_Verifications') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{-- Status Tabs --}}
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === 'pending' ? 'active' : '' }}"
                               href="{{ route('admin.verifications.index', ['status' => 'pending']) }}">
                                {{ __('messages.Pending') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === 'approved' ? 'active' : '' }}"
                               href="{{ route('admin.verifications.index', ['status' => 'approved']) }}">
                                {{ __('messages.Approved') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === 'rejected' ? 'active' : '' }}"
                               href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}">
                                {{ __('messages.Rejected') }}
                            </a>
                        </li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.User') }}</th>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <th>{{ __('messages.Document_Type') }}</th>
                                    <th>{{ __('messages.Status') }}</th>
                                    <th>{{ __('messages.Created') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $v)
                                <tr>
                                    <td>{{ $v->id }}</td>
                                    <td>{{ $v->user->profile->display_name ?? '—' }}</td>
                                    <td>{{ $v->user->country_code }} {{ $v->user->phone }}</td>
                                    <td>{{ __('messages.' . ($v->document_type === 'national_id' ? 'National_ID' : ucfirst($v->document_type ?? ''))) }}</td>
                                    <td>
                                        @if ($v->status === 'approved')
                                            <span class="badge badge-success">{{ __('messages.Approved') }}</span>
                                        @elseif ($v->status === 'rejected')
                                            <span class="badge badge-danger">{{ __('messages.Rejected') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('messages.Pending') }}</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $v->created_at->format('Y-m-d') }}</small></td>
                                    <td>
                                        <a href="{{ route('admin.verifications.show', $v->id) }}"
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
