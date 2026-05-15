@extends('layouts.admin')

@section('title', __('messages.Roles'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('messages.Roles') }}</h1>
        <a href="{{ route('admin.role.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.create') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($data->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('messages.number') }}</th>
                            <th>{{ __('messages.role_name') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $value)
                            <tr>
                                <td>{{ $value->id }}</td>
                                <td><span class="font-weight-bold">{{ $value->name }}</span></td>
                                <td>
                                    <a href="{{ route('admin.role.edit', $value->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                    </a>

                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $value->id }})">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>

                                    <form id="delete-form-{{ $value->id }}" action="{{ route('admin.role.delete', $value->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-4">
                    <p class="text-muted">{{ __('messages.no_roles_found') }}</p>
                    <a href="{{ route('admin.role.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.create') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: @json(__('messages.delete_confirm')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: @json(__('messages.yes_delete')),
                cancelButtonText: @json(__('messages.cancel')),
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection