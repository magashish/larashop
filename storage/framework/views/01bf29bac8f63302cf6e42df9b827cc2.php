<?php
$allReviews = get_google_reviews();
$total = $allReviews->count();
$charLimit = 120;
if ($total > 8) {
    $middle = ceil($total / 2);
    $topReviews = $allReviews->slice(0, $middle);
    $bottomReviews = $allReviews->slice($middle);
} else {
    $topReviews = $allReviews;
    $bottomReviews = collect();
}
?>


<section class="client-testimonial-sec">
    <div class="container-fluid px-0">
        <div class="review-slider-top" dir="rtl">
            <?php $__currentLoopData = $topReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('consumer.blocks.review-card', [
                    'review' => $review,
                    'limit' => $charLimit
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="review-slider-bottom mt-4">
            <?php $__currentLoopData = $bottomReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('consumer.blocks.review-card', [
                    'review' => $review,
                    'limit' => $charLimit
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/google-review-slider.blade.php ENDPATH**/ ?>