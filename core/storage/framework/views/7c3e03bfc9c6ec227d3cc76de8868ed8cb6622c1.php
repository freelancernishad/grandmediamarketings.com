


<?php $__env->startSection('content'); ?>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?php echo e(__($pageTitle)); ?></h1>
            </div>


            <div class="row">
                <div class="col-md-12">

                    
                    
                    <div class="card">
                  
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>

                                            <th><?php echo e(__('SL')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>                                  

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $__empty_1 = true; $__currentLoopData = $subscribers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscribe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($subscribe->email); ?></td>
                                            
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>


                                            <tr>

                                                <td class="text-center" colspan="100%">
                                                    <?php echo e(__('No Data Found')); ?>

                                                </td>

                                            </tr>



                                        <?php endif; ?>



                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if($subscribers->hasPages()): ?>
                            <div class="card-footer">

                                <?php echo e($subscribers->links('backend.partial.paginate')); ?>


                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/backend/subscriber.blade.php ENDPATH**/ ?>