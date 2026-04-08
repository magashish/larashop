<?php
    $reviews = get_google_reviews();
    $limit = 150;
?>

<div class="review-slider">
    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $text = $review->review_text;
        $short = \Illuminate\Support\Str::limit($text, $limit, '');
        $hasMore = strlen($text) > $limit;
        $imgUrl = str_starts_with($review->author_img, 'http') 
                  ? $review->author_img 
                  : asset('storage/' . $review->author_img);
    ?>

    <div class="review-items">
        <div class="author-img">
            <img src="<?php echo e($imgUrl); ?>" alt="<?php echo e($review->author_name); ?>">
            <img src="<?php echo e(asset('images/' . $review->type . '.svg')); ?>" class="over-icon"  alt="<?php echo e($review->type); ?>">
        </div>

        <div class="author-name"><?php echo e($review->author_name); ?></div>

       

        <div class="rating-icon rating-stars mt-2 mb-2" style="--rating: <?php echo e($review->rating); ?>;">
            <span class="stars-bg">★★★★★</span>
            <span class="stars-fill">★★★★★</span>
        </div>

        
        <div class="review-content review-content-desktop d-none d-lg-block">
            <p class="review-text">
                <span class="short-text"><?php echo $text; ?></span>
            </p>
        </div>

        
        <div class="review-content review-content-mobile d-block d-lg-none">
            <p class="review-text">
                <span class="short-text"><?php echo $short; ?></span>
                <?php if($hasMore): ?>
                <span class="ellipsis">... </span>
                <span class="full-text" style="display:none"><?php echo substr($text, $limit); ?></span>
                <?php endif; ?>
            </p>
            <?php if($hasMore): ?>
            <button type="button" class="read-more-btn" aria-expanded="false">Read more</button>
            <?php endif; ?>
        </div>

        <p>
            <strong><?php echo e($review->type); ?></strong> rating score:
           <strong><?php echo e(number_format($review->rating, 1)); ?></strong> of 5
        </p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php /**PATH /Applications/MAMP/htdocs/laravel/xhalenew/resources/views/consumer/blocks/google-review.blade.php ENDPATH**/ ?>