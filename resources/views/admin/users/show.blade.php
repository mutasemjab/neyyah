@extends("layouts.admin")

@section('title', __('messages.User_Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('messages.Users') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.User_Details') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.User_Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Profile Info --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @php $mainPhoto = $user->profileImages->first(); @endphp
                    @if ($mainPhoto)
                        <img src="{{ asset($mainPhoto->url) }}"
                             class="rounded-circle img-thumbnail mb-3"
                             style="width:120px;height:120px;object-fit:cover;"
                             alt="">
                    @else
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:120px;height:120px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif

                    <h5>{{ $user->display_name ?? '—' }}</h5>
                    <p class="text-muted mb-1">{{ $user->phone }}</p>

                    @php
                        $statusColors = ['active'=>'success','pending'=>'warning','suspended'=>'secondary','banned'=>'danger'];
                        $color = $statusColors[$user->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }} mb-3">
                        {{ __('messages.' . ucfirst($user->status)) }}
                    </span>

                    {{-- Change Status --}}
                    <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <div class="input-group">
                            <select name="status" class="form-control form-control-sm">
                                <option value="pending"   {{ $user->status === 'pending'   ? 'selected':'' }}>{{ __('messages.Pending') }}</option>
                                <option value="active"    {{ $user->status === 'active'    ? 'selected':'' }}>{{ __('messages.Active') }}</option>
                                <option value="suspended" {{ $user->status === 'suspended' ? 'selected':'' }}>{{ __('messages.Suspended') }}</option>
                                <option value="banned"    {{ $user->status === 'banned'    ? 'selected':'' }}>{{ __('messages.Banned') }}</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-warning" type="submit">
                                    {{ __('messages.Change_Status') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Details') }}</h6></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('messages.Sent_Requests') }}</span>
                            <span class="badge badge-primary">{{ $sentCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('messages.Received_Requests') }}</span>
                            <span class="badge badge-info">{{ $receivedCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('messages.Total_Conversations_Count') }}</span>
                            <span class="badge badge-success">{{ $convoCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('messages.Blocks') }}</span>
                            <span class="badge badge-secondary">{{ $blocksCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('messages.Reports_Count') }}</span>
                            <span class="badge badge-danger">{{ $reportsCount }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Right Column: Details --}}
        <div class="col-md-8">

            {{-- Profile Details --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.User_Profile') }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>{{ __('messages.Display_Name') }}:</strong> {{ $user->display_name ?? '—' }}</p>
                            <p><strong>{{ __('messages.Date_of_Birth') }}:</strong> {{ $user->birth_date?->format('Y-m-d') ?? '—' }}</p>
                            <p><strong>{{ __('messages.Age') }}:</strong> {{ $user->age ?? '—' }}</p>
                            <p><strong>{{ __('messages.Gender') }}:</strong> {{ $user->gender ? __('messages.' . ucfirst($user->gender)) : '—' }}</p>
                            <p><strong>{{ __('messages.City') }}:</strong> {{ $user->city ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>{{ __('messages.Religiosity') }}:</strong> {{ $user->religiosity_level ?? '—' }}</p>
                            <p><strong>{{ __('messages.Education') }}:</strong> {{ $user->education_level ?? '—' }}</p>
                            <p><strong>{{ __('messages.Income_Range') }}:</strong> {{ $user->income_range ?? '—' }}</p>
                            <p><strong>{{ __('messages.Is_Smoker') }}:</strong>
                                {{ $user->is_smoker ? __('messages.Yes') : __('messages.No') }}</p>
                            <p><strong>{{ __('messages.Marriage_Timeline') }}:</strong> {{ $user->marriage_timeline ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>{{ __('messages.Completion_Pct') }}:</strong></p>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-success"
                                     style="width:{{ round($user->completion_pct * 100) }}%"></div>
                            </div>
                            <small>{{ round($user->completion_pct * 100) }}%</small>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>{{ __('messages.Seriousness_Score') }}:</strong></p>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-info"
                                     style="width:{{ round($user->seriousness_score * 100) }}%"></div>
                            </div>
                            <small>{{ round($user->seriousness_score * 100) }}%</small>
                        </div>
                    </div>
                    @if ($user->bio)
                    <p class="mt-2"><strong>{{ __('messages.Bio') }}:</strong><br>
                        <span class="text-muted">{{ $user->bio }}</span>
                    </p>
                    @endif
                </div>
            </div>

            {{-- Photos --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Photos') }} ({{ $user->profileImages->count() }})</h6></div>
                <div class="card-body">
                    @if ($user->profileImages->count())
                    <div class="row">
                        @foreach ($user->profileImages as $photo)
                        <div class="col-3 mb-2">
                            <img src="{{ asset($photo->url) }}"
                                 class="img-fluid rounded"
                                 style="height:90px;width:100%;object-fit:cover;"
                                 alt="">
                            <small class="d-block text-center text-muted mt-1">
                                #{{ $photo->sort_order }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                    @else
                        <p class="text-muted mb-0">{{ __('messages.No_data') }}</p>
                    @endif
                </div>
            </div>

            {{-- Interests --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Interests') }}</h6></div>
                <div class="card-body">
                    @forelse ($user->interests as $interest)
                        <span class="badge badge-light border mr-1 mb-1" style="font-size:0.85rem;">
                            {{ $interest->label }}
                        </span>
                    @empty
                        <p class="text-muted mb-0">{{ __('messages.No_data') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Intent Card --}}
            @if ($user->intentCard)
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Intent_Card') }}</h6></div>
                <div class="card-body">
                    @php $ic = $user->intentCard; @endphp
                    <p><strong>{{ __('messages.Marriage_Timeline') }}:</strong> {{ $ic->target_timeline ?? '—' }}</p>
                    <p><strong>{{ __('messages.Life_Goals') }}:</strong> {{ $ic->children_intent ?? '—' }}</p>
                    @if ($ic->additional_notes)
                        <p><strong>{{ __('messages.Notes') }}:</strong> {{ $ic->additional_notes }}</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Verification Status --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Identity_Verification') }}</h6></div>
                <div class="card-body">
                    @if ($user->identityVerification)
                    @php $v = $user->identityVerification; @endphp
                        <p><strong>{{ __('messages.Document_Type') }}:</strong> {{ $v->id_type ?? '—' }}</p>
                        <p><strong>{{ __('messages.Status') }}:</strong>
                            @if ($v->status === 'approved')
                                <span class="badge badge-success">{{ __('messages.Approved') }}</span>
                            @elseif ($v->status === 'rejected')
                                <span class="badge badge-danger">{{ __('messages.Rejected') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('messages.Under_Review') }}</span>
                            @endif
                        </p>
                        <a href="{{ route('admin.verifications.show', $v->id) }}"
                           class="btn btn-sm btn-outline-info">
                            {{ __('messages.Show') }}
                        </a>
                    @else
                        <p class="text-muted mb-0">{{ __('messages.No_data') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
        </a>
    </div>
</div>
@endsection
