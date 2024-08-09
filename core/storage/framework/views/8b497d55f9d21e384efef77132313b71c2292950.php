<?php $__env->startSection('content'); ?>
    <!-- page banner start -->
    <section class="page-banner">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h2 class="title text-white"><?php echo e(__($pageTitle)); ?></h2>
                    <ul class="page-breadcrumb justify-content-center mt-2">
                        <li><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
                        <li><?php echo e(__($pageTitle)); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- page banner end -->

    <?php if($page->sections != null): ?>
        <?php $__currentLoopData = $page->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sections): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make(template().'sections.' . $sections, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>



<?php $__env->stopSection(); ?>

<?php echo $__env->make(template() . 'layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/theme5/pages.blade.php ENDPATH**/ ?>