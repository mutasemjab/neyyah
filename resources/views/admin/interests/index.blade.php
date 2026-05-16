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
                    <p class="text-muted mb-3">{{ __('messages.Interests_Info') }}</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('messages.Label') }}</th>
                                    <th>{{ __('messages.Users') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $interest)
                                <tr>
                                    <td><strong>{{ $interest->label }}</strong></td>
                                    <td>
                                        <span class="badge badge-info">{{ $interest->users_count }}</span>
                                    </td>
                                    <td>
                                        @can('interests-delete')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete('{{ addslashes($interest->label) }}')">
                                            <i class="mdi mdi-trash-can"></i> {{ __('messages.Delete') }}
                                        </button>
                                        <form id="delete-form-{{ $loop->index }}"
                                              action="{{ route('admin.interests.destroy', urlencode($interest->label)) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
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
    function confirmDelete(label) {
        Swal.fire({
            title: '{{ __("messages.Are you sure?") }}',
            text: '"' + label + '"',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.Yes, delete it!") }}',
            cancelButtonText: '{{ __("messages.Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                // find the form by label match and submit
                document.querySelectorAll('form[id^="delete-form-"]').forEach(function(form) {
                    if (form.action.includes(encodeURIComponent(label))) {
                        form.submit();
                    }
                });
            }
        });
    }
</script>
@endsection
