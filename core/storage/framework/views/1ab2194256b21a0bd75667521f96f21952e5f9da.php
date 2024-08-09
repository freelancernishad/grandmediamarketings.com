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

    <!-- blog details section start --> 
    <section class="sp_pt_120 sp_pb_120">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-8">
            <div class="blog-details-img">
                <img src="<?php echo e(getFile('blog', @$data->data->image)); ?>" height="400px" width="100%" alt="blog">
            </div>
            <div class="blog-details-content mt-4">
                <h3 class="title mb-3"><?php echo e(@$data->data->title); ?></h3>
                <p class="text-justifys"> <?php echo clean(@$data->data->description); ?></p>

                <div class="social-links my-3">
                    <h5 class="d-inline me-2"><?php echo e(__('Share')); ?>:</h5>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(URL::current()); ?>" target="_blank"
                        class="social-links-btn btn-border btn-sm ">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.twitter.com/intent/tweet?text=blog;url=<?php echo e(URL::current()); ?>" target="_blank" class="social-links-btn btn-border btn-sm"><i class="bx bxl-twitter"></i></a>
                </div>

                <div class="mt-5">
                    <h4><?php echo e(__('All Comments')); ?></h4>
                    <hr>
                    <?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="single-comment">
                            <div class="comment-thumb">
                                <?php if($comment->user->image): ?>
                                    <img src="<?php echo e(getFile('user', $comment->user->image)); ?>" alt="pp">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('asset/frontend/img/user.png')); ?>" alt="pp">
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <h5><?php echo e($comment->user->fname); ?> <?php echo e($comment->user->lname); ?></h5>
                                <p><?php echo e($comment->created_at->format('d M Y')); ?></p>

                                <p class="mt-2"><?php echo e($comment->comment); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p><?php echo e(__('Comment Not Found')); ?></p>
                    <?php endif; ?>

                    <?php echo e($comments->links('backend.partial.paginate')); ?>

                </div>

                <?php if(Auth::user()): ?>
                    <div class=" mt-5">
                        <div class="site-card">
                            <div class="card-header">
                                <h4><?php echo e(__('Post a Comment')); ?></h4>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo e(route('blogcomment', @$data->id)); ?>" method="post" role="form">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <textarea class="form-control" name="comment" rows="5" placeholder="Comment" required></textarea>
                                    </div>
                                    <button class="btn main-btn" type="submit"><?php echo e(__('Post Comemnt')); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-4 ps-lg-4">
            <div class="blog-widget">
              <h4 class="blog-widget-title"><?php echo e(__('Recent Blog Posts')); ?></h4>
              <div class="short-post-wrapper">
                <?php $__empty_1 = true; $__currentLoopData = $recentblog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="short-post">
                        <div class="thumb">
                            <img src="<?php echo e(getFile('blog', @$item->data->image)); ?>" alt="image">
                        </div>
                        <div class="content">
                            <h5 class="title"><a href="<?php echo e(route('blog', [@$item->id, Str::slug(@$item->data->title)])); ?>"><?php echo e(@$item->data->title); ?></a></h5>
                            <!-- <ul class="blog-meta mt-1">
                                <li><i class="far fa-clock"></i> 29 Mar, 2022</li>
                            </ul> -->
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> 
    <!-- blog details section end --> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make(template().'layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/divinebetatrade/public_html/core/resources/views/theme5/pages/blog.blade.php ENDPATH**/ ?>