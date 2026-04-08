<?php
    $organizations = $category->offers->groupBy('organisation_id');
    $totalOrganizationsInCurrentCategory = $organizations->count();
    $organizationsToShowInitially = 15;

    $sliderCount = 0;
    if ($totalOrganizationsInCurrentCategory >= 8 && $totalOrganizationsInCurrentCategory <= 9) {
        $sliderCount = 1;
    } elseif ($totalOrganizationsInCurrentCategory >= 14 && $totalOrganizationsInCurrentCategory <= 20) {
        $sliderCount = 2;
    } elseif ($totalOrganizationsInCurrentCategory >= 21) {
        $sliderCount = 3;
    }

    $organizationChunks = $sliderCount > 0
        ? $organizations->chunk(ceil($totalOrganizationsInCurrentCategory / $sliderCount))
        : collect([$organizations]);
?>

<div class="tab-content-inner">
    <h2 class="d-flex align-items-center gap-2">
        <img src="<?php echo e(asset('images/dumble-icon.svg')); ?>" class="head-icon" alt="Category Icon">
        <?php echo e(strtoupper($category->name)); ?>

    </h2>

    <?php if($organizations->isNotEmpty()): ?>

        
        <div class="row offers-container desktop-view d-lg-grid d-none">
            <?php $loopcheck = 0; ?>
            <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orgId => $offers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $organization = optional($offers->first())->organisation;
                    if (!$organization) continue;

                    $offerIds        = $offers->pluck('id')->implode(',');
                    $firstOffer      = $offers->first();
                    $firstAttachment = optional($firstOffer)->attachments->first();
                    $offerPercentage = optional($firstOffer)->percentage_off;

                    $organizationLogo = asset('images/ubx.png');
                    if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                        $organizationLogo = Storage::url($organization->image);
                    }

                    $imageUrl      = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
                    $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                ?>

                <div class="<?php echo e($loopcheck); ?> col offer-item-col <?php echo e($loopcheck >= $organizationsToShowInitially ? 'd-none' : ''); ?>">
                    <div class="offer-col <?php echo e($offerclass ?? ''); ?> <?php echo e($isHighlighted ? 'highlighted' : ''); ?>" data-id="<?php echo e($organization->id); ?>" data-offer-ids="<?php echo e($offerIds); ?>">
                        <?php if($offerPercentage > 0): ?>
                            <div class="offer-percent"><?php echo e(round($offerPercentage)); ?>% Off</div>
                        <?php endif; ?>
                        <div class="offer-col-inner">
                            <div class="ubx-col">
                                <img src="<?php echo e($organizationLogo); ?>" alt="<?php echo e($organization->title); ?> Logo">
                                <span><?php echo e($organization->title); ?></span>
                            </div>
                            <div class="offer-col-img">
                                <img data-src="<?php echo e($imageUrl); ?>" class="cat-offer-img lazy lozad" alt="Offer Image"
                                     onerror="this.onerror=null;this.src='<?php echo e(asset('images/ubx.png')); ?>'">
                            </div>
                            <div class="ubx-bottom"><?php echo e($organization->title); ?></div>
                        </div>
                    </div>
                </div>
                <?php $loopcheck++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="mobile-slider d-lg-none">
            <?php $__currentLoopData = $organizationChunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunkIndex => $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="offer-slide offer-slide-<?php echo e($category->id); ?>-<?php echo e($chunkIndex); ?>">
                    <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orgId => $offers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $organization = optional($offers->first())->organisation;
                            if (!$organization) continue;

                            $offerIds        = $offers->pluck('id')->implode(',');
                            $firstOffer      = $offers->first();
                            $firstAttachment = optional($firstOffer)->attachments->first();
                            $offerPercentage = optional($firstOffer)->percentage_off;

                            $organizationLogo = asset('images/ubx.png');
                            if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                                $organizationLogo = Storage::url($organization->image);
                            }

                            $imageUrl      = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
                            $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                        ?>

                        <div class="offer-item <?php echo e($offerclass ?? ''); ?> <?php echo e($isHighlighted ? 'highlighted' : ''); ?>" data-id="<?php echo e($organization->id); ?>" data-offer-ids="<?php echo e($offerIds); ?>">
                            <?php if($offerPercentage > 0): ?>
                                <div class="offer-percent"><?php echo e(round($offerPercentage)); ?>% Off</div>
                            <?php endif; ?>
                            <div class="offer-col-inner">
                                <div class="ubx-col">
                                    <img src="<?php echo e($organizationLogo); ?>" alt="<?php echo e($organization->title); ?> Logo">
                                    <span><?php echo e($organization->title); ?></span>
                                </div>
                                <div class="offer-col-img">
                                    <img src="<?php echo e($imageUrl); ?>" class="cat-offer-img lazy" alt="Offer Image"
                                         onerror="this.onerror=null;this.src='<?php echo e(asset('images/ubx.png')); ?>'">
                                </div>
                                <div class="ubx-bottom"><?php echo e($organization->title); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($totalOrganizationsInCurrentCategory > $organizationsToShowInitially): ?>
            <div class="action-btn text-center mt-4 d-lg-inline-block d-none">
                <a class="load-all-offers-btn signup" data-target-tab="#nav-<?php echo e($category->id); ?>">View More</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="no-offers-message-container">
            <div class="col-12">
                <p>No organizations available for this category yet.</p>
            </div>
        </div>
    <?php endif; ?>
</div><?php /**PATH /Applications/MAMP/htdocs/laravel/xhalenew/resources/views/consumer/blocks/category-organisations-offers-tab-partial.blade.php ENDPATH**/ ?>