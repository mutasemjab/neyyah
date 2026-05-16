@extends("layouts.admin")

@section('title', __('messages.Match_Requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Match_Requests') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Match_Requests') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{-- Status Tabs --}}
                    <ul class="nav nav-tabs mb-3">
                        @foreach (['', 'pending', 'accepted', 'declined'] as $s)
                        <li class="nav-item">
                            <a class="nav-link {{ $statusFilter === $s ? 'active' : '' }}"
                               href="{{ route('admin.match-requests.index', $s ? ['status' => $s] : []) }}">
                                {{ $s ? __('messages.' . ucfirst($s)) : __('messages.All') }}
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.From_User') }}</th>
                                    <th>{{ __('messages.To_User') }}</th>
                                    <th>{{ __('messages.Request_Status') }}</th>
                                    <th>{{ __('messages.Responded_At') }}</th>
                                    <th>{{ __('messages.Sent_At') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $req)
                                <tr>
                                    <td>{{ $req->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $req->from_user_id) }}">
                                            {{ $req->fromUser->display_name ?? $req->fromUser->phone }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $req->to_user_id) }}">
                                            {{ $req->toUser->display_name ?? $req->toUser->phone }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','expired'=>'secondary','cancelled'=>'dark'];
                                            $c = $colors[$req->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $c }}">
                                            {{ __('messages.' . ucfirst($req->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $req->responded_at?->format('Y-m-d') ?? '—' }}
                                        </small>
                                    </td>
                                    <td><small>{{ $req->sent_at?->format('Y-m-d') ?? '—' }}</small></td>
                                    <td>
                                        <a href="{{ route('admin.match-requests.show', $req->id) }}"
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
