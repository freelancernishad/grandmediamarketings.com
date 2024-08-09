<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?php echo e(__('User Designations')); ?></h1>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-12 col-12 all-users-table">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('User Designation List')); ?></h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('User ID')); ?></th>
                                            <th><?php echo e(__('User Name')); ?></th>
                                            <th><?php echo e(__('Designation')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $userDesignations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userDesignation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($userDesignation->user->id); ?></td>
                                                <td><?php echo e($userDesignation->user->fullName); ?></td>
                                                <td><?php echo e($userDesignation->designation->name); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\divine_beta_trade\core\resources\views/backend/user-designations/index.blade.php ENDPATH**/ ?>