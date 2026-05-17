

<?php $__env->startSection('title', __('messages.Identity_Verifications')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('messages.Home')); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo e(__('messages.Identity_Verifications')); ?></li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo e(__('messages.Identity_Verifications')); ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($statusFilter === 'pending' ? 'active' : ''); ?>"
                               href="<?php echo e(route('admin.verifications.index', ['status' => 'pending'])); ?>">
                                <?php echo e(__('messages.Pending')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($statusFilter === 'approved' ? 'active' : ''); ?>"
                               href="<?php echo e(route('admin.verifications.index', ['status' => 'approved'])); ?>">
                                <?php echo e(__('messages.Approved')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($statusFilter === 'rejected' ? 'active' : ''); ?>"
                               href="<?php echo e(route('admin.verifications.index', ['status' => 'rejected'])); ?>">
                                <?php echo e(__('messages.Rejected')); ?>

                            </a>
                        </li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.User')); ?></th>
                                    <th><?php echo e(__('messages.Phone')); ?></th>
                                    <th><?php echo e(__('messages.Document_Type')); ?></th>
                                    <th><?php echo e(__('messages.Status')); ?></th>
                                    <th><?php echo e(__('messages.Created')); ?></th>
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($v->id); ?></td>
                                    <td><?php echo e($v->user->profile->display_name ?? '—'); ?></td>
                                    <td><?php echo e($v->user->country_code); ?> <?php echo e($v->user->phone); ?></td>
                                    <td><?php echo e(__('messages.' . ($v->document_type === 'national_id' ? 'National_ID' : ucfirst($v->document_type ?? '')))); ?></td>
                                    <td>
                                        <?php if($v->status === 'approved'): ?>
                                            <span class="badge badge-success"><?php echo e(__('messages.Approved')); ?></span>
                                        <?php elseif($v->status === 'rejected'): ?>
                                            <span class="badge badge-danger"><?php echo e(__('messages.Rejected')); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?php echo e(__('messages.Pending')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small><?php echo e($v->created_at->format('Y-m-d')); ?></small></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.verifications.show', $v->id)); ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> <?php echo e(__('messages.Show')); ?>

                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <?php echo e(__('messages.No_data')); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3"><?php echo e($data->appends(request()->query())->links()); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/verifications/index.blade.php ENDPATH**/ ?>