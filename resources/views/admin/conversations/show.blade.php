@extends("layouts.admin")

@section('title', __('messages.Conversation_Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.conversations.index') }}">{{ __('messages.Conversations') }}</a></li>
                        <li class="breadcrumb-item active">#{{ $conversation->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Conversation_Details') }} #{{ $conversation->id }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Details') }}</h6></div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-5">
                            <strong>{{ __('messages.User_A') }}</strong><br>
                            <a href="{{ route('admin.users.show', $conversation->user_a_id) }}">
                                {{ $conversation->userA->profile->display_name ?? $conversation->userA->phone }}
                            </a>
                        </div>
                        <div class="col-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrows-alt-h fa-2x text-muted"></i>
                        </div>
                        <div class="col-5">
                            <strong>{{ __('messages.User_B') }}</strong><br>
                            <a href="{{ route('admin.users.show', $conversation->user_b_id) }}">
                                {{ $conversation->userB->profile->display_name ?? $conversation->userB->phone }}
                            </a>
                        </div>
                    </div>

                    <hr>

                    <p><strong>{{ __('messages.Stage') }}:</strong>
                        @php
                            $stageColors = ['taaaruf'=>'info','ihtimam'=>'primary','jiddiyya'=>'warning','family'=>'success','khitba'=>'danger'];
                            $sc = $stageColors[$conversation->stage] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $sc }}">
                            {{ __('messages.Stage_' . $conversation->stage) }}
                        </span>
                    </p>

                    <p><strong>{{ __('messages.Chat_Unlocked') }}:</strong>
                        @if ($conversation->is_chat_unlocked)
                            <span class="badge badge-success">{{ __('messages.Yes') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('messages.No') }}</span>
                        @endif
                    </p>

                    @if ($conversation->chat_unlocked_at)
                    <p><strong>{{ __('messages.Chat_Expires_At') }}:</strong>
                        {{ $conversation->chat_expires_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    @endif

                    <p><strong>{{ __('messages.Messages_Count') }}:</strong>
                        <span class="badge badge-info">{{ $messagesCount }}</span></p>

                    <p><strong>{{ __('messages.Last_Activity') }}:</strong>
                        {{ $conversation->last_activity_at?->format('Y-m-d H:i') ?? '—' }}</p>

                    <p><strong>{{ __('messages.Unread_A') }}:</strong>
                        <span class="badge badge-warning">{{ $conversation->unread_count_a ?? 0 }}</span></p>
                    <p><strong>{{ __('messages.Unread_B') }}:</strong>
                        <span class="badge badge-warning">{{ $conversation->unread_count_b ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Guided_Questions_Progress') }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <strong>{{ __('messages.User_A') }}</strong>
                            <div class="display-4">{{ $conversation->guided_questions_answered_a ?? 0 }}</div>
                            <small class="text-muted">{{ __('messages.Guided_Questions') }}</small>
                        </div>
                        <div class="col-6 text-center">
                            <strong>{{ __('messages.User_B') }}</strong>
                            <div class="display-4">{{ $conversation->guided_questions_answered_b ?? 0 }}</div>
                            <small class="text-muted">{{ __('messages.Guided_Questions') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            @if ($conversation->matchRequest)
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Match_Request') }}</h6></div>
                <div class="card-body">
                    <p><strong>{{ __('messages.Request_Status') }}:</strong>
                        {{ __('messages.' . ucfirst($conversation->matchRequest->status)) }}</p>
                    <a href="{{ route('admin.match-requests.show', $conversation->matchRequest->id) }}"
                       class="btn btn-sm btn-outline-info">
                        {{ __('messages.Request_Details') }}
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>
@endsection
