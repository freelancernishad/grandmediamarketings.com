<?php $__env->startPush('name'); ?>
    <style>
        .card-footer {
            padding: 0.5rem, 0rem !important;
        }

    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <section class="page-banner">
        <div class="container">
            <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="title text-white"><?php echo e(__($pageTitle)); ?></h2>
                <ul class="page-breadcrumb justify-content-center mt-2">
                    <li><a href="index.html"><?php echo e(__('Home')); ?></a></li>
                    <li><?php echo e(__($pageTitle)); ?></li>
                </ul>
            </div>
            </div>
        </div>
    </section>

    <section class="blog-section sp_pt_100 sp_pb_100">
        <div class="container">
            <div class="row gy-4">
                <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $comment = App\Models\Comment::where('blog_id', $blog->id)->count();
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="blog-item">
                            <div class="blog-thumb">
                                <img src="<?php echo e(getFile('blog', @$blog->data->image)); ?>" alt="blog thumb">
                            </div>
                            <div class="blog-content">
                                <ul class="blog-meta mb-2">
                                    <li><i class="fas fa-clock"></i> <?php echo e(@$blog->created_at->diffforhumans()); ?></li>
                                    <li><i class="fas fa-comment"></i> <?php echo e($comment); ?> <?php echo e(__('comments')); ?></li>
                                </ul>
                                <h4 class="blog-title"><a href="<?php echo e(route('blog', [@$blog->id, Str::slug(@$blog->data->title)])); ?>"><?php echo e(@$blog->data->title); ?></a></h4>
                                <a href="<?php echo e(route('blog', [@$blog->id, Str::slug(@$blog->data->title)])); ?>" class="blog-btn">
                                    <span><?php echo e(__('Read More')); ?></span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php echo e($blogs->links('backend.partial.paginate')); ?>

        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(template().'layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/theme5/pages/allblog.blade.php ENDPATH**/ ?>