@extends("layouts.admin")

@section('title', __('messages.Employees'))


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
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ env('APP_NAME') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('messages.Employees') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('messages.Employees') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                {{ $data->links() }}
                            </div>
                            <div class="col-sm-8">
                                <div class="text-sm-right">
                                    @can('employee-add')
                                    <a type="button" href="{{ route('admin.employee.create') }}"
                                        class="btn btn-primary waves-effect waves-light mb-2 text-white">
                                        {{ __('messages.New Employee') }}
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('messages.Name') }}</th>
                                        <th>{{ __('messages.Email') }}</th>
                                        <th>{{ __('messages.Username') }}</th>
                                        <th>{{ __('messages.Roles') }}</th>
                                        <th>{{ __('messages.Created') }}</th>
                                        <th style="width: 82px;">{{ __('messages.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="font-weight-bold">{{ $employee->name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $employee->email }}
                                            </td>
                                            <td>
                                                {{ $employee->username ?: __('messages.Not set') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-success">{{ $employee->roles->count() }}</span>
                                                @if($employee->roles->count() > 0)
                                                <button class="btn btn-link btn-sm p-0 ml-1" 
                                                        type="button" 
                                                        data-toggle="collapse" 
                                                        data-target="#roles{{ $employee->id }}" 
                                                        aria-expanded="false">
                                                    <small>{{ __('messages.View') }}</small>
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $employee->created_at->format('M d, Y') }}
                                                    <br>
                                                    {{ $employee->created_at->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td>
                                                @can('employee-edit')
                                                <a class="btn btn-sm btn-outline-info"
                                                    href="{{ route('admin.employee.edit', $employee->id) }}">
                                                    <i class="mdi mdi-pencil-box"></i> {{ __('messages.Edit') }}
                                                </a>
                                                @endcan
                                                
                                                @can('employee-delete')
                                                <a class="btn btn-sm btn-outline-danger" 
                                                   href="javascript:void(0)"
                                                   @if (env('Environment') == 'sendbox') 
                                                       onclick="myFunction()" 
                                                   @else 
                                                       onclick="confirmDelete({{ $employee->id }}, '{{ $employee->name }}')"
                                                   @endif>
                                                    <i class="mdi mdi-trash-can"></i> {{ __('messages.Delete') }}
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                        @if($employee->roles->count() > 0)
                                        <tr>
                                            <td colspan="6" class="p-0">
                                                <div class="collapse" id="roles{{ $employee->id }}">
                                                    <div class="card card-body m-2 bg-light">
                                                        <small class="text-muted mb-2">{{ __('messages.Assigned Roles') }}:</small>
                                                        @foreach ($employee->roles as $role)
                                                            <span class="badge badge-primary mr-1 mb-1">{{ $role->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
    
    <script>
        function confirmDelete(employeeId, employeeName) {
            Swal.fire({
                title: '{{ __("messages.Are you sure?") }}',
                text: '{{ __("messages.You want to delete the employee") }} "' + employeeName + '"',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("messages.Yes, delete it!") }}',
                cancelButtonText: '{{ __("messages.Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make AJAX request
                    $.ajax({
                        url: '{{ route("admin.employee.destroy", ":id") }}'.replace(':id', employeeId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.Deleted!") }}',
                                text: '{{ __("messages.Employee has been deleted successfully") }}',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("messages.Error!") }}',
                                text: '{{ __("messages.Something went wrong") }}'
                            });
                        }
                    });
                }
            });
        }

        function myFunction() {
            Swal.fire({
                icon: 'info',
                title: '{{ __("messages.Demo Mode") }}',
                text: '{{ __("messages.This action is disabled in demo environment") }}'
            });
        }
    </script>
@endsection