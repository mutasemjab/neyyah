<?php $__env->startSection('title', 'الجلسات الاستشارية'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.consultations.index')); ?>" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="<?php echo e(__('messages.Search')); ?>..."
                                    value="<?php echo e($searchQuery ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">الحالة</option>
                                    <?php $__currentLoopData = ['pending_payment','confirmed','ongoing','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e(($statusFilter ?? '') === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="meeting_type" class="form-control">
                                    <option value="">نوع الاجتماع</option>
                                    <option value="voice"  <?php echo e(($meetingFilter ?? '') === 'voice'  ? 'selected' : ''); ?>>صوت</option>
                                    <option value="video"  <?php echo e(($meetingFilter ?? '') === 'video'  ? 'selected' : ''); ?>>فيديو</option>
                                    <option value="chat"   <?php echo e(($meetingFilter ?? '') === 'chat'   ? 'selected' : ''); ?>>دردشة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> بحث</button>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('admin.consultations.index')); ?>" class="btn btn-secondary w-100"><?php echo e(__('messages.All')); ?></a>
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
                    <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>الجلسات الاستشارية</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.consultations.reviews')); ?>" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-star mr-1"></i> التقييمات
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>المستشار</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>نوع الاجتماع</th>
                                    <th>الحالة</th>
                                    <th>السعر</th>
                                    <th><?php echo e(__('messages.Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $sc = ['pending_payment'=>'warning','confirmed'=>'success','ongoing'=>'primary','completed'=>'success','cancelled'=>'danger'];
                                ?>
                                <tr>
                                    <td><?php echo e($session->user->display_name ?? '—'); ?></td>
                                    <td><?php echo e($session->consultant->user->display_name ?? '—'); ?></td>
                                    <td><?php echo e($session->session_date->format('Y-m-d')); ?></td>
                                    <td><?php echo e($session->start_time); ?>–<?php echo e($session->end_time); ?></td>
                                    <td><span class="badge badge-info"><?php echo e($session->meeting_type); ?></span></td>
                                    <td><span class="badge badge-<?php echo e($sc[$session->status] ?? 'secondary'); ?>"><?php echo e($session->status); ?></span></td>
                                    <td><?php echo e($session->price); ?> JD</td>
                                    <td>
                                        <a href="<?php echo e(route('admin.consultations.show', $session->id)); ?>"
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

<?php echo $__env->make("layouts.admin", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\neyyah\resources\views/admin/consultations/index.blade.php ENDPATH**/ ?>