<?php
 $latestDraw = getLatestDrawCountdown();
?>
<?php if($latestDraw): ?>

<?php
$prizeText = '';
$prizeAmount = '';
$prizeDescription = $latestDraw->prize_description;
if ($latestDraw->prize_type === 'cash') {
    $prizeText = "Xhale";
    $prizeAmount = '$' . number_format($latestDraw->cash_amount, 2);
} elseif ($latestDraw->prize_type === 'non_cash') {
    $prizeText = $latestDraw->prize_title;
    $prizeAmount = $latestDraw->prize_sub_title;
    // if (!empty($latestDraw->prize_value_amount)) {
    //     $prizeAmount .= '$' . number_format($latestDraw->prize_value_amount, 2);
    // }
}
?>



<section class="cash-giveway car-giveaway">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-12 order-lg-1 order-2">
               <div class="cash-img">
                <?php if(isset($latestDraw->prize_image)): ?>
                <img class="lazy lozad" src="<?php echo e(asset('storage/' . $latestDraw->prize_image)); ?>" alt="Current Image">
                <?php endif; ?>
            </div> 

        </div>
        <div class="col-lg-5 col-12 order-lg-2 order-3">
            <div class="cash-content">
                <div class="bg-title small-title">
                    <div class="yellow-bg"><?php echo e($prizeText); ?></div><br>
                    <div class="black-bg"> <?php echo e($prizeAmount); ?></div>
                </div>
                <p><?php echo $prizeDescription ?? ''; ?></p>
                <div class="action-btn">
                    <?php echo $__env->make('global.login-signup-btn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
        <div class="col-12 order-lg-3 order-1">
            <div class="clock" data-date="<?php echo e($latestDraw->draw_date); ?>" id="slide-clock-<?php echo e($latestDraw->id); ?>"></div>
        </div>
    </div>
</div>
<div>
    <img src="../images/notesImg.png" class="money-img d-lg-none d-block">
</div>
</section>
<?php endif; ?>

<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/cash-giveaway.blade.php ENDPATH**/ ?>