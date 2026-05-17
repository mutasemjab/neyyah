@extends("layouts.admin")

@section('title', 'طلبات الوساطة الخاصة')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.private-requests.index') }}" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('messages.Search') }}..."
                                    value="{{ $searchQuery ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('messages.Filter_by_Status') }}</option>
                                    @foreach (['pending_payment','active','searching','candidates_sent','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ ($statusFilter ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> بحث</button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.private-requests.index') }}" class="btn btn-secondary w-100">{{ __('messages.All') }}</a>
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
                    <h3 class="card-title"><i class="fas fa-handshake mr-2"></i>طلبات الوساطة الخاصة</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>الوسيط</th>
                                    <th>الباقة</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $req)
                                <tr>
                                    <td>{{ $req->user->display_name ?? '—' }}<br>
                                        <small class="text-muted">{{ $req->user->phone ?? '' }}</small></td>
                                    <td>{{ $req->matchmaker->user->display_name ?? '—' }}</td>
                                    <td>{{ $req->package->name_ar ?? '—' }}</td>
                                    <td>{{ $req->price }} JD</td>
                                    <td>
                                        @php $sc = ['pending_payment'=>'warning','active'=>'success','searching'=>'info','candidates_sent'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp
                                        <span class="badge badge-{{ $sc[$req->status] ?? 'secondary' }}">{{ $req->status }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $req->created_at->format('Y-m-d') }}</small></td>
                                    <td>
                                        <a href="{{ route('admin.private-requests.show', $req->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">{{ __('messages.No_data') }}</td>
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
