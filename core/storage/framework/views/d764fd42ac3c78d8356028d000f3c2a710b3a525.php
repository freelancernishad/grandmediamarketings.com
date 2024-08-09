<?php
$content = content('plan.content');
$plans = App\Models\Plan::where('status', 1)
->latest()
->get();
?>

<section class="plan-section sp_pt_120 sp_pb_120 sp_separator_bg">
    <div class="plan-section-el">
        <img src="<?php echo e(getFile('elements', 'el-1.png')); ?>" alt="image">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div class="sp_site_header  wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.3s">
                    <h2 class="sp_site_title"><?php echo e(__(@$content->data->title)); ?></h2>
                </div>
            </div>
        </div>

        <div class="row gy-4 items-wrapper justify-content-center">
            <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
            $plan_exist = App\Models\Payment::where('plan_id', $plan->id)
            ->where('user_id', Auth::id())
            ->where('next_payment_date', '!=', null)
            ->where('payment_status', 1)
            ->count();
            ?>

            <div class="col-xl-4 col-md-6">
                <div class="plan-item">
                    <div class="plan-name-area text-center">
                        <h3 class="plan-name mb-2"><?php echo e($plan->plan_name); ?></h3>
                    </div>

                    <div class="star-img">
                        <img src="<?php echo e(getFile('elements', 'star.png')); ?>" alt="image">
                    </div>

                    <div class="plan-rio text-center">
                        <p class="plan-amount">
                            <?php echo e(number_format($plan->return_interest, 2)); ?> <?php if($plan->interest_status == 'percentage'): ?>
                            <?php echo e('%'); ?>

                            <?php else: ?>
                            <?php echo e(@$general->site_currency); ?>

                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="plan-fatures">
                        <ul class="plan-list">
                            <?php if($plan->amount_type == 0): ?>
                                <li>
                                    <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Minimum')); ?> </span>
                                    <span class="details"> <?php echo e(number_format($plan->minimum_amount, 2) . ' ' . @$general->site_currency); ?></span>
                                </li>
                                <li>
                                    <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Maximum')); ?> </span>
                                    <span class="details"> <?php echo e(number_format($plan->maximum_amount, 2) . ' ' . @$general->site_currency); ?></span>
                                </li>
                            <?php else: ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Amount')); ?> </span>
                                <span class="details"> <?php echo e(number_format($plan->amount, 2) . ' ' . @$general->site_currency); ?></span>
                            </li>
                            <?php endif; ?>

                            <?php if($plan->return_for == 1): ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('For')); ?> </span>
                                <span class="details"> <?php echo e($plan->how_many_time); ?> <?php echo e(__('Times')); ?></span>
                            </li>
                            <?php else: ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('For')); ?> </span>
                                <span class="details"> <?php echo e(__('Lifetime')); ?></span>
                            </li>
                            <?php endif; ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Rio Back')); ?> </span>
                                <span class="details"> <?php echo e(__('Every')); ?> <?php echo e($plan->time->name); ?></span>
                            </li>
                            
                            <?php if($plan->capital_back == 1): ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Capital Back')); ?> </span>
                                <span class="details"> <?php echo e(__('YES')); ?></span>
                            </li>
                            <?php else: ?>
                            <li>
                                <span class="caption"><i class="fas fa-check"></i> <?php echo e(__('Capital Back')); ?> </span>
                                <span class="details"> <?php echo e(__('NO')); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <div class="view-affiliate-wrapper mt-4">
                            <p><?php echo e(__('Affiliate Bonus')); ?></p>
                            <button type="button" class="view-affiliate-btn"><?php echo e(__('View all')); ?></button>
                            <div class="plan-referral-area">
                                <div class="plan-referral pt-3">
                                    <button type="button" class="plan-referral-area-close"><i class="fas fa-times"></i></button>
                                    <h6 class="text-center mb-4"><?php echo e(__('Affiliate Bonus')); ?></h6>
                                    <?php if($plan->referrals): ?>
                                        <?php $__currentLoopData = $plan->referrals->level; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="single-referral">
                                                <p><?php echo e($value); ?></p>
                                                <span><?php echo e($plan->referrals->commision[$key]); ?> %</span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="plan-action">
                        <?php if($plan_exist >= $plan->invest_limit): ?>
                        <a class="btn btn-md main-btn plan-btn w-100 disabled" href="#">
                            <span><?php echo e(__('Max Limit exceeded')); ?></span>
                        </a>
                        <?php else: ?>
                        <a class="btn btn-md main-btn plan-btn w-100" href="<?php echo e(route('user.gateways', $plan->id)); ?>">
                            <span><?php echo e(__('Invest Now')); ?></span>
                        </a>
                        <?php if(auth()->guard()->check()): ?>
                        <button class="btn btn-md main-btn2 bg-white sp_text_dark balance w-100 mt-2 justify-content-center" data-plan="<?php echo e($plan); ?>" data-url=""><span><?php echo e(__('Invest Using Balance')); ?></span></button>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="calculate-area">
    <div class="calculator"><img src="<?php echo e(getFile('elements', 'calculator.png')); ?>" alt="image"></div>
    <div class="container">
        <div class="row gy-4 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="mbl-h text-white"><?php echo e(__('Amount')); ?></label>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="<?php echo e(__('Enter amount')); ?>">
            </div>
            <div class="col-lg-5 col-md-6">
                <label class="mbl-h text-white"><?php echo e(__('Investment Plan')); ?></label>
                <select class="select" name="selectplan" id="plan">
                    <option selected disabled class="sp_text_secondary"><?php echo e(__('Select a plan')); ?></option>
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->plan_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <a href="#0" id="calculate-btn" class="main-btn w-100"> <span><?php echo e(__('Calculate Earning')); ?></span></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invest" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?php echo e(route('user.investmentplan.submit')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Invest Now')); ?></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for=""><?php echo e(__('Invest Amount')); ?></label>
                            <input type="text" name="amount" class="form-control">
                            <input type="hidden" name="plan_id" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
                    <button type="submit" class="btn main-btn"><span><?php echo e(__('Invest Now')); ?></span></button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('script'); ?>
<script>
    $(function() {
        'use strict'

        $('.balance').on('click', function() {
            const modal = $('#invest');
            modal.find('input[name=plan_id]').val($(this).data('plan').id);
            modal.modal('show')
        });

        $(".view-affiliate-btn").on("click", function(){
            $(this).siblings(".plan-referral-area").addClass("active");
        });

        $(".plan-referral-area-close").on("click", function(){
            $(this).parent(".plan-referral").parent(".plan-referral-area").removeClass("active");
        });
    })
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\htdocs\divine_beta_trade\core\resources\views/theme5/sections/plan.blade.php ENDPATH**/ ?>