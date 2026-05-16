@extends("layouts.admin")

@section('title', __('messages.Conversations'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Conversations') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Conversations') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{-- Stage Tabs --}}
                    <ul class="nav nav-tabs mb-3 flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link {{ !$stageFilter ? 'active' : '' }}"
                               href="{{ route('admin.conversations.index') }}">
                                {{ __('messages.All') }}
                            </a>
                        </li>
                        @foreach (['taaaruf','ihtimam','jiddiyya','family','khitba'] as $stage)
                        <li class="nav-item">
                            <a class="nav-link {{ $stageFilter === $stage ? 'active' : '' }}"
                               href="{{ route('admin.conversations.index', ['stage' => $stage]) }}">
                                {{ __('messages.Stage_' . $stage) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.User_1') }}</th>
                                    <th>{{ __('messages.User_2') }}</th>
                                    <th>{{ __('messages.Stage') }}</th>
                                    <th>{{ __('messages.Chat_Unlocked') }}</th>
                                    <th>{{ __('messages.Guided_Questions_Progress') }}</th>
                                    <th>{{ __('messages.Updated_At') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $conv)
                                <tr>
                                    <td>{{ $conv->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $conv->user1_id) }}">
                                            {{ $conv->user1->display_name ?? $conv->user1->phone }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $conv->user2_id) }}">
                                            {{ $conv->user2->display_name ?? $conv->user2->phone }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $stageColors = ['taaaruf'=>'info','ihtimam'=>'primary','jiddiyya'=>'warning','family'=>'success','khitba'=>'danger'];
                                            $sc = $stageColors[$conv->stage] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $sc }}">
                                            {{ __('messages.Stage_' . $conv->stage) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($conv->is_chat_unlocked)
                                            <span class="badge badge-success"><i class="fas fa-unlock"></i> {{ __('messages.Yes') }}</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-lock"></i> {{ __('messages.No') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>U1: {{ $conv->questions_completed_u1 }} / U2: {{ $conv->questions_completed_u2 }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $conv->updated_at?->diffForHumans() ?? '—' }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.conversations.show', $conv->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> {{ __('messages.Show') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
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
