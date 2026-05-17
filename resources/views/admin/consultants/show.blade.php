@extends("layouts.admin")

@section('title', 'تفاصيل المستشار')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.consultants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right mr-1"></i> {{ __('messages.Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $consultant->user->display_name ?? '—' }}</h5>
                    <p class="text-primary mb-1">{{ $consultant->title_ar }}</p>
                    <p class="text-muted small">{{ $consultant->user->phone ?? '' }}</p>

                    @php
                        $colors = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                        $labels = ['approved' => 'موافق عليه', 'pending' => 'قيد المراجعة', 'rejected' => 'مرفوض'];
                    @endphp
                    <span class="badge badge-{{ $colors[$consultant->verification_status] ?? 'secondary' }} px-3 py-2 mb-3">
                        {{ $labels[$consultant->verification_status] ?? $consultant->verification_status }}
                    </span>

                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="h4 mb-0 text-warning">{{ number_format($consultant->rating_avg, 1) }}</div>
                            <small class="text-muted">التقييم</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0">{{ $consultant->years_experience }}</div>
                            <small class="text-muted">سنوات</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-success">{{ $consultant->session_price }}</div>
                            <small class="text-muted">JD/جلسة</small>
                        </div>
                    </div>
                </div>
            </div>

            @if ($consultant->verification_status === 'pending')
            <div class="card">
                <div class="card-header"><h6 class="mb-0">الإجراءات</h6></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.consultants.approve', $consultant->id) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i> {{ __('messages.Approve') }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.consultants.reject', $consultant->id) }}">
                        @csrf
                        <div class="form-group mb-2">
                            <textarea name="reason_ar" class="form-control" rows="2"
                                placeholder="{{ __('messages.Enter_Rejection_Reason') }}" required></textarea>
                        </div>
                        <button class="btn btn-danger btn-block"><i class="fas fa-times mr-1"></i> {{ __('messages.Reject') }}</button>
                    </form>
                </div>
            </div>
            @endif

            @if ($consultant->verification_status === 'approved')
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.consultants.toggle', $consultant->id) }}">
                        @csrf
                        <button class="btn btn-{{ $consultant->is_active ? 'warning' : 'success' }} btn-block">
                            {{ $consultant->is_active ? 'تعطيل المستشار' : 'تفعيل المستشار' }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">نبذة</h6></div>
                <div class="card-body">
                    <p>{{ $consultant->bio_ar }}</p>
                    @if ($consultant->specializations)
                        <strong>التخصصات:</strong>
                        <div class="mt-1">
                            @foreach ($consultant->specializations as $spec)
                                <span class="badge badge-info mr-1">{{ $spec }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-2">
                        <strong>طرق الاجتماع:</strong>
                        @foreach ($consultant->meeting_types ?? [] as $type)
                            <span class="badge badge-secondary mr-1">{{ $type }}</span>
                        @endforeach
                    </div>
                    @if ($consultant->rejection_reason_ar)
                        <div class="alert alert-danger mt-3">
                            <strong>سبب الرفض:</strong> {{ $consultant->rejection_reason_ar }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="mb-0">أوقات التوافر</h6></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr><th>اليوم</th><th>من</th><th>إلى</th></tr>
                        </thead>
                        <tbody>
                            @php $days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت']; @endphp
                            @forelse ($consultant->availabilities as $slot)
                            <tr>
                                <td>{{ $days[$slot->day_of_week] ?? $slot->day_of_week }}</td>
                                <td>{{ $slot->start_time }}</td>
                                <td>{{ $slot->end_time }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">لا توجد أوقات محددة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">الجلسات ({{ $consultant->sessions->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr><th>التاريخ</th><th>الوقت</th><th>نوع الاجتماع</th><th>الحالة</th><th>السعر</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($consultant->sessions->take(10) as $session)
                            <tr>
                                <td>{{ $session->session_date->format('Y-m-d') }}</td>
                                <td>{{ $session->start_time }}–{{ $session->end_time }}</td>
                                <td><span class="badge badge-info">{{ $session->meeting_type }}</span></td>
                                <td>
                                    @php
                                        $sc = ['confirmed'=>'success','completed'=>'success','ongoing'=>'primary','cancelled'=>'danger','pending_payment'=>'warning'];
                                    @endphp
                                    <span class="badge badge-{{ $sc[$session->status] ?? 'secondary' }}">{{ $session->status }}</span>
                                </td>
                                <td>{{ $session->price }} JD</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">لا توجد جلسات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
