@extends("layouts.admin")

@section('title', __('messages.Report'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">{{ __('messages.Reports') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Report') }} #{{ $report->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Report') }} #{{ $report->id }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Details') }}</h6></div>
                <div class="card-body">
                    <p><strong>{{ __('messages.Reporter') }}:</strong>
                        <a href="{{ route('admin.users.show', $report->reporter_id) }}">
                            {{ $report->reporter->profile->display_name ?? $report->reporter->phone }}
                        </a>
                    </p>
                    <p><strong>{{ __('messages.Reported_User') }}:</strong>
                        <a href="{{ route('admin.users.show', $report->reported_user_id) }}">
                            {{ $report->reportedUser->profile->display_name ?? $report->reportedUser->phone }}
                        </a>
                    </p>
                    <p><strong>{{ __('messages.Report_Reason') }}:</strong>
                        <span class="badge badge-warning">{{ $report->reason }}</span>
                    </p>
                    @if ($report->details)
                    <p><strong>{{ __('messages.Details') }}:</strong><br>
                        <span class="text-muted">{{ $report->details }}</span></p>
                    @endif
                    <p><strong>{{ __('messages.Created') }}:</strong>
                        {{ $report->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('messages.Action_Taken') }}</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.update', $report->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label>{{ __('messages.Is_Reviewed') }}</label>
                            <select name="is_reviewed" class="form-control">
                                <option value="0" {{ !$report->is_reviewed ? 'selected' : '' }}>
                                    {{ __('messages.No') }}
                                </option>
                                <option value="1" {{ $report->is_reviewed ? 'selected' : '' }}>
                                    {{ __('messages.Yes') }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Action_Taken') }}</label>
                            <textarea name="action_taken" class="form-control" rows="3"
                                >{{ old('action_taken', $report->action_taken) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Admin_Notes') }}</label>
                            <textarea name="admin_notes" class="form-control" rows="3"
                                >{{ old('admin_notes', $report->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.Save') }}
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary ml-2">
                            {{ __('messages.Back') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
