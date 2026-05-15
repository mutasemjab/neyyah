@extends("layouts.admin")

@section('title', __('messages.Interests'))

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
                        <li class="breadcrumb-item active">{{ __('messages.Interests') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Interests') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            {{ $data->links() }}
                        </div>
                        <div class="col-sm-6 text-right">
                            @can('interests-add')
                            <a href="{{ route('admin.interests.create') }}"
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('messages.New_Interest') }}
                            </a>
                            @endcan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Icon') }}</th>
                                    <th>{{ __('messages.Name_AR') }}</th>
                                    <th>{{ __('messages.Name_EN') }}</th>
                                    <th>{{ __('messages.Is_Active') }}</th>
                                    <th>{{ __('messages.Users') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $interest)
                                <tr>
                                    <td>{{ $interest->id }}</td>
                                    <td style="font-size:1.5rem;">{{ $interest->icon ?? '—' }}</td>
                                    <td>{{ $interest->name_ar }}</td>
                                    <td>{{ $interest->name_en }}</td>
                                    <td>
                                        @if ($interest->is_active)
                                            <span class="badge badge-success">{{ __('messages.Yes') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('messages.No') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $interest->users_count }}</span>
                                    </td>
                                    <td>
                                        @can('interests-edit')
                                        <a href="{{ route('admin.interests.edit', $interest->id) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="mdi mdi-pencil-box"></i> {{ __('messages.Edit') }}
                                        </a>
                                        @endcan
                                        @can('interests-delete')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $interest->id }}, '{{ addslashes($interest->name_en) }}')">
                                            <i class="mdi mdi-trash-can"></i> {{ __('messages.Delete') }}
                                        </button>
                                        <form id="delete-form-{{ $interest->id }}"
                                              action="{{ route('admin.interests.destroy', $interest->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('messages.No_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $data->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: '{{ __("messages.Are you sure?") }}',
            text: '"' + name + '"',
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
