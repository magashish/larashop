<?php
    $latestDraw = getLatestDrawCountdown();
?>
<?php if($latestDraw): ?>
    <section class="top-header-cash-giveway car-giveaway">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 order-lg-3 order-1">
                    <span class="top-header-cash-giveway-title">WIN A <?php echo e($latestDraw->title); ?></span>
                    <div class="clock" data-date="<?php echo e($latestDraw->draw_date); ?>" id="slide-clock-<?php echo e($latestDraw->id); ?>"></div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/top-cash-giveaway-time.blade.php ENDPATH**/ ?>