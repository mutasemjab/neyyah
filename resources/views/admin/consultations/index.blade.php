@extends("layouts.admin")

@section('title', 'الجلسات الاستشارية')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.consultations.index') }}" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('messages.Search') }}..."
                                    value="{{ $searchQuery ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">الحالة</option>
                                    @foreach (['pending_payment','confirmed','ongoing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ ($statusFilter ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="meeting_type" class="form-control">
                                    <option value="">نوع الاجتماع</option>
                                    <option value="voice"  {{ ($meetingFilter ?? '') === 'voice'  ? 'selected' : '' }}>صوت</option>
                                    <option value="video"  {{ ($meetingFilter ?? '') === 'video'  ? 'selected' : '' }}>فيديو</option>
                                    <option value="chat"   {{ ($meetingFilter ?? '') === 'chat'   ? 'selected' : '' }}>دردشة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> بحث</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary w-100">{{ __('messages.All') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>الجلسات الاستشارية</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.consultations.reviews') }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-star mr-1"></i> التقييمات
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>المستشار</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>نوع الاجتماع</th>
                                    <th>الحالة</th>
                                    <th>السعر</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $session)
                                @php
                                    $sc = ['pending_payment'=>'warning','confirmed'=>'success','ongoing'=>'primary','completed'=>'success','cancelled'=>'danger'];
                                @endphp
                                <tr>
                                    <td>{{ $session->user->display_name ?? '—' }}</td>
                                    <td>{{ $session->consultant->user->display_name ?? '—' }}</td>
                                    <td>{{ $session->session_date->format('Y-m-d') }}</td>
                                    <td>{{ $session->start_time }}–{{ $session->end_time }}</td>
                                    <td><span class="badge badge-info">{{ $session->meeting_type }}</span></td>
                                    <td><span class="badge badge-{{ $sc[$session->status] ?? 'secondary' }}">{{ $session->status }}</span></td>
                                    <td>{{ $session->price }} JD</td>
                                    <td>
                                        <a href="{{ route('admin.consultations.show', $session->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">{{ __('messages.No_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $data->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
