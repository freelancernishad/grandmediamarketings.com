<?php $__env->startSection('content'); ?>
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

    
    <section class="sp_pt_100 sp_pb_100">
        <div class="container">
            <?php
                $policy = content('privacy policy.content');
            ?>
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="site-card">
                            <div class="card-body">
                                <h4 class="text-center mb-2"><b><?php echo e(@$policy->data->Title); ?></b></h4>
                                <p> <?= clean(@$policy->data->Privacy_Policy); ?> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(template().'layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/theme5/pages/privacypolicy.blade.php ENDPATH**/ ?>