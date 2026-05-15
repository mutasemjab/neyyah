@extends("layouts.admin")

@section('title', __('messages.Identity_Verification'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.verifications.index') }}">{{ __('messages.Identity_Verifications') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Details') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Identity_Verification') }} #{{ $verification->id }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.User') }}</h6></div>
                <div class="card-body">
                    <p><strong>{{ __('messages.Display_Name') }}:</strong>
                        {{ $verification->user->profile->display_name ?? '—' }}</p>
                    <p><strong>{{ __('messages.Phone') }}:</strong>
                        {{ $verification->user->country_code }} {{ $verification->user->phone }}</p>
                    <p><strong>{{ __('messages.Document_Type') }}:</strong>
                        {{ $verification->document_type ?? '—' }}</p>
                    <p><strong>{{ __('messages.Status') }}:</strong>
                        @if ($verification->status === 'approved')
                            <span class="badge badge-success">{{ __('messages.Approved') }}</span>
                        @elseif ($verification->status === 'rejected')
                            <span class="badge badge-danger">{{ __('messages.Rejected') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('messages.Pending') }}</span>
                        @endif
                    </p>
                    @if ($verification->rejection_reason)
                        <p><strong>{{ __('messages.Rejection_Reason') }}:</strong><br>
                            <span class="text-danger">{{ $verification->rejection_reason }}</span></p>
                    @endif
                    @if ($verification->reviewed_at)
                        <p><strong>{{ __('messages.Reviewed_At') }}:</strong>
                            {{ $verification->reviewed_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
            </div>

            {{-- Approve / Reject Actions --}}
            @if ($verification->status === 'pending')
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Action') }}</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.verifications.approve', $verification->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> {{ __('messages.Approve') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.verifications.reject', $verification->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="{{ __('messages.Enter_Rejection_Reason') }}" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-times"></i> {{ __('messages.Reject') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Details') }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        @if ($verification->document_front_path)
                        <div class="col-md-4 mb-3">
                            <p class="font-weight-bold text-center">{{ __('messages.Document_Front') }}</p>
                            <a href="{{ asset($verification->document_front_path) }}" target="_blank">
                                <img src="{{ asset($verification->document_front_path) }}"
                                     class="img-fluid rounded border" alt="Front">
                            </a>
                        </div>
                        @endif

                        @if ($verification->document_back_path)
                        <div class="col-md-4 mb-3">
                            <p class="font-weight-bold text-center">{{ __('messages.Document_Back') }}</p>
                            <a href="{{ asset($verification->document_back_path) }}" target="_blank">
                                <img src="{{ asset($verification->document_back_path) }}"
                                     class="img-fluid rounded border" alt="Back">
                            </a>
                        </div>
                        @endif

                        @if ($verification->selfie_path)
                        <div class="col-md-4 mb-3">
                            <p class="font-weight-bold text-center">{{ __('messages.Selfie') }}</p>
                            <a href="{{ asset($verification->selfie_path) }}" target="_blank">
                                <img src="{{ asset($verification->selfie_path) }}"
                                     class="img-fluid rounded border" alt="Selfie">
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.verifications.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>
@endsection
