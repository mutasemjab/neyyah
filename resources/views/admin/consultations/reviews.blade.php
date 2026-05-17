@extends("layouts.admin")

@section('title', 'تقييمات الجلسات')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right mr-1"></i> العودة إلى الجلسات
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star mr-2"></i>تقييمات الجلسات الاستشارية</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>المستشار</th>
                                    <th>التقييم</th>
                                    <th>التعليق</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviews as $review)
                                <tr>
                                    <td>{{ $review->user->display_name ?? '—' }}</td>
                                    <td>{{ $review->consultant->user->display_name ?? '—' }}<br>
                                        <small class="text-muted">{{ $review->consultant->title_ar ?? '' }}</small></td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:.8rem;"></i>
                                        @endfor
                                        <strong class="ml-1">{{ $review->rating }}</strong>
                                    </td>
                                    <td>{{ Str::limit($review->review_ar ?? '—', 80) }}</td>
                                    <td><small class="text-muted">{{ $review->created_at->format('Y-m-d') }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('messages.No_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $reviews->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
