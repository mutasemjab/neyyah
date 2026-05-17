@extends("layouts.admin")

@section('title', 'تفاصيل الطلب الخاص')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.private-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right mr-1"></i> {{ __('messages.Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h6>تفاصيل الطلب</h6></div>
                <div class="card-body">
                    @php $sc = ['pending_payment'=>'warning','active'=>'success','searching'=>'info','candidates_sent'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>المستخدم</th><td>{{ $pmRequest->user->display_name ?? '—' }} ({{ $pmRequest->user->phone ?? '' }})</td></tr>
                        <tr><th>الوسيط</th><td>{{ $pmRequest->matchmaker->user->display_name ?? '—' }}</td></tr>
                        <tr><th>الباقة</th><td>{{ $pmRequest->package->name_ar ?? '—' }}</td></tr>
                        <tr><th>السعر</th><td>{{ $pmRequest->price }} JD</td></tr>
                        <tr><th>الحالة</th><td><span class="badge badge-{{ $sc[$pmRequest->status] ?? 'secondary' }}">{{ $pmRequest->status }}</span></td></tr>
                        <tr><th>مرجع الدفع</th><td>{{ $pmRequest->payment_reference ?? '—' }}</td></tr>
                        <tr><th>ينتهي في</th><td>{{ $pmRequest->expires_at?->format('Y-m-d') ?? '—' }}</td></tr>
                        <tr><th>تاريخ الإنشاء</th><td>{{ $pmRequest->created_at->format('Y-m-d H:i') }}</td></tr>
                    </table>

                    @if ($pmRequest->notes_ar)
                    <div class="alert alert-info mt-3 mb-0">
                        <strong>ملاحظات الوسيط:</strong> {{ $pmRequest->notes_ar }}
                    </div>
                    @endif

                    @if ($pmRequest->cancelled_reason_ar)
                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>سبب الإلغاء:</strong> {{ $pmRequest->cancelled_reason_ar }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h6>المرشحون ({{ $pmRequest->candidates->count() }})</h6></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr><th>تفاصيل المرشح</th><th>ملاحظة الوسيط</th><th>الحالة</th><th>الرد في</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($pmRequest->candidates as $candidate)
                            <tr>
                                <td><small>{{ json_encode($candidate->candidate_details, JSON_UNESCAPED_UNICODE) }}</small></td>
                                <td>{{ $candidate->note_ar ?? '—' }}</td>
                                <td>
                                    @php $cs = ['pending'=>'secondary','accepted'=>'success','rejected'=>'danger']; @endphp
                                    <span class="badge badge-{{ $cs[$candidate->status] ?? 'secondary' }}">{{ $candidate->status }}</span>
                                </td>
                                <td><small class="text-muted">{{ $candidate->responded_at?->format('Y-m-d') ?? '—' }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">لا يوجد مرشحون</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
