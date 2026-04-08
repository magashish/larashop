<?php $__env->startSection('content'); ?>

<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0 unsubscribe-wrapper">
    <div class="container">
        <div class="inner-content">
            <img src="<?php echo e(asset('images/Xhale_team_shoot_-_Hero_Banner.webp')); ?>" class="d-lg-block d-none" alt="Banner Image">
            <img src="<?php echo e(asset('images/Xhale_team_shoot_-_Hero_Banner.webp')); ?>" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="inner-content text-center py-5">
            <h1 class="text-success">🎉 Success!</h1>
            <p class="lead mt-3"><?php echo e($message ?? 'Your action has been completed successfully.'); ?></p>
            <a href="<?php echo e(url('/')); ?>" class="btn-home signup">Back to Home</a>
        </div>
    </div>
</section>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.consumer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/unsubscribe/success.blade.php ENDPATH**/ ?>