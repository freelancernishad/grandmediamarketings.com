<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?php echo e(__($pageTitle)); ?></h1>
            </div>

            <div class="row">
                <div class="col-md-12  col-lg-12 col-12 all-users-table">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Investment Commission')); ?></h5>

                            <button class="btn btn-primary btn-sm add"><?php echo e(__('Add New')); ?></button>


                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="">
                                            <th scope="col"><?php echo e(__('Plan')); ?></th>
                                            <th scope="col"><?php echo e(__('Type')); ?></th>
                                            <th scope="col"><?php echo e(__('Referral')); ?></th>
                                            <th scope="col"><?php echo e(__('Action')); ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php echo e($ref->plan->plan_name); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($ref->type); ?>

                                                </td>

                                                <td>

                                                    <?php $__currentLoopData = $ref->level; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="mb-3">
                                                            <span
                                                                class="badge badge-primary"><?php echo e($lv . ' => ' . $ref->commision[$key]); ?>

                                                                %</span>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </td>

                                                <td>
                                                    <button class="btn btn-primary edit"
                                                        data-route="<?php echo e(route('admin.referral.update', $ref->id)); ?>"
                                                        data-referral="<?php echo e($ref); ?>">
                                                        Edit
                                                    </button>
                                                </td>


                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

        </section>
    </div>


    <div class="modal fade" id="add" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="<?php echo e(route('admin.referral.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('Add Commission')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for=""><?php echo e(__('Plan')); ?></label>
                            <select name="plan_id" id="" class="form-control">
                                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->plan_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for=""><?php echo e(__('Type')); ?></label>
                            <select name="type" id="" class="form-control">
                                <option value="invest">Invest</option>
                                <option value="interest">Interest</option>
                            </select>
                        </div>


                        <div class="input-group mb-3 mt-3 ml-auto ">
                            <input type="number" class="form-control counter" placeholder="How Many Field You Want"
                                required>

                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="generator"><?php echo e(__('Generate')); ?></button>
                            </div>
                        </div>


                        <div class="append  ml-auto">

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="edit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('update Commission')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for=""><?php echo e(__('Plan')); ?></label>
                            <select name="plan_id" id="plan" class="form-control">
                                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->plan_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for=""><?php echo e(__('Type')); ?></label>
                            <select name="type" id="type" class="form-control">
                                <option value="invest">Invest</option>
                                <option value="interest">Interest</option>
                            </select>
                        </div>


                        <div class="input-group mb-3 mt-3 ml-auto ">
                            <input type="number" class="form-control counterUpdate" placeholder="How Many Field You Want">

                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button"
                                    id="generatorUpdate"><?php echo e(__('Generate')); ?></button>
                            </div>
                        </div>


                        <div class="appendUpdate  ml-auto">



                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        'use strict';
        $('.create-invest').hide();
        $('.create-interest').hide();

        $(document).ready(function() {


            $('.add').on('click', function() {
                const modal = $('#add');

                modal.modal('show')
            })


            $('.edit').on('click', function() {
                const modal = $('#edit');

                let data = $(this).data('referral')
                let url = $(this).data('route')


                modal.find('form').attr('action', url);

                modal.find("#plan option[value='"+ data.plan_id +"']").attr("selected", "selected");
                modal.find("#type option[value='"+ data.type +"']").attr("selected", "selected");




                var html = "";

                for (let i = 0; i < data.level.length; i++) {
                    html += `

                        <div class="input-group mb-3 mt-3 ">
                            <div class="input-group-prepend">
                                <input class="btn btn-primary" type="text"  name=level[] value="${data.level[i]}" readonly>
                            </div>
                                <input type="number" required class="form-control" name=commision[]
                                    placeholder="Commision" value="${data.commision[i]}">

                                <div class="input-group-append">
                                    <button class="btn btn-primary text-light no-drop" type="button"
                                        >%</button>
                                    <button class="btn btn-danger text-white delete_invest" type="button"
                                        >X</button>
                                </div>

                        </div>
             `

                }


                $('.appendUpdate').html(html).hide().slideDown('slow');

                modal.modal('show')
            })





            $(document).on('click', '#generator', function() {


                var value = $('.counter').val();



                if (value > 7) {
                    iziToast.error({
                        message: 'Max Limit of Refferal level is 7 ',
                        position: 'topRight'
                    });

                    return;
                }
                var viewHtml = "";

                for (let i = 0; i < value; i++) {
                    viewHtml += `

                        <div class="input-group mb-3 mt-3 ">
                            <div class="input-group-prepend">
                                <input class="btn btn-primary" type="text"  name=level[] value="level ${i+1}" readonly>
                            </div>
                                <input type="number" required class="form-control" name=commision[]
                                    placeholder="Commision">

                                <div class="input-group-append">
                                    <button class="btn btn-primary text-light no-drop" type="button"
                                        >%</button>
                                    <button class="btn btn-danger text-white delete_invest" type="button"
                                        >X</button>
                                </div>

                        </div>
             `
                    $('.append').html(viewHtml).hide().slideDown('slow');


                }


            });


            $(document).on('click', '#generatorUpdate', function() {



                var value = $('.counterUpdate').val();



                if (value > 7) {
                    iziToast.error({
                        message: 'Max Limit of Refferal level is 7 ',
                        position: 'topRight'
                    });

                    return;
                }
                var viewHtml = "";

                for (let i = 0; i < value; i++) {
                    viewHtml += `

        <div class="input-group mb-3 mt-3 ">
            <div class="input-group-prepend">
                <input class="btn btn-primary" type="text"  name=level[] value="level ${i+1}" readonly>
            </div>
                <input type="number" required class="form-control" name=commision[]
                    placeholder="Commision">

                <div class="input-group-append">
                    <button class="btn btn-primary text-light no-drop" type="button"
                        >%</button>
                    <button class="btn btn-danger text-white delete_invest" type="button"
                        >X</button>
                </div>

        </div>
`
                    $('.appendUpdate').html(viewHtml).hide().slideDown('slow');


                }


            });
            $(document).on('click', '.delete_invest', function() {
                $(this).closest('.input-group').remove();

                var count = $('.append_invest').children().length;

                if (count == 0) {
                    $('.create-invest').hide();
                }

            });





            $('#interest').on('click', function() {
                var value = $('.interest_commision').val();
                var viewHtml = "";

                if (value > 5) {
                    iziToast.error({
                        message: 'Max Limit of Refferal level is 5 ',
                        position: 'topRight'
                    });

                    return;
                }


                for (let i = 0; i < value; i++) {
                    viewHtml += `

            <div class="input-group mb-3 mt-3 ">
                <div class="input-group-prepend">
                                                <input class="btn btn-success" type="text"  name="level[]"  value="level ${i+1}" readonly>
                                            </div>
                                            <input type="number" name=commision[] class="form-control"
                                                placeholder="Commision" min="0" required>

                                            <div class="input-group-append">
                                                <button class="btn btn-success text-light no-drop" type="button"
                                                    >%</button>
                                                <button class="btn btn-danger text-white delete_interest" type="button"
                                                    >X</button>
                                            </div>


                                        </div>

             `
                    $('.append_interest').html(viewHtml).hide().slideDown('slow');
                    $('.interest_commision').val('');
                    $('.create-interest').show();
                }


            });
            $(document).on('click', '.delete_interest', function() {
                $(this).closest('.input-group').remove();
                var count = $('.append_interest').children().length;

                if (count == 0) {
                    $('.create-interest').hide();
                }
            });
        });

        $(function() {

            $('#investstatus').on('change', function() {
                let status = $(this).data('status');
                let url = $(this).data('url');

                $.ajax({

                    headers: {
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
                    },

                    url: url,

                    data: {
                        status: status
                    },

                    method: "POST",

                    success: function(response) {
                        iziToast.success({

                            message: response.success,
                            position: 'topRight'
                        });
                    }
                })
            })
        })

        $(function() {

            $('#intereststatus').on('change', function() {
                let status = $(this).data('status');
                let url = $(this).data('url');

                $.ajax({

                    headers: {
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
                    },

                    url: url,

                    data: {
                        status: status
                    },

                    method: "POST",

                    success: function(response) {
                        iziToast.success({

                            message: response.success,
                            position: 'topRight'
                        });
                    }
                })
            })
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\divine_beta_trade\core\resources\views/backend/referral/index.blade.php ENDPATH**/ ?>