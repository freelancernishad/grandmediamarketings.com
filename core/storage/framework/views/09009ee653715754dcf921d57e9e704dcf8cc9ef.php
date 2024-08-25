<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?php echo e(__('Designations')); ?></h1>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-12 col-12 all-users-table">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Designation List')); ?></h5>
                            <button class="btn btn-primary btn-sm add" data-toggle="modal" data-target="#addDesignationModal"><?php echo e(__('Add New')); ?></button>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('ID')); ?></th>
                                            <th><?php echo e(__('Name')); ?></th>
                                            <th><?php echo e(__('Minimum Investment')); ?></th>
                                            <th><?php echo e(__('Bonus')); ?></th>
                                            <th><?php echo e(__('Commission Level')); ?></th>
                                            <th><?php echo e(__('Users')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($designation->id); ?></td>
                                                <td><?php echo e($designation->name); ?></td>
                                                <td>$<?php echo e(number_format($designation->minimum_investment, 2)); ?></td>
                                                <td>$<?php echo e(number_format($designation->bonus, 2)); ?></td>
                                                <td><?php echo e($designation->commission_level); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('admin.designations.users', $designation->id)); ?>" class="btn btn-info">
                                                        View User ( <?php echo e($designation->user_designations_count); ?> )
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary edit"
                                                        data-toggle="modal"
                                                        data-target="#editDesignationModal"
                                                        data-route="<?php echo e(route('admin.designations.update', $designation->id)); ?>"
                                                        data-designation="<?php echo e($designation); ?>">
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
                </div>
            </div>
        </section>
    </div>

    <!-- Add Designation Modal -->
    <div class="modal fade" id="addDesignationModal" tabindex="-1" role="dialog" aria-labelledby="addDesignationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?php echo e(route('admin.designations.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('Add Designation')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name"><?php echo e(__('Name')); ?></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="minimum_investment"><?php echo e(__('Minimum Investment')); ?></label>
                            <input type="number" name="minimum_investment" id="minimum_investment" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bonus"><?php echo e(__('Bonus')); ?></label>
                            <input type="number" name="bonus" id="bonus" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="commission_level"><?php echo e(__('Commission Level')); ?></label>
                            <input type="number" name="commission_level" id="commission_level" class="form-control" required>
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

    <!-- Edit Designation Modal -->
    <div class="modal fade" id="editDesignationModal" tabindex="-1" role="dialog" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('Edit Designation')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name"><?php echo e(__('Name')); ?></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_minimum_investment"><?php echo e(__('Minimum Investment')); ?></label>
                            <input type="number" name="minimum_investment" id="edit_minimum_investment" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_bonus"><?php echo e(__('Bonus')); ?></label>
                            <input type="number" name="bonus" id="edit_bonus" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_commission_level"><?php echo e(__('Commission Level')); ?></label>
                            <input type="number" name="commission_level" id="edit_commission_level" class="form-control" required>
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
    $(document).ready(function() {
        // Show Edit Modal with Data
        $('.edit').on('click', function() {
            const modal = $('#editDesignationModal');
            const data = $(this).data('designation');
            const url = $(this).data('route');

            modal.find('form').attr('action', url);
            modal.find('#edit_name').val(data.name);
            modal.find('#edit_minimum_investment').val(data.minimum_investment);
            modal.find('#edit_bonus').val(data.bonus);
            modal.find('#edit_commission_level').val(data.commission_level);

            modal.modal('show');
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('backend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\divine_beta_trade\core\resources\views/backend/designations/index.blade.php ENDPATH**/ ?>