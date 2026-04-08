<?php
$latestDraw = getSecondDrawCountdown();
?>
<?php if($latestDraw): ?>
<section id="cashStickyBar" class="sticky-top-header-cash-giveway top-header-cash-giveway car-giveaway">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 order-lg-3 order-1">
                <div class="sticky-top-cash-giveway">
                 <p><?php echo e($latestDraw->title); ?> $<?php echo e($latestDraw->cash_amount); ?> <?php echo e($latestDraw->draw_date->format('l, F jS Y \a\t g:i A')); ?></p> 

                 

              
            </div>
            </div>
        </div>
    </div>
</section>
<script>
const stickyBar = document.getElementById('cashStickyBar');
const triggerPoint = 100;

window.addEventListener('scroll', () => {
    if (!stickyBar) return;

    if (window.scrollY > triggerPoint) {
        stickyBar.classList.add('is-visible');
    } else {
        stickyBar.classList.remove('is-visible');
    }
}, { passive: true });


   

</script>
<?php endif; ?>


<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/sticky-top-cash-giveaway-time.blade.php ENDPATH**/ ?>