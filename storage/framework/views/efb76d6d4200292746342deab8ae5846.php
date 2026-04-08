<?php $__env->startSection('content'); ?>



<div class="giveaways-banner-sec desktop">
    <?php 
    $heroSlides = json_decode($metaData['landing_banner_slider'] ?? '[]', true); 
    ?>
    <div class="giveaways-slider">
        <?php if(!empty($heroSlides) && is_array($heroSlides)): ?>
        <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!empty($slide) && Storage::disk('public')->exists($slide)): ?>
        <div class="giveaways-wrapper" style="background-image: url('<?php echo e(Storage::url($slide)); ?>'); "></div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        
        <div class="giveaways-wrapper" style="background-image: url('<?php echo e(asset('images/herolp-img.jpg')); ?>'); "></div>
        <?php endif; ?>
    </div>
    <div class="win-mazda">
        <?php $winImg = $metaData['landing_hero_image'] ?? 'win.png'; ?>
        <?php if(!empty($winImg) && (Storage::disk('public')->exists($winImg) || file_exists(public_path('images/'.$winImg)))): ?>
        <img src="<?php echo e(Str::contains($winImg, '/') ? Storage::url($winImg) : asset('images/'.$winImg)); ?>" alt="Win">
        <?php endif; ?>

        <a href="#supporting-community-sec" class="signup">Read More</a>
    </div>
</div>

<div class="giveaways-banner-sec for-mobile">
    <?php 
    $heromobileSlides = json_decode($metaData['landing_banner_mobile_slider'] ?? '[]', true); 
    ?>

    <div class="giveaways-slider">
     <?php if(!empty($heromobileSlides) && is_array($heromobileSlides)): ?>
     <?php $__currentLoopData = $heromobileSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
     <?php if(!empty($slide) && Storage::disk('public')->exists($slide)): ?>
     <div class="giveaways-wrapper" style="background-image: url('<?php echo e(Storage::url($slide)); ?>'); "></div>
     <?php endif; ?>
     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     <?php else: ?>
     
     <div class="giveaways-wrapper" style="background-image: url('<?php echo e(asset('images/mob-hero.jpg')); ?>'); "></div>
     <?php endif; ?>
 </div>
 <div class="win-mazda">
    <?php $winImg = $metaData['landing_hero_image'] ?? 'win.png'; ?>
    <?php if(!empty($winImg) && (Storage::disk('public')->exists($winImg) || file_exists(public_path('images/'.$winImg)))): ?>
    <img src="<?php echo e(Str::contains($winImg, '/') ? Storage::url($winImg) : asset('images/'.$winImg)); ?>" alt="Win">
    <?php endif; ?>
</div>
</div>



<div class="access-column popup-wrapper">
            <?php echo $__env->make('consumer.blocks.pop-up-subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
          </div> 



<div class="access-column">
    <div class="xhale-access">
        <div class="container">
            <div class="xhale-access-row">
                <div class="xhale-access-row">
                    <div class="jed">
                        <?php $jedImg = $metaData['landing_package_image'] ?? 'jed.png'; ?>
                        <?php if(!empty($jedImg)): ?>
                        <img src="<?php echo e(Str::contains($jedImg, '/') ? Storage::url($jedImg) : asset('images/jed.png')); ?>" alt="Jed">
                        <?php endif; ?>
                    </div>
                    <div class="sec-head">
                        <div class="bg-title mx-0 mb-5">
                            <div class="yellow-bg"><?php echo e($metaData['landing_package_title'] ?? 'UNLOCK YOUR'); ?></div>
                            <div class="black-bg"><?php echo e($metaData['landing_package_subtitle'] ?? 'XHALE ACCESS'); ?></div>
                        </div>
                    </div>
                </div> 
            </div>          
        </div>
    </div>

    <?php echo $__env->make('consumer.blocks.pop-up-subscription-package-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 


</div>











<?php 
$worthSlider = json_decode($metaData['landing_worth_slider'] ?? '[]', true); 
?>
<div class="win-mazda-car">
    <div class="container">
        <div class="sec-head">
            <div class="bg-title">
                <div class="yellow-bg"><?php echo e($metaData['landing_worth_title'] ?? 'WIN A NEW MAZDA CX5'); ?></div>
                <div class="black-bg"><?php echo e($metaData['landing_worth_subtitle'] ?? 'WORTH $55,000'); ?></div>
            </div>
        </div>
        <?php if(!empty($worthSlider)): ?>
        <div class="gallery-wrapper position-relative">
            <div class="main-slider" id="win-mazda-main-slider">
                <?php $__currentLoopData = $worthSlider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(Storage::disk('public')->exists($img)): ?>
                <div><img src="<?php echo e(Storage::url($img)); ?>" alt="Prize Image"></div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if(count($worthSlider) > 1): ?>
            <div class="thumb-slider" id="win-mazda-thumb">
                <?php $__currentLoopData = $worthSlider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(Storage::disk('public')->exists($img)): ?>
                <div><img src="<?php echo e(Storage::url($img)); ?>" alt="Thumb"></div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php echo $__env->make('consumer.blocks.cash-giveaway', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php echo $__env->make('global.upcoming-draws-carousel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php if(!empty($metaData['landing_real_video_src']) && Storage::disk('public')->exists($metaData['landing_real_video_src'])): ?>
<section class="real-giveaway-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 mb-md-5 mb-2">
                <div class="bg-title small-title">
                    <div class="yellow-bg"><?php echo e($metaData['landing_real_title'] ?? 'REAL GIVEAWAYS'); ?></div>
                    <div class="black-bg"><?php echo e($metaData['landing_real_subtitle'] ?? 'REAL WINNERS'); ?></div>
                </div>
            </div>
            <div class="col-12">
                <div class="video">
                    <div class="ratio ratio-16x9">
                        <video class="video w-full rounded-md shadow-md" 
                        poster="<?php echo e(!empty($metaData['landing_real_video_poster']) ? Storage::url($metaData['landing_real_video_poster']) : ''); ?>" 
                        src="<?php echo e(Storage::url($metaData['landing_real_video_src'])); ?>" 
                        controls preload="metadata" controlslist="nodownload">
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<?php endif; ?>






<?php echo $__env->make('consumer.blocks.google-review-slider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<section class="past-winners">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 offset-md-4">
                <div class="bg-title small-title">
                    <div class="yellow-bg"><?php echo e($metaData['landing_past_winner_title'] ?? 'SOME OF OUR'); ?></div>
                    <div class="black-bg"><?php echo e($metaData['landing_past_winner_subtitle'] ?? 'PAST WINNERS'); ?></div>
                </div>

                <div class="past-winner-slider">
                    <?php $pastWinners = json_decode($metaData['landing_past_winner_slider'] ?? '[]', true); ?>
                    <?php if(!empty($pastWinners)): ?>
                    <?php $__currentLoopData = $pastWinners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $winnerImg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(Storage::disk('public')->exists($winnerImg)): ?>
                    <div class="winner-review-wrapper">
                        <img src="<?php echo e(Storage::url($winnerImg)); ?>" alt="Winner" class="img-fluid rounded">
                    </div>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    
                    <div><img src="<?php echo e(asset('images/winner.png')); ?>"></div>
                    <div><img src="<?php echo e(asset('images/winner.png')); ?>"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="jed">
            <?php $pastWinnerJed = $metaData['landing_past_winner_image'] ?? 'jed.png'; ?>
            <img src="<?php echo e(Str::contains($pastWinnerJed, '/') ? Storage::url($pastWinnerJed) : asset('images/jed.png')); ?>">
        </div>  
    </div>
</section>




<div class="supporting-community" id="supporting-community-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12 order-lg-1 order-2">
                <div class="supporting-community-contnet mb-lg-0 mb-5">                  
                    <div class="supporting-community-inner">
                        <h2>
                            <?php echo e($metaData['landing_harcourt_title'] ?? 'Supporting Harcourt'); ?> 
                            <span><?php echo e($metaData['landing_harcourt_subtitle'] ?? 'Community'); ?></span>
                        </h2>

                        <?php
                        $fullContent = $metaData['landing_harcourt_description'] ?? '';
                        $plainText   = trim(strip_tags($fullContent));
                        $words       = preg_split('/\s+/', $plainText);

                        $wordLimit = 67;
                        $isLong = count($words) > $wordLimit;

                        $shortText = implode(' ', array_slice($words, 0, $wordLimit));
                        ?>

                        <div class="harcourt-description" id="harcourtDesc">
                            
                            <div class="harcourt-short">
                                <?php echo nl2br(e($shortText)); ?><?php echo e($isLong ? ' ' : ''); ?>

                            </div>

                            
                            <div class="harcourt-full">
                                <?php echo $fullContent; ?>

                            </div>

                            <?php if($isLong): ?>
                            <button type="button" class="read-toggle" id="readToggle">
                                Read more >
                            </button>
                            <?php endif; ?>
                        </div>

                        
                        <div class="action-btn d-flex flex-wrap align-items-center gap-2">
                            <a class="login" href="<?php echo e(route('login')); ?>">Log In</a>
                            <a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 order-lg-2 order-1">
                <div class="supporting-img1">
                    <?php if(!empty($metaData['landing_harcourt_image_1']) && Storage::disk('public')->exists($metaData['landing_harcourt_image_1'])): ?>
                    <img src="<?php echo e(Storage::url($metaData['landing_harcourt_image_1'])); ?>" alt="Harcourt Support 1">
                    <?php else: ?>
                    <img src="<?php echo e(asset('images/harcourt-img-1.jpg')); ?>" alt="Default Support 1">
                    <?php endif; ?>
                </div>
                <div class="supporting-img2">
                    <?php if(!empty($metaData['landing_harcourt_image_2']) && Storage::disk('public')->exists($metaData['landing_harcourt_image_2'])): ?>
                    <img src="<?php echo e(Storage::url($metaData['landing_harcourt_image_2'])); ?>" alt="Harcourt Support 2">
                    <?php else: ?>
                    <img src="<?php echo e(asset('images/harcourt-img-2.jpg')); ?>" alt="Default Support 2">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php echo $__env->make('consumer.blocks.social-feeds', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.read-more-circle');
        if (!btn) return;
        const targetId = btn.dataset.target;
        const content = document.getElementById(targetId);
        if (!content) return;
        content.classList.toggle('active');
        btn.classList.toggle('active');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('harcourtDesc');
        const button = document.getElementById('readToggle');

        if (!container || !button) return;

        button.addEventListener('click', function () {
            const expanded = container.classList.toggle('expanded');
            button.textContent = expanded ? 'Read less <' : 'Read more >';
        });
    });
</script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.consumer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/landingpage.blade.php ENDPATH**/ ?>