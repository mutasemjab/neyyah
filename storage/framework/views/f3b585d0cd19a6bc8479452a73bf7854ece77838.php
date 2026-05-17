

<?php $__env->startSection('title', __('messages.Users')); ?>

<?php $__env->startSection('css'); ?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('messages.Home')); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo e(__('messages.Users')); ?></li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo e(__('messages.Users')); ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    
                    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control"
                                    placeholder="<?php echo e(__('messages.Search')); ?>..."
                                    value="<?php echo e($searchQuery ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value=""><?php echo e(__('messages.Filter_by_Status')); ?></option>
                                    <option value="pending"   <?php echo e(($statusFilter ?? '') === 'pending'   ? 'selected' : ''); ?>><?php echo e(__('messages.Pending')); ?></option>
                                    <option value="active"    <?php echo e(($statusFilter ?? '') === 'active'    ? 'selected' : ''); ?>><?php echo e(__('messages.Active')); ?></option>
                                    <option value="suspended" <?php echo e(($statusFilter ?? '') === 'suspended' ? 'selected' : ''); ?>><?php echo e(__('messages.Suspended')); ?></option>
                                    <option value="banned"    <?php echo e(($statusFilter ?? '') === 'banned'    ? 'selected' : ''); ?>><?php echo e(__('messages.Banned')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> <?php echo e(__('messages.Search')); ?>

                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary w-100">
                                    <?php echo e(__('messages.All')); ?>

                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.Display_Name')); ?></th>
                                    <th><?php echo e(__('messages.Phone')); ?></th>
                                    <th><?php echo e(__('messages.Gender')); ?></th>
                                    <th><?php echo e(__('messages.Status')); ?></th>
                                    <th><?php echo e(__('messages.Is_Verified')); ?></th>
                                    <th><?php echo e(__('messages.Completion_Pct')); ?></th>
                                    <th><?php echo e(__('messages.Joined')); ?></th>
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td>
                                        <strong><?php echo e($user->display_name ?? '—'); ?></strong>
                                    </td>
                                    <td><?php echo e($user->phone); ?></td>
                                    <td>
                                        <?php if($user->gender === 'male'): ?>
                                            <span class="badge badge-info"><?php echo e(__('messages.Male')); ?></span>
                                        <?php elseif($user->gender === 'female'): ?>
                                            <span class="badge badge-danger"><?php echo e(__('messages.Female')); ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $statusColors = [
                                                'active'    => 'success',
                                                'pending'   => 'warning',
                                                'suspended' => 'secondary',
                                                'banned'    => 'danger',
                                                'deleted'   => 'dark',
                                            ];
                                            $color = $statusColors[$user->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?php echo e($color); ?>">
                                            <?php echo e(__('messages.' . ucfirst($user->status))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($user->is_verified): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="progress" style="height:6px; min-width:80px;">
                                            <div class="progress-bar bg-success"
                                                 style="width:<?php echo e(round($user->completion_pct * 100)); ?>%">
                                            </div>
                                        </div>
                                        <small><?php echo e(round($user->completion_pct * 100)); ?>%</small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($user->created_at->format('Y-m-d')); ?></small>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.users.show', $user->id)); ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> <?php echo e(__('messages.Show')); ?>

                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <?php echo e(__('messages.No_data')); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($data->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/users/index.blade.php ENDPATH**/ ?>