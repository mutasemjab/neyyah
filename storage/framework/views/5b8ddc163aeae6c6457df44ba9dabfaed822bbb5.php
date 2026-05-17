<?php $__env->startSection('title', 'طلبات الوساطة الخاصة'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.private-requests.index')); ?>" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="<?php echo e(__('messages.Search')); ?>..."
                                    value="<?php echo e($searchQuery ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value=""><?php echo e(__('messages.Filter_by_Status')); ?></option>
                                    <?php $__currentLoopData = ['pending_payment','active','searching','candidates_sent','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e(($statusFilter ?? '') === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> بحث</button>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo e(route('admin.private-requests.index')); ?>" class="btn btn-secondary w-100"><?php echo e(__('messages.All')); ?></a>
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
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($req->user->display_name ?? '—'); ?><br>
                                        <small class="text-muted"><?php echo e($req->user->phone ?? ''); ?></small></td>
                                    <td><?php echo e($req->matchmaker->user->display_name ?? '—'); ?></td>
                                    <td><?php echo e($req->package->name_ar ?? '—'); ?></td>
                                    <td><?php echo e($req->price); ?> JD</td>
                                    <td>
                                        <?php $sc = ['pending_payment'=>'warning','active'=>'success','searching'=>'info','candidates_sent'=>'primary','completed'=>'success','cancelled'=>'danger']; ?>
                                        <span class="badge badge-<?php echo e($sc[$req->status] ?? 'secondary'); ?>"><?php echo e($req->status); ?></span>
                                    </td>
                                    <td><small class="text-muted"><?php echo e($req->created_at->format('Y-m-d')); ?></small></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.private-requests.show', $req->id)); ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted"><?php echo e(__('messages.No_data')); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3"><?php echo e($data->appends(request()->query())->links()); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/private-requests/index.blade.php ENDPATH**/ ?>