

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?php echo e(__($pageTitle)); ?></h1>
            </div>

            <div class="row">

                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">

                                <?php echo csrf_field(); ?>

                                <div class="row">

                                    <div class="form-group col-md-6">

                                        <label for=""><?php echo e(__('Recaptcha Key')); ?></label>

                                        <input type="text" name="recaptcha_key" class="form-control" placeholder="Recaptcha Key"
                                            value="<?php echo e(@$recaptcha->recaptcha_key); ?>">

                                            

                                    </div>

                                    <div class="form-group col-md-6">

                                        <label for=""><?php echo e(__('Recaptcha Secret')); ?></label>
                                        <input type="text" name="recaptcha_secret" class="form-control" placeholder="Recaptcha Secret"
                                            value="<?php echo e(@$recaptcha->recaptcha_secret); ?>">

                                    </div>


                                    <div class="form-group col-md-6">

                                        <label for=""><?php echo e(__('Allow Recaptcha')); ?></label>

                                        <select name="allow_recaptcha" id="" class="form-control selectric">

                                            <option value="1" <?php echo e(@$recaptcha->allow_recaptcha==1 ? 'selected' : ''); ?>>
                                                <?php echo e(__('Yes')); ?></option>
                                            <option value="0" <?php echo e(@$recaptcha->allow_recaptcha==0 ? 'selected' : ''); ?>>
                                                <?php echo e(__('No')); ?></option>

                                        </select>

                                    </div>

                                    <div class="form-group col-md-12">


                                        <button type="submit" class="btn btn-primary float-right"><?php echo e(__('Update Recaptcha')); ?></button>

                                    </div>


                                </div>


                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/backend/setting/recaptcha.blade.php ENDPATH**/ ?>