<?php
    $searchTerm  = request('search');
    $business_id = request('busness');
    $hasActiveCategory = false;
?>

<div class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 tabs-content-col">
                <nav class="categories-tabs">
                    <div class="scroll-wrapper">
                        <button class="scroll-btn left" aria-label="Scroll left">‹</button>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <?php $__currentLoopData = $categories->sortBy('position'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $organizations        = $category->offers->groupBy('organisation_id');
                                    $organizationIdsArray = $organizations->keys()->toArray();
                                    $totalOrgs            = $organizations->count();
                                    $isActive             = false;

                                    if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                        $isActive = true; $hasActiveCategory = true;
                                    } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                        $isActive = true; $hasActiveCategory = true;
                                    } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrgs > 0) {
                                        $isActive = true; $hasActiveCategory = true;
                                    }

                                    $hasOrgClass = (!empty($searchTerm) && $totalOrgs > 0) ||
                                                   (!empty($business_id) && in_array($business_id, $organizationIdsArray));
                                ?>

                                <button
                                    class="nav-link <?php echo e($isActive ? 'active' : ''); ?> <?php echo e($hasOrgClass ? 'has-org' : ''); ?>"
                                    id="nav-<?php echo e($category->id); ?>-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-<?php echo e($category->id); ?>"
                                    type="button"
                                    role="tab"
                                    data-category-id="<?php echo e($category->id); ?>"
                                    data-load-url="<?php echo e(route('category.tab.offers', $category->id)); ?>"
                                    data-offerclass="<?php echo e($offerclass ?? ''); ?>"
                                    aria-controls="nav-<?php echo e($category->id); ?>"
                                    aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                                >
                                    <?php if(!empty($category->icon)): ?>
                                        <img src="<?php echo e(Str::startsWith($category->icon, ['http://','https://']) ? $category->icon : Storage::url($category->icon)); ?>" alt="<?php echo e($category->name); ?> icon">
                                    <?php endif; ?>
                                    <p><?php echo e(strtoupper($category->name)); ?></p>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button class="scroll-btn right" aria-label="Scroll right">›</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    <?php $hasActiveCategory = false; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $categories->sortBy('position'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $organizations        = $category->offers->groupBy('organisation_id');
                            $organizationIdsArray = $organizations->keys()->toArray();
                            $totalOrgs            = $organizations->count();
                            $isActive             = false;

                            if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                $isActive = true; $hasActiveCategory = true;
                            } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                $isActive = true; $hasActiveCategory = true;
                            } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrgs > 0) {
                                $isActive = true; $hasActiveCategory = true;
                            }
                        ?>

                        <div class="tab-pane fade <?php echo e($isActive ? 'show active' : ''); ?>"
                             id="nav-<?php echo e($category->id); ?>"
                             role="tabpanel"
                             aria-labelledby="nav-<?php echo e($category->id); ?>-tab"
                             tabindex="0">
                            <div class="text-center py-5 tab-spinner">
                                <div class="spinner-border text-warning" role="status"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="row">
                            <div class="col-lg-12 text-center my-5">
                                <p>No offers found that match your search.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/category-organisations-offers-tab.blade.php ENDPATH**/ ?>