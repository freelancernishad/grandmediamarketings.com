

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
                            <form action="" method="post" enctype="multipart/form-data">

                                <?php echo csrf_field(); ?>

                                <div class="row">

                                    <div class="form-group col-md-3 mb-3">
                                        <label class="col-form-label"><?php echo e(__('Razorpay Image')); ?></label>

                                        <div id="image-preview" class="image-preview"
                                            style="background-image:url(<?php echo e(getFile('gateways', @$gateway->gateway_image)); ?>);">
                                            <label for="image-upload"
                                                id="image-label"><?php echo e(__('Choose File')); ?></label>
                                            <input type="file" name="razor_image" id="image-upload" />
                                        </div>

                                    </div>

                                    <div class="col-md-9">

                                        <div class="row">


                                            <div class="form-group col-md-6">

                                                <label for=""><?php echo e(__('Gateway Currency')); ?></label>
                                                <input type="text" name="gateway_currency"
                                                    class="form-control form_control site-currency"
                                                    value="<?php echo e(@$gateway->gateway_parameters->gateway_currency ?? old('gateway_currency')); ?>">
                                            </div>

                                          

                                            <div class="form-group col-md-6">
                                                <label><?php echo e(__('Conversion Rate')); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <?php echo e('1 ' . @$general->site_currency . ' = '); ?>

                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control currency" name="rate"
                                                        value="<?php echo e(number_format(@$gateway->rate, 4) ?? 0); ?>">

                                                    <div class="input-group-append">
                                                        <div class="input-group-text append_currency">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="form-group col-md-12">

                                                <label for=""><?php echo e(__('RazorPay Key')); ?></label>
                                                <input type="text" name="razor_key" class="form-control"
                                                    value="<?php echo e(@$gateway->gateway_parameters->razor_key ?? old('razor_key')); ?>">
                                            </div>

                                            <div class="form-group col-md-12">

                                                <label for=""><?php echo e(__('RazorPay Secret')); ?></label>
                                                <input type="text" name="razor_secret" class="form-control"
                                                    value="<?php echo e(@$gateway->gateway_parameters->razor_secret ?? old('razor_secret')); ?>">
                                            </div>

                                            <div class="form-group col-md-12">

                                                <label for=""><?php echo e(__('Allow as payment method')); ?></label>

                                                <select name="status" id="" class="form-control selectric">

                                                    <option value="1" <?php echo e(@$gateway->status ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Yes')); ?>

                                                    </option>
                                                    <option value="0" <?php echo e(@$gateway->status ? '' : 'selected'); ?>>
                                                        <?php echo e(__('No')); ?></option>


                                                </select>

                                            </div>



                                        </div>



                                    </div>


                                    <div class="col-md-12">
                                        <button type="submit"
                                        class="btn btn-primary float-right"><?php echo e(__('Update RazorPay Information')); ?></button>
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


<?php $__env->startPush('script'); ?>

    <script>
        $(function() {
            'use strict'
            $.uploadPreview({
                input_field: "#image-upload", // Default: .image-upload
                preview_box: "#image-preview", // Default: .image-preview
                label_field: "#image-label", // Default: .image-label
                label_default: "<?php echo e(__('Choose File')); ?>", // Default: Choose File
                label_selected: "<?php echo e(__('Update Image')); ?>", // Default: Change File
                no_label: false, // Default: false
                success_callback: null // Default: null
            });

            $('.site-currency').on('keyup', function() {
                $('.append_currency').text($(this).val())
            })

            $('.append_currency').text($('.site-currency').val())
        })
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/backend/gateways/razorpay.blade.php ENDPATH**/ ?>