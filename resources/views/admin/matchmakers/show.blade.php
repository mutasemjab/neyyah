@extends("layouts.admin")

@section('title', 'تفاصيل الوسيط')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.matchmakers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right mr-1"></i> {{ __('messages.Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $matchmaker->user->display_name ?? '—' }}</h5>
                    <p class="text-muted mb-2">{{ $matchmaker->user->phone ?? '' }}</p>
                    <p class="text-muted small">{{ $matchmaker->user->city ?? '' }}</p>

                    @php
                        $colors = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                        $labels = ['approved' => 'موافق عليه', 'pending' => 'قيد المراجعة', 'rejected' => 'مرفوض'];
                    @endphp
                    <span class="badge badge-{{ $colors[$matchmaker->verification_status] ?? 'secondary' }} px-3 py-2 mb-3">
                        {{ $labels[$matchmaker->verification_status] ?? $matchmaker->verification_status }}
                    </span>

                    <div class="row text-center mt-3">
                        <div class="col-4">
                            <div class="h4 mb-0 text-warning">{{ number_format($matchmaker->rating_avg, 1) }}</div>
                            <small class="text-muted">التقييم</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-success">{{ $matchmaker->success_cases }}</div>
                            <small class="text-muted">نجاح</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0">{{ $matchmaker->years_experience }}</div>
                            <small class="text-muted">سنوات</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if ($matchmaker->verification_status === 'pending')
            <div class="card">
                <div class="card-header"><h6 class="mb-0">الإجراءات</h6></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.matchmakers.approve', $matchmaker->id) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-success btn-block">
                            <i class="fas fa-check mr-1"></i> {{ __('messages.Approve') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.matchmakers.reject', $matchmaker->id) }}">
                        @csrf
                        <div class="form-group mb-2">
                            <textarea name="reason_ar" class="form-control" rows="2"
                                placeholder="{{ __('messages.Enter_Rejection_Reason') }}" required></textarea>
                        </div>
                        <button class="btn btn-danger btn-block">
                            <i class="fas fa-times mr-1"></i> {{ __('messages.Reject') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if ($matchmaker->verification_status === 'approved')
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.matchmakers.toggle', $matchmaker->id) }}">
                        @csrf
                        <button class="btn btn-{{ $matchmaker->is_active ? 'warning' : 'success' }} btn-block">
                            {{ $matchmaker->is_active ? 'تعطيل الوسيط' : 'تفعيل الوسيط' }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">نبذة عن الوسيط</h6></div>
                <div class="card-body">
                    <p>{{ $matchmaker->bio_ar }}</p>
                    @if ($matchmaker->specializations)
                        <strong>التخصصات:</strong>
                        <div class="mt-1">
                            @foreach ($matchmaker->specializations as $spec)
                                <span class="badge badge-info mr-1">{{ $spec }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if ($matchmaker->rejection_reason_ar)
                        <div class="alert alert-danger mt-3">
                            <strong>سبب الرفض:</strong> {{ $matchmaker->rejection_reason_ar }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Packages --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">الباقات ({{ $matchmaker->packages->count() }})</h6></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>الاسم</th><th>السعر</th><th>المدة</th><th>المرشحون</th><th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($matchmaker->packages as $pkg)
                            <tr>
                                <td>{{ $pkg->name_ar }}</td>
                                <td>{{ $pkg->price }} JD</td>
                                <td>{{ $pkg->duration_days }} يوم</td>
                                <td>{{ $pkg->candidate_limit }}</td>
                                <td>
                                    @if ($pkg->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">معطّل</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">لا توجد باقات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Posts --}}
            <div class="card">
                <div class="card-header"><h6 class="mb-0">المنشورات ({{ $matchmaker->posts->count() }})</h6></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr><th>الجنس</th><th>الفئة العمرية</th><th>المدينة</th><th>الاهتمامات</th><th>الحالة</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($matchmaker->posts as $post)
                            <tr>
                                <td>{{ $post->gender === 'male' ? 'ذكر' : 'أنثى' }}</td>
                                <td>{{ $post->age_from }}–{{ $post->age_to }}</td>
                                <td>{{ $post->city ?? '—' }}</td>
                                <td><span class="badge badge-primary">{{ $post->interests_count }}</span></td>
                                <td>
                                    <span class="badge badge-{{ $post->is_active ? 'success' : 'secondary' }}">
                                        {{ $post->is_active ? 'نشط' : 'مخفي' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">لا توجد منشورات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
