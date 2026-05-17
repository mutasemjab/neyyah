<?php $__env->startSection('title', 'الوسطاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.matchmakers.index')); ?>" class="mb-0">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control"
                                    placeholder="<?php echo e(__('messages.Search')); ?>..."
                                    value="<?php echo e($searchQuery ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value=""><?php echo e(__('messages.Filter_by_Status')); ?></option>
                                    <option value="pending"  <?php echo e(($statusFilter ?? '') === 'pending'  ? 'selected' : ''); ?>>قيد المراجعة</option>
                                    <option value="approved" <?php echo e(($statusFilter ?? '') === 'approved' ? 'selected' : ''); ?>>موافق عليه</option>
                                    <option value="rejected" <?php echo e(($statusFilter ?? '') === 'rejected' ? 'selected' : ''); ?>>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> <?php echo e(__('messages.Search')); ?></button>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('admin.matchmakers.index')); ?>" class="btn btn-secondary w-100"><?php echo e(__('messages.All')); ?></a>
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
                    <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>الوسطاء</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th><?php echo e(__('messages.Display_Name')); ?></th>
                                    <th><?php echo e(__('messages.Phone')); ?></th>
                                    <th>التخصصات</th>
                                    <th>الخبرة (سنوات)</th>
                                    <th>التقييم</th>
                                    <th>الحالات الناجحة</th>
                                    <th>الحالة</th>
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $matchmaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($matchmaker->user->display_name ?? '—'); ?></strong></td>
                                    <td><?php echo e($matchmaker->user->phone ?? '—'); ?></td>
                                    <td>
                                        <?php if($matchmaker->specializations): ?>
                                            <?php $__currentLoopData = array_slice($matchmaker->specializations, 0, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge badge-light border"><?php echo e($spec); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?> —
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($matchmaker->years_experience); ?></td>
                                    <td>
                                        <span class="text-warning"><i class="fas fa-star"></i></span>
                                        <?php echo e(number_format($matchmaker->rating_avg, 1)); ?>

                                        <small class="text-muted">(<?php echo e($matchmaker->ratings_count); ?>)</small>
                                    </td>
                                    <td><span class="badge badge-success"><?php echo e($matchmaker->success_cases); ?></span></td>
                                    <td>
                                        <?php
                                            $colors = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                                            $labels = ['approved' => 'موافق عليه', 'pending' => 'قيد المراجعة', 'rejected' => 'مرفوض'];
                                        ?>
                                        <span class="badge badge-<?php echo e($colors[$matchmaker->verification_status] ?? 'secondary'); ?>">
                                            <?php echo e($labels[$matchmaker->verification_status] ?? $matchmaker->verification_status); ?>

                                        </span>
                                        <?php if(!$matchmaker->is_active): ?>
                                            <span class="badge badge-secondary ml-1">معطّل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.matchmakers.show', $matchmaker->id)); ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted"><?php echo e(__('messages.No_data')); ?></td>
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

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/matchmakers/index.blade.php ENDPATH**/ ?>