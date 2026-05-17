@extends("layouts.admin")

@section('title', __('messages.Notifications'))

@section('content')
<div class="container-fluid">

    {{-- Send Notification Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane mr-2"></i>{{ __('messages.Send_Notification') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form id="notifForm" method="POST" action="{{ route('admin.notifications.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>{{ __('messages.Notification_Title') }}</label>
                                    <input type="text" name="title_ar"
                                        class="form-control @error('title_ar') is-invalid @enderror"
                                        value="{{ old('title_ar') }}" required>
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('messages.Target') }}</label>
                                    <div class="mt-2">
                                        <div class="custom-control custom-radio mb-1">
                                            <input type="radio" id="targetAll" name="target" value="all"
                                                class="custom-control-input"
                                                {{ old('target', 'all') === 'all' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="targetAll">
                                                <i class="fas fa-users mr-1"></i>{{ __('messages.All_Users') }}
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="targetSpecific" name="target" value="specific"
                                                class="custom-control-input"
                                                {{ old('target') === 'specific' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="targetSpecific">
                                                <i class="fas fa-user-check mr-1"></i>{{ __('messages.Specific_Users') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Notification_Body') }}</label>
                            <textarea name="body_ar" rows="3"
                                class="form-control @error('body_ar') is-invalid @enderror"
                                required>{{ old('body_ar') }}</textarea>
                            @error('body_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- User Selection Section --}}
                        <div id="userSelectionSection" style="{{ old('target') === 'specific' ? '' : 'display:none;' }}">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-user-check mr-1"></i>{{ __('messages.Select_Users') }}
                                </h6>
                                <span class="badge badge-primary px-3 py-2" id="selectedCount">
                                    0 {{ __('messages.Selected') }}
                                </span>
                            </div>

                            @error('user_ids')
                                <div class="alert alert-danger py-2">{{ $message }}</div>
                            @enderror

                            {{-- Selected users chips --}}
                            <div id="selectedChips" class="mb-3" style="min-height: 32px;"></div>

                            {{-- Search --}}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="userSearchInput" class="form-control"
                                    placeholder="{{ __('messages.Search') }} {{ __('messages.Display_Name') }} / {{ __('messages.Phone') }}...">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="searchSpinner" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height:380px; overflow-y:auto;">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAllVisible" title="{{ __('messages.Select_All_Visible') }}">
                                            </th>
                                            <th>{{ __('messages.Display_Name') }}</th>
                                            <th>{{ __('messages.Phone') }}</th>
                                            <th>{{ __('messages.Gender') }}</th>
                                            <th>{{ __('messages.Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <tr id="loadingRow">
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                {{ __('messages.Loading') }}...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Hidden inputs injected by JS --}}
                            <div id="selectedUsersInputs"></div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane mr-1"></i>
                                {{ __('messages.Send_Notification') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- History Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>{{ __('messages.Notification_History') }}
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('messages.Notification_Title') }}</th>
                                    <th>{{ __('messages.Notification_Body') }}</th>
                                    <th>{{ __('messages.Target') }}</th>
                                    <th>{{ __('messages.Users_Count') }}</th>
                                    <th>{{ __('messages.Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($broadcasts as $broadcast)
                                <tr>
                                    <td><strong>{{ $broadcast->title_ar }}</strong></td>
                                    <td class="text-muted">{{ Str::limit($broadcast->body_ar, 70) }}</td>
                                    <td>
                                        @if ($broadcast->target === 'all')
                                            <span class="badge badge-primary">{{ __('messages.All_Users') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('messages.Specific_Users') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">{{ number_format($broadcast->user_count) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $broadcast->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        {{ __('messages.No_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($broadcasts->hasPages())
                    <div class="p-3">
                        {{ $broadcasts->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
(function () {
    // ── State ────────────────────────────────────────────────────────────────
    var selectedUsers = {};   // { id: { id, display_name, phone } }
    var searchTimer   = null;

    // ── Toggle user-selection panel ──────────────────────────────────────────
    document.querySelectorAll('input[name="target"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var section = document.getElementById('userSelectionSection');
            section.style.display = this.value === 'specific' ? '' : 'none';
            if (this.value === 'specific' && Object.keys(selectedUsers).length === 0) {
                fetchUsers('');
            }
        });
    });

    // ── Fetch users via AJAX ─────────────────────────────────────────────────
    function fetchUsers(q) {
        document.getElementById('loadingRow').style.display = '';
        document.getElementById('usersTableBody').innerHTML =
            '<tr id="loadingRow"><td colspan="5" class="text-center py-4 text-muted">' +
            '<i class="fas fa-spinner fa-spin mr-1"></i>{{ __("messages.Loading") }}...</td></tr>';

        var spinner = document.getElementById('searchSpinner');
        spinner.style.display = '';

        fetch('{{ route("admin.notifications.search-users") }}?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (users) {
            spinner.style.display = 'none';
            renderTable(users);
        })
        .catch(function () {
            spinner.style.display = 'none';
            document.getElementById('usersTableBody').innerHTML =
                '<tr><td colspan="5" class="text-center py-3 text-danger">{{ __("messages.Something went wrong") }}</td></tr>';
        });
    }

    // ── Render table rows ────────────────────────────────────────────────────
    function renderTable(users) {
        var tbody = document.getElementById('usersTableBody');

        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">{{ __("messages.No_data") }}</td></tr>';
            return;
        }

        var statusColors = { active: 'success', pending: 'warning', suspended: 'secondary', banned: 'danger' };
        var genderLabels = { male: '<span class="badge badge-info">{{ __("messages.Male") }}</span>',
                             female: '<span class="badge badge-danger">{{ __("messages.Female") }}</span>' };

        var html = '';
        users.forEach(function (u) {
            var checked  = selectedUsers[u.id] ? 'checked' : '';
            var color    = statusColors[u.status] || 'secondary';
            var gender   = genderLabels[u.gender] || '—';
            var status   = u.status ? u.status.charAt(0).toUpperCase() + u.status.slice(1) : '';
            html +=
                '<tr>' +
                '<td><input type="checkbox" class="user-checkbox" data-id="' + u.id + '" ' +
                    'data-name="' + escHtml(u.display_name || '') + '" data-phone="' + escHtml(u.phone || '') + '" ' + checked + '></td>' +
                '<td>' + escHtml(u.display_name || '—') + '</td>' +
                '<td>' + escHtml(u.phone || '—') + '</td>' +
                '<td>' + gender + '</td>' +
                '<td><span class="badge badge-' + color + '">' + escHtml(status) + '</span></td>' +
                '</tr>';
        });
        tbody.innerHTML = html;

        document.getElementById('selectAllVisible').checked = false;
    }

    // ── Checkbox delegation ──────────────────────────────────────────────────
    document.getElementById('usersTableBody').addEventListener('change', function (e) {
        var cb = e.target;
        if (!cb.classList.contains('user-checkbox')) return;

        if (cb.checked) {
            selectedUsers[cb.dataset.id] = { id: cb.dataset.id, display_name: cb.dataset.name, phone: cb.dataset.phone };
        } else {
            delete selectedUsers[cb.dataset.id];
        }
        refresh();
    });

    // ── Select-all visible ───────────────────────────────────────────────────
    document.getElementById('selectAllVisible').addEventListener('change', function () {
        var checked = this.checked;
        document.querySelectorAll('#usersTableBody .user-checkbox').forEach(function (cb) {
            cb.checked = checked;
            if (checked) {
                selectedUsers[cb.dataset.id] = { id: cb.dataset.id, display_name: cb.dataset.name, phone: cb.dataset.phone };
            } else {
                delete selectedUsers[cb.dataset.id];
            }
        });
        refresh();
    });

    // ── Search with debounce ─────────────────────────────────────────────────
    document.getElementById('userSearchInput').addEventListener('input', function () {
        var q = this.value;
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { fetchUsers(q); }, 350);
    });

    // ── Remove chip ──────────────────────────────────────────────────────────
    document.getElementById('selectedChips').addEventListener('click', function (e) {
        var btn = e.target.closest('[data-remove]');
        if (!btn) return;
        var id = btn.dataset.remove;
        delete selectedUsers[id];
        // uncheck in table if visible
        var cb = document.querySelector('.user-checkbox[data-id="' + id + '"]');
        if (cb) cb.checked = false;
        refresh();
    });

    // ── Refresh count, chips, hidden inputs ──────────────────────────────────
    function refresh() {
        var ids    = Object.keys(selectedUsers);
        var count  = ids.length;

        document.getElementById('selectedCount').textContent = count + ' {{ __("messages.Selected") }}';

        // chips
        var chipsHtml = '';
        ids.forEach(function (id) {
            var u = selectedUsers[id];
            chipsHtml +=
                '<span class="badge badge-info mr-1 mb-1 p-2" style="font-size:.8rem;">' +
                escHtml(u.display_name || u.phone || id) +
                ' <a href="#" data-remove="' + id + '" class="text-white ml-1" style="text-decoration:none;">&times;</a>' +
                '</span>';
        });
        document.getElementById('selectedChips').innerHTML = chipsHtml;

        // hidden inputs
        var container = document.getElementById('selectedUsersInputs');
        container.innerHTML = '';
        ids.forEach(function (id) {
            var inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'user_ids[]';
            inp.value = id;
            container.appendChild(inp);
        });
    }

    // ── HTML escape helper ───────────────────────────────────────────────────
    function escHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Auto-load if "specific" was pre-selected (after validation fail) ─────
    if (document.getElementById('targetSpecific').checked) {
        fetchUsers('');
    }
})();
</script>
@endsection
