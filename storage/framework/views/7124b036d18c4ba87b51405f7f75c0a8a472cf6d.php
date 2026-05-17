<?php $__env->startSection('title', __('messages.Notifications')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane mr-2"></i><?php echo e(__('messages.Send_Notification')); ?>

                    </h3>
                </div>
                <div class="card-body">
                    <form id="notifForm" method="POST" action="<?php echo e(route('admin.notifications.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label><?php echo e(__('messages.Notification_Title')); ?></label>
                                    <input type="text" name="title_ar"
                                        class="form-control <?php $__errorArgs = ['title_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('title_ar')); ?>" required>
                                    <?php $__errorArgs = ['title_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(__('messages.Target')); ?></label>
                                    <div class="mt-2">
                                        <div class="custom-control custom-radio mb-1">
                                            <input type="radio" id="targetAll" name="target" value="all"
                                                class="custom-control-input"
                                                <?php echo e(old('target', 'all') === 'all' ? 'checked' : ''); ?>>
                                            <label class="custom-control-label" for="targetAll">
                                                <i class="fas fa-users mr-1"></i><?php echo e(__('messages.All_Users')); ?>

                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="targetSpecific" name="target" value="specific"
                                                class="custom-control-input"
                                                <?php echo e(old('target') === 'specific' ? 'checked' : ''); ?>>
                                            <label class="custom-control-label" for="targetSpecific">
                                                <i class="fas fa-user-check mr-1"></i><?php echo e(__('messages.Specific_Users')); ?>

                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo e(__('messages.Notification_Body')); ?></label>
                            <textarea name="body_ar" rows="3"
                                class="form-control <?php $__errorArgs = ['body_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                required><?php echo e(old('body_ar')); ?></textarea>
                            <?php $__errorArgs = ['body_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div id="userSelectionSection" style="<?php echo e(old('target') === 'specific' ? '' : 'display:none;'); ?>">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-user-check mr-1"></i><?php echo e(__('messages.Select_Users')); ?>

                                </h6>
                                <span class="badge badge-primary px-3 py-2" id="selectedCount">
                                    0 <?php echo e(__('messages.Selected')); ?>

                                </span>
                            </div>

                            <?php $__errorArgs = ['user_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="alert alert-danger py-2"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            
                            <div id="selectedChips" class="mb-3" style="min-height: 32px;"></div>

                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="userSearchInput" class="form-control"
                                    placeholder="<?php echo e(__('messages.Search')); ?> <?php echo e(__('messages.Display_Name')); ?> / <?php echo e(__('messages.Phone')); ?>...">
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
                                                <input type="checkbox" id="selectAllVisible" title="<?php echo e(__('messages.Select_All_Visible')); ?>">
                                            </th>
                                            <th><?php echo e(__('messages.Display_Name')); ?></th>
                                            <th><?php echo e(__('messages.Phone')); ?></th>
                                            <th><?php echo e(__('messages.Gender')); ?></th>
                                            <th><?php echo e(__('messages.Status')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <tr id="loadingRow">
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                <?php echo e(__('messages.Loading')); ?>...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            
                            <div id="selectedUsersInputs"></div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane mr-1"></i>
                                <?php echo e(__('messages.Send_Notification')); ?>

                            </button>
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
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i><?php echo e(__('messages.Notification_History')); ?>

                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th><?php echo e(__('messages.Notification_Title')); ?></th>
                                    <th><?php echo e(__('messages.Notification_Body')); ?></th>
                                    <th><?php echo e(__('messages.Target')); ?></th>
                                    <th><?php echo e(__('messages.Users_Count')); ?></th>
                                    <th><?php echo e(__('messages.Created')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($broadcast->title_ar); ?></strong></td>
                                    <td class="text-muted"><?php echo e(Str::limit($broadcast->body_ar, 70)); ?></td>
                                    <td>
                                        <?php if($broadcast->target === 'all'): ?>
                                            <span class="badge badge-primary"><?php echo e(__('messages.All_Users')); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo e(__('messages.Specific_Users')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border"><?php echo e(number_format($broadcast->user_count)); ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($broadcast->created_at->format('Y-m-d H:i')); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <?php echo e(__('messages.No_data')); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($broadcasts->hasPages()): ?>
                    <div class="p-3">
                        <?php echo e($broadcasts->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
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
            '<i class="fas fa-spinner fa-spin mr-1"></i><?php echo e(__("messages.Loading")); ?>...</td></tr>';

        var spinner = document.getElementById('searchSpinner');
        spinner.style.display = '';

        fetch('<?php echo e(route("admin.notifications.search-users")); ?>?q=' + encodeURIComponent(q), {
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
                '<tr><td colspan="5" class="text-center py-3 text-danger"><?php echo e(__("messages.Something went wrong")); ?></td></tr>';
        });
    }

    // ── Render table rows ────────────────────────────────────────────────────
    function renderTable(users) {
        var tbody = document.getElementById('usersTableBody');

        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><?php echo e(__("messages.No_data")); ?></td></tr>';
            return;
        }

        var statusColors = { active: 'success', pending: 'warning', suspended: 'secondary', banned: 'danger' };
        var genderLabels = { male: '<span class="badge badge-info"><?php echo e(__("messages.Male")); ?></span>',
                             female: '<span class="badge badge-danger"><?php echo e(__("messages.Female")); ?></span>' };

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

        document.getElementById('selectedCount').textContent = count + ' <?php echo e(__("messages.Selected")); ?>';

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>