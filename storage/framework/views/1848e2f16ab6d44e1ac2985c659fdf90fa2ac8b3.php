

<?php $__env->startSection('title', __('messages.Match_Requests')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('messages.Home')); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo e(__('messages.Match_Requests')); ?></li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo e(__('messages.Match_Requests')); ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    
                    <ul class="nav nav-tabs mb-3">
                        <?php $__currentLoopData = ['', 'pending', 'accepted', 'declined']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($statusFilter === $s ? 'active' : ''); ?>"
                               href="<?php echo e(route('admin.match-requests.index', $s ? ['status' => $s] : [])); ?>">
                                <?php echo e($s ? __('messages.' . ucfirst($s)) : __('messages.All')); ?>

                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.From_User')); ?></th>
                                    <th><?php echo e(__('messages.To_User')); ?></th>
                                    <th><?php echo e(__('messages.Request_Status')); ?></th>
                                    <th><?php echo e(__('messages.Responded_At')); ?></th>
                                    <th><?php echo e(__('messages.Sent_At')); ?></th>
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($req->id); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.users.show', $req->from_user_id)); ?>">
                                            <?php echo e($req->fromUser->display_name ?? $req->fromUser->phone); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.users.show', $req->to_user_id)); ?>">
                                            <?php echo e($req->toUser->display_name ?? $req->toUser->phone); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                            $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','expired'=>'secondary','cancelled'=>'dark'];
                                            $c = $colors[$req->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?php echo e($c); ?>">
                                            <?php echo e(__('messages.' . ucfirst($req->status))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo e($req->responded_at?->format('Y-m-d') ?? '—'); ?>

                                        </small>
                                    </td>
                                    <td><small><?php echo e($req->sent_at?->format('Y-m-d') ?? '—'); ?></small></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.match-requests.show', $req->id)); ?>"
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

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/match-requests/index.blade.php ENDPATH**/ ?>