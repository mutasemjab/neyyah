@extends("layouts.admin")

@section('title', __('messages.Match_Request'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.match-requests.index') }}">{{ __('messages.Match_Requests') }}</a></li>
                        <li class="breadcrumb-item active">#{{ $matchRequest->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Match_Request') }} #{{ $matchRequest->id }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Request_Details') }}</h6></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6 text-center">
                            <strong class="d-block mb-1">{{ __('messages.From_User') }}</strong>
                            <a href="{{ route('admin.users.show', $matchRequest->from_user_id) }}">
                                {{ $matchRequest->fromUser->profile->display_name ?? $matchRequest->fromUser->phone }}
                            </a>
                        </div>
                        <div class="col-6 text-center">
                            <strong class="d-block mb-1">{{ __('messages.To_User') }}</strong>
                            <a href="{{ route('admin.users.show', $matchRequest->to_user_id) }}">
                                {{ $matchRequest->toUser->profile->display_name ?? $matchRequest->toUser->phone }}
                            </a>
                        </div>
                    </div>

                    <p><strong>{{ __('messages.Request_Status') }}:</strong>
                        @php
                            $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','expired'=>'secondary','cancelled'=>'dark'];
                            $c = $colors[$matchRequest->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $c }}">{{ __('messages.' . ucfirst($matchRequest->status)) }}</span>
                    </p>
                    <p><strong>{{ __('messages.Expires_At') }}:</strong>
                        {{ $matchRequest->expires_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    <p><strong>{{ __('messages.Created') }}:</strong>
                        {{ $matchRequest->created_at->format('Y-m-d H:i') }}</p>

                    @if ($matchRequest->reason)
                    <hr>
                    <p><strong>{{ __('messages.Request_Reason') }}:</strong><br>
                        <span class="text-muted">{{ $matchRequest->reason }}</span></p>
                    @endif

                    @if ($matchRequest->life_goals)
                    <p><strong>{{ __('messages.Life_Goals') }}:</strong><br>
                        <span class="text-muted">{{ $matchRequest->life_goals }}</span></p>
                    @endif

                    @if ($matchRequest->marriage_expectations)
                    <p><strong>{{ __('messages.Marriage_Expectations') }}:</strong><br>
                        <span class="text-muted">{{ $matchRequest->marriage_expectations }}</span></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            @if ($matchRequest->conversation)
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Conversation') }}</h6></div>
                <div class="card-body">
                    <p><strong>{{ __('messages.Stage') }}:</strong>
                        {{ __('messages.Stage_' . $matchRequest->conversation->stage) }}</p>
                    <p><strong>{{ __('messages.Chat_Unlocked') }}:</strong>
                        {{ $matchRequest->conversation->is_chat_unlocked ? __('messages.Yes') : __('messages.No') }}</p>
                    <a href="{{ route('admin.conversations.show', $matchRequest->conversation->id) }}"
                       class="btn btn-sm btn-outline-info">
                        <i class="fas fa-comments"></i> {{ __('messages.Conversation_Details') }}
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.match-requests.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>
@endsection
