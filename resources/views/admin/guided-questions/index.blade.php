@extends("layouts.admin")

@section('title', __('messages.Guided_Questions'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Guided_Questions') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Guided_Questions') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <form method="GET" action="{{ route('admin.guided-questions.index') }}" class="d-flex">
                                <select name="category" class="form-control mr-2">
                                    <option value="">{{ __('messages.Category') }} — {{ __('messages.All') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-7 text-right">
                            @can('guided-questions-add')
                            <a href="{{ route('admin.guided-questions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('messages.New_Question') }}
                            </a>
                            @endcan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('messages.Sort_Order') }}</th>
                                    <th>{{ __('messages.Question_Text') }}</th>
                                    <th>{{ __('messages.Category') }}</th>
                                    <th>{{ __('messages.Version') }}</th>
                                    <th>{{ __('messages.Guided_Questions') }} ({{ __('messages.Users') }})</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $q)
                                <tr>
                                    <td><span class="badge badge-secondary">{{ $q->sort_order }}</span></td>
                                    <td style="max-width:320px;">
                                        <span title="{{ $q->question_text }}">
                                            {{ Str::limit($q->question_text, 80) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($q->category)
                                            <span class="badge badge-light border">{{ $q->category }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>v{{ $q->version }}</td>
                                    <td><span class="badge badge-info">{{ $q->answers_count }}</span></td>
                                    <td>
                                        @can('guided-questions-edit')
                                        <a href="{{ route('admin.guided-questions.edit', $q->id) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="mdi mdi-pencil-box"></i> {{ __('messages.Edit') }}
                                        </a>
                                        @endcan
                                        @can('guided-questions-delete')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $q->id }})">
                                            <i class="mdi mdi-trash-can"></i> {{ __('messages.Delete') }}
                                        </button>
                                        <form id="delete-form-{{ $q->id }}"
                                              action="{{ route('admin.guided-questions.destroy', $q->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('messages.No_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $data->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '{{ __("messages.Are you sure?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.Yes, delete it!") }}',
            cancelButtonText: '{{ __("messages.Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
