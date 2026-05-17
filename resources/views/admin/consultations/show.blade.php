@extends("layouts.admin")

@section('title', 'تفاصيل الجلسة')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right mr-1"></i> {{ __('messages.Back') }}
            </a>
        </div>
    </div>

    @php $sc = ['pending_payment'=>'warning','confirmed'=>'success','ongoing'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6>تفاصيل الجلسة</h6></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>المستخدم</th><td>{{ $session->user->display_name ?? '—' }} ({{ $session->user->phone ?? '' }})</td></tr>
                        <tr><th>المستشار</th><td>{{ $session->consultant->user->display_name ?? '—' }}</td></tr>
                        <tr><th>التاريخ</th><td>{{ $session->session_date->format('Y-m-d') }}</td></tr>
                        <tr><th>الوقت</th><td>{{ $session->start_time }} – {{ $session->end_time }}</td></tr>
                        <tr><th>نوع الاجتماع</th><td><span class="badge badge-info">{{ $session->meeting_type }}</span></td></tr>
                        <tr><th>الحالة</th><td><span class="badge badge-{{ $sc[$session->status] ?? 'secondary' }}">{{ $session->status }}</span></td></tr>
                        <tr><th>السعر</th><td>{{ $session->price }} JD</td></tr>
                        <tr><th>مرجع الدفع</th><td>{{ $session->payment_reference ?? '—' }}</td></tr>
                        <tr><th>تاريخ الإنشاء</th><td>{{ $session->created_at->format('Y-m-d H:i') }}</td></tr>
                    </table>
                    @if ($session->cancelled_at)
                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>سبب الإلغاء:</strong> {{ $session->cancelled_reason_ar ?? '—' }}<br>
                        <small>{{ $session->cancelled_at->format('Y-m-d H:i') }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            @if ($session->review)
            <div class="card">
                <div class="card-header"><h6>التقييم</h6></div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $session->review->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ml-2 font-weight-bold">{{ $session->review->rating }}/5</span>
                    </div>
                    @if ($session->review->review_ar)
                    <p class="mb-0">{{ $session->review->review_ar }}</p>
                    @endif
                    <small class="text-muted">{{ $session->review->created_at->format('Y-m-d') }}</small>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <p>لم يتم تقديم تقييم بعد</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
