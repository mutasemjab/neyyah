@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.Roles'))

@section('css')
<style>
    .permissions-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .permission-group {
        background-color: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .group-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .group-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        text-transform: capitalize;
    }

    .group-select-all {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .group-select-all input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .permission-item {
        display: flex;
        align-items: center;
        padding: 10px;
        margin-bottom: 8px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        transition: all 0.2s ease;
    }

    .permission-item:hover {
        background-color: #e7f3ff;
        border-color: #0d6efd;
    }

    .permission-item.checked {
        background-color: #e7f3ff;
        border-color: #0d6efd;
    }

    .permission-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-left: 10px;
        cursor: pointer;
    }

    .permission-item label {
        margin-bottom: 0;
        cursor: pointer;
        flex-grow: 1;
        font-weight: 500;
    }

    .select-all-box {
        background-color: #0d6efd;
        color: white;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .select-all-box input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-left: 10px;
        margin-right: 0;
        cursor: pointer;
        flex-shrink: 0;
    }

    .select-all-box label {
        margin-bottom: 0;
        margin-right: 10px;
        cursor: pointer;
        font-weight: 600;
        color: white;
        flex-grow: 1;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('messages.edit') }} {{ __('messages.Roles') }}</h1>
    <a href="{{ route('admin.role.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.role.update', $role->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label for="name" class="form-label">{{ __('messages.role_name') }}</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       placeholder="{{ __('messages.role_name') }}"
                       value="{{ old('name', $role->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="permissions-box">
                <h5 class="mb-3">{{ __('messages.select_permissions') }}</h5>

                <!-- Select All -->
                <div class="select-all-box">
                    <label for="select_all">{{ __('messages.select_all_permissions') }}</label>
                    <input type="checkbox" id="select_all">
                </div>

                @error('perms')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Permissions Groups -->
                <div class="row">
                    @foreach($groupedPermissions as $groupName => $permissions)
                        <div class="col-md-6 col-lg-4">
                            <div class="permission-group">
                                <div class="group-header">
                                    <h6 class="group-title">{{ ucfirst(str_replace('-', ' ', $groupName)) }}</h6>
                                    <div class="group-select-all">
                                        <label for="group_{{ $groupName }}">{{ __('messages.select_all') }}</label>
                                        <input type="checkbox" class="group-checkbox" id="group_{{ $groupName }}" data-group="{{ $groupName }}">
                                    </div>
                                </div>

                                <div class="permissions-list">
                                    @foreach($permissions as $permission)
                                        <div class="permission-item {{ in_array($permission->id, $role_permissions) ? 'checked' : '' }}">
                                            <input
                                                type="checkbox"
                                                class="permission-checkbox group-{{ $groupName }}"
                                                name="perms[]"
                                                id="perm_{{ $permission->id }}"
                                                value="{{ $permission->id }}"
                                                {{ in_array($permission->id, $role_permissions) ? 'checked' : '' }}>
                                            <label for="perm_{{ $permission->id }}">
                                                @php
                                                    $parts = explode('-', $permission->name);
                                                    $action = end($parts); // Get last part (index, create, edit, delete)
                                                @endphp
                                                {{ ucfirst($action) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; justify-content: end; gap: 10px">
                <a href="{{ route('admin.role.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> {{ __('messages.update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    // Select All functionality (global)
    document.getElementById('select_all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const groupCheckboxes = document.querySelectorAll('.group-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            updateItemStyle(checkbox);
        });
        groupCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Group Select All functionality
    document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
        groupCheckbox.addEventListener('change', function() {
            const group = this.getAttribute('data-group');
            const groupPermissions = document.querySelectorAll('.group-' + group);
            groupPermissions.forEach(checkbox => {
                checkbox.checked = this.checked;
                updateItemStyle(checkbox);
            });
            updateGlobalSelectAll();
        });
    });

    // Update Select All when individual checkboxes change
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateItemStyle(this);

            // Update group checkbox
            const classList = Array.from(this.classList);
            const groupClass = classList.find(cls => cls.startsWith('group-'));
            if (groupClass) {
                const group = groupClass.replace('group-', '');
                const groupPermissions = document.querySelectorAll('.group-' + group);
                const checkedGroupPermissions = document.querySelectorAll('.group-' + group + ':checked');
                const groupCheckbox = document.getElementById('group_' + group);
                groupCheckbox.checked = groupPermissions.length === checkedGroupPermissions.length;
            }

            updateGlobalSelectAll();
        });
    });

    // Function to update permission item style
    function updateItemStyle(checkbox) {
        const permissionItem = checkbox.closest('.permission-item');
        if (checkbox.checked) {
            permissionItem.classList.add('checked');
        } else {
            permissionItem.classList.remove('checked');
        }
    }

    function updateGlobalSelectAll() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
        document.getElementById('select_all').checked = allCheckboxes.length === checkedCheckboxes.length;
    }

    // Check if all are selected on page load
    window.addEventListener('DOMContentLoaded', function() {
        // Check each group
        document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
            const group = groupCheckbox.getAttribute('data-group');
            const groupPermissions = document.querySelectorAll('.group-' + group);
            const checkedGroupPermissions = document.querySelectorAll('.group-' + group + ':checked');
            groupCheckbox.checked = groupPermissions.length === checkedGroupPermissions.length;
        });

        // Check global
        updateGlobalSelectAll();
    });
</script>
@endsection