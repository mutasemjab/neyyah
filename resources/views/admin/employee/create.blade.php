@extends("layouts.admin")

@section('title', __('messages.Create Employee'))

@section('css')
<link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.role-group {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.role-label {
    font-weight: 500;
    color: #495057;
    cursor: pointer;
}

</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.employee.index') }}">{{ __('messages.Employees') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.Create') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Create Employee') }}</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('messages.Employee Information') }}</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('messages.Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.Enter full name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">{{ __('messages.Username') }}</label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror"
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           placeholder="{{ __('messages.Enter username') }}">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('messages.Optional - leave blank to use email as username') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('messages.Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="{{ __('messages.Enter email address') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('messages.Password') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password" 
                                               name="password" 
                                               placeholder="{{ __('messages.Enter password') }}"
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">{{ __('messages.Minimum 6 characters') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>{{ __('messages.Assign Roles') }} <span class="text-danger">*</span></h5>
                            <p class="text-muted">{{ __('messages.Select one or more roles for this employee') }}</p>
                            
                            @error('roles')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="role-group">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select-all-roles">
                                        <label class="form-check-label fw-bold" for="select-all-roles">
                                            {{ __('messages.Select All Roles') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    @forelse($roles as $role)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="role-item">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input role-checkbox" 
                                                           name="roles[]" 
                                                           id="role-{{ $role->id }}" 
                                                           value="{{ $role->id }}"
                                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label role-label" for="role-{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                {{ __('messages.No roles available') }}. 
                                                <a href="{{ route('admin.role.create') }}">{{ __('messages.Create a role first') }}</a>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.employee.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> {{ __('messages.Save Employee') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Select all roles functionality
    $('#select-all-roles').change(function() {
        $('.role-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Individual role change
    $('.role-checkbox').change(function() {
        updateSelectAllStatus();
    });

    function updateSelectAllStatus() {
        const totalRoles = $('.role-checkbox').length;
        const checkedRoles = $('.role-checkbox:checked').length;
        
        $('#select-all-roles').prop('checked', totalRoles === checkedRoles);
    }

    // Form validation
    $('form').on('submit', function(e) {
        const checkedRoles = $('.role-checkbox:checked').length;
        
        if (checkedRoles === 0) {
            e.preventDefault();
            alert('{{ __("messages.Please select at least one role") }}');
            return false;
        }
    });
});
</script>
@endsection