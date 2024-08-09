<?php $__env->startPush('style'); ?>
    <style>
        .activebtn {
            background-color: #ffc451;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content2'); ?>
    <div class="dashboard-body-part">

        <div class="mobile-page-header">
            <h5 class="title"><?php echo e(__('Withdraw History')); ?></h5>
            <a href="<?php echo e(route('user.dashboard')); ?>" class="back-btn"><i class="bi bi-arrow-left"></i> <?php echo e(__('Back')); ?></a>
        </div>

        <div class="site-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                <h5 class="mb-sm-0 mb-2"><?php echo e(__('Withdraw Log')); ?></h5>
                <form action="" method="get">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="text" name="trx" class="form-control form-control-sm" placeholder="transaction id">
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control form-control-sm" placeholder="Search User" name="date">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn main-btn btn-sm"><?php echo e(__('Search')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-header">
                <div class="text-center mb-4">
                    <div class="tab-btn-group">
                        <a class="tab-btn <?php echo e(Request::routeIs('user.withdraw.all') ? 'active' : ''); ?>"
                            href="<?php echo e(route('user.withdraw.all')); ?>"><?php echo e(__('All')); ?> <span class="d-sm-inline-block d-none"><?php echo e(__('Withdraw')); ?></span></a>

                        <a class="tab-btn <?php echo e(Request::routeIs('user.withdraw.pending') ? 'active' : ''); ?>"
                            href="<?php echo e(route('user.withdraw.pending')); ?>"><?php echo e(__('Pending')); ?> <span class="d-sm-inline-block d-none"><?php echo e(__('Withdraw')); ?></span></a>

                        <a class="tab-btn <?php echo e(Request::routeIs('user.withdraw.complete') ? 'active' : ''); ?>"
                            href="<?php echo e(route('user.withdraw.complete')); ?>"><?php echo e(__('Complete')); ?> <span class="d-sm-inline-block d-none"><?php echo e(__('Withdraw')); ?></span></a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table site-table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('TRX')); ?></th>
                                <th><?php echo e(__('Date')); ?></th>
                                <th><?php echo e(__('Method Name')); ?></th>
                                <th><?php echo e(__('Withdraw Amount')); ?></th>
                                <th><?php echo e(__('Getable Amount')); ?></th>
                                <th><?php echo e(__('Charge Type')); ?></th>
                                <th><?php echo e(__('Charge')); ?></th>
                                <th><?php echo e(__('status')); ?></th>
                                <th><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $withdrawlogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $withdrawlog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-caption="<?php echo e(__('Sl')); ?>"><?php echo e($key + $withdrawlogs->firstItem()); ?></td>
                                <td data-caption="<?php echo e(__('Date')); ?>"><?php echo e(__($withdrawlog->created_at->format('d F Y'))); ?></td>
                                <td data-caption="<?php echo e(__('Method Name')); ?>"><?php echo e(__($withdrawlog->withdrawMethod->name)); ?></td>
                                <td data-caption="<?php echo e(__('Getable Amount')); ?>"><?php echo e($general->currency_icon .
                                    '  ' .
                                    $withdrawlog->withdraw_amount +
                                    ($withdrawlog->withdrawMethod->charge_type === 'percent'
                                        ? ($withdrawlog->withdraw_amount * $withdrawlog->withdraw_charge) / 100
                                        : $withdrawlog->withdraw_amount)); ?>

                                </td>
                                <td>


                                    <?php echo e($withdrawlog->withdraw_amount); ?>


                                </td>
                                <td data-caption="<?php echo e(__('Charge Type')); ?>">
                                    <?php echo e(ucwords($withdrawlog->withdrawMethod->charge_type)); ?>

                                </td>
                                <td data-caption="<?php echo e(__('Charge')); ?>">
                                    <?php if($withdrawlog->withdrawMethod->charge_type == 'percent'): ?>
                                        
                                    <?php echo e($withdrawlog->withdraw_amount * $withdrawlog->withdraw_charge / 100 .' '.$general->site_currency); ?>

                                
                                <?php else: ?>
                                
                                <?php echo e(number_format($withdrawlog->withdraw_charge, 2).' '.@$general->site_currency); ?>

                                <?php endif; ?>
                                </td>
                                <td data-caption="<?php echo e(__('status')); ?>">
                                    <?php if($withdrawlog->status == 1): ?>
                                        <span
                                            class="sp_badge sp_badge_success"><?php echo e(__('Success')); ?></span>
                                    <?php elseif($withdrawlog->status == 2): ?>
                                        <span
                                            class="sp_badge sp_badge_danger"><?php echo e(__('Rejected')); ?></span>
                                    <?php else: ?>
                                        <span
                                            class="sp_badge sp_badge_warning"><?php echo e(__('Pending')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td data-caption="<?php echo e(__('Action')); ?>">
                                    <button class="sp_view_btn details" data-user_data="<?php echo e(json_encode($withdrawlog->user_withdraw_prof)); ?>" data-withdraw="<?php echo e($withdrawlog); ?>"><i class="far fa-eye"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td data-caption="<?php echo e(__('Status')); ?>" class="text-center" colspan="100%">
                                        <?php echo e(__('No Data Found')); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($withdrawlogs->hasPages()): ?>
                    <?php echo e($withdrawlogs->links()); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Withdraw Details')); ?></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="withdraw-details">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm sp_btn_danger"
                        data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('script'); ?>
    <script>
        $(function() {
            'use strict'

            $('.details').on('click', function() {
                const modal = $('#details');

                let html = `

                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                               <?php echo e(__('Withdraw Email')); ?>

                                <span>${$(this).data('user_data').email}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e(__('Withdraw Account Information')); ?>

                                <span>${$(this).data('user_data').account_information}</span>
                            </li>


                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e(__('Note For Withdraw')); ?>

                                <span>${$(this).data('user_data').note}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e(__('Withdraw Transaction')); ?>

                                <span>${$(this).data('withdraw').transaction_id}</span>
                            </li>
                        </ul>
                `;

                modal.find('.withdraw-details').html(html);

                modal.modal('show');
            })

        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(template() . 'layout.master2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/theme5/user/withdraw/withdraw_log.blade.php ENDPATH**/ ?>