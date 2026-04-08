<?php $__env->startSection('content'); ?>

<div class="section-wrapped">



    <!-- Win Banner Sec Start -->
    <div class="banner-outer-wrapper">

     


<div class="desktop-win-banner-slider win-banner-slider d-none d-md-block">
    <?php
        $dynamicSlides = json_decode($metaData['hero_dynamic_slider'] ?? '[]', true);
    ?>
    
    <?php if(!empty($dynamicSlides)): ?>
        <?php $__currentLoopData = $dynamicSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="win-banner-sec win-banner-sec-<?php echo e($loop->iteration); ?>" 
                 <?php if(!empty($slide['bg_image'])): ?>
                 style="background-image: url('<?php echo e(Storage::url($slide['bg_image'])); ?>');"
                 <?php endif; ?>
            >
                <div class="container">
                    <div class="win-mazda">
                        <?php if(!empty($slide['fg_image'])): ?>
                            <img src="<?php echo e(Storage::url($slide['fg_image'])); ?>" alt="<?php echo e($slide['text'] ?? 'Win'); ?>">
                        <?php endif; ?>
                    </div>

                    <?php if(!empty($slide['text'])): ?>
                            <div class="win-banner-text"> <?php echo $slide['text']; ?> </div>
                        <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        
        <div class="win-banner-sec"
             style="background-image: url('<?php echo e(asset('images/sararh-with-car.webp')); ?>');">
            <div class="container">
                <div class="win-mazda">
                    <img src="/images/deserve-win-img.png" alt="Win">
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="mobile-win-banner-slider win-banner-slider d-block d-md-none">
    <?php if(!empty($dynamicSlides)): ?>
        <?php $__currentLoopData = $dynamicSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php
                $backgroundImage = null;

                if (!empty($slide['bg_mobile_image'])) {
                    $backgroundImage = Storage::url($slide['bg_mobile_image']);
                } elseif (!empty($slide['bg_image'])) {
                    $backgroundImage = Storage::url($slide['bg_image']);
                }
            ?>

            <div class="win-banner-sec win-banner-sec-<?php echo e($loop->iteration); ?>"
                <?php if($backgroundImage): ?>
                    style="background-image: url('<?php echo e($backgroundImage); ?>');"
                <?php endif; ?>
            >
                <div class="container">
                    <div class="win-mazda">
                        <?php if(!empty($slide['fg_image'])): ?>
                            <img src="<?php echo e(Storage::url($slide['fg_image'])); ?>" alt="<?php echo e($slide['text'] ?? 'Win'); ?>">
                        <?php endif; ?>
                    </div>

                    <?php if(!empty($slide['text'])): ?>
                        <div class="win-banner-text">
                            <?php echo $slide['text']; ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        
        <div class="win-banner-sec"
             style="background-image: url('<?php echo e(asset('images/sararh-with-car.webp')); ?>');">
            <div class="container">
                <div class="win-mazda">
                    <img src="/images/deserve-win-img.png" alt="Win">
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>




        

        <!-- Slider Banner Content -->
        <!-- <div class="win-banner-slider-content">
            <div class="container">
                <div class="inner-content">
                    <div class="row align-items-end">
                        <div class="banner-outer-content">
                            <div class="container">
                                <div class="hero-content text-center" >
                                    <div class="bg-title">
                                        <img src="<?php echo e(!empty($metaData['hero_img_2']) ? asset('storage/' . $metaData['hero_img_2']) : asset('images/default-hero2.jpg')); ?>" alt="Hero Image 2">
                                        
                                    </div>
                                    <div class="banner-auth-box">
                                        <p class="position-relative">
                                            Join the draw today and every dollar of profit goes directly to supporting the Harcourt community impacted by the recent Victorian bushfires.
                                            
                                        </p>

                                            
                                  


                                    </div>
                                </div>
                            </div>
                        </div>


                        


                        

                        
                    </div>            
                </div>
            </div>
        </div> -->
    </div>

    <!-- Pricing Plan Section Start -->
    <section class="pricing-plan below-win gray-bg mt-0 pt-sm-5 pt-0">
        <div>
            <img src="<?php echo e(asset('images/notesImg.webp')); ?>" class="money-img d-lg-block d-none" alt="Money Notes">
            <img src="<?php echo e(asset('images/vegBasket-img.webp')); ?>" class="basket-img d-lg-block d-none" alt="Vegetable Basket">
        </div>
        <div class="container">
            

           <div class="access-column pt-5 mt-lg-2 position-relative z-1">
            <?php echo $__env->make('consumer.blocks.pop-up-subscription-package-list', ['customRegister' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          </div> 

            <div class="col-12 text-center mt-1 mb-3 d-lg-block d-none">
                <div class="action-btn">
                    <?php echo $__env->make('global.login-signup-btn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </section>

   
    <!-- Pricing Plan Section End -->

</div>


<div class="access-column popup-wrapper">
    <?php echo $__env->make('consumer.blocks.pop-up-subscription', ['customRegister' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div> 

<!-- Mobile Google Review Section -->
<div class="reviw-mob-sec d-lg-none d-none">
    <div class="container-fluid px-0">
        <?php echo $__env->make('consumer.blocks.google-review', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>

<?php echo $__env->make('consumer.blocks.cash-giveaway', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 



<!-- Categories & Offers Section -->
<section class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="highlight-title d-lg-flex d-none">
                    <h2>SHOP BY <span>CATEGORY</span></h2>
                    <a href="<?php echo e(route('consumer.allcategory')); ?>">EXPLORE ALL CATEGORIES</a>
                </div>
                <div class="bg-title small-title d-lg-none d-block">
                    <div class="yellow-bg">SHOP BY</div>
                    <div class="black-bg">CATEGORY</div>
                </div>
            </div>
            <?php echo $__env->make('consumer.blocks.category-organisations-offers-tab', ['categories' => $categories ,'offerclass' => 'home-offer-detail' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</section>

<!-- How it Works Tabs Section -->
<section class="second-tabs-sec pt-lg-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="work-tabs-inner">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php $__currentLoopData = $tabData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e($loop->first ? 'active' : ''); ?>"
                                id="<?php echo e($tabKey); ?>-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#<?php echo e($tabKey); ?>-tab-pane"
                                type="button"
                                role="tab">
                                <?php echo e($tab['title_line1'] ?? ucfirst($tabKey)); ?>

                            </button>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <!-- Tabs Content -->
                    <div class="tab-content" id="myTabContent">
                        <?php $__currentLoopData = $tabData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>"
                            id="<?php echo e($tabKey); ?>-tab-pane"
                            role="tabpanel">
                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-12 order-lg-1 order-2">
                                        <div class="work-tab-img">
                                            <?php if(!empty($tab['image'])): ?>
                                            <img class="lazy lozad" data-src="<?php echo e(Str::startsWith($tab['image'], ['http://','https://']) ? $tab['image'] : asset('storage/' . $tab['image'])); ?>" alt="<?php echo e($tab['title_line1'] ?? 'Work Tab Image'); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12 order-lg-2 order-1">
                                        <div class="work-tab-content text-lg-start text-center">
                                            <div class="highlight-title">
                                                <h3><?php echo $tab['title_line2'] ?? ''; ?></h3>
                                            </div>
                                            <?php echo $tab['paragraph'] ?? ''; ?>

                                            <div class="action-btn d-flex flex-wrap align-items-center justify-content-center gap-2">
                                                <?php echo $__env->make('global.login-signup-btn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section with two sliders -->
<section class="partners-sec darkgray-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="bg-title small-title">
                    <div class="yellow-bg">OUR BUSINESS</div>
                    <div class="black-bg">PARTNERS</div>
                </div>
            </div>
        </div>
    </div>
    <div class="partner-slider-top partner-slider" dir="rtl">
        <?php $__currentLoopData = $organisations_part1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!empty($organisation->image)): ?>
        <div class="parner-logo">
            <a href="<?php echo e(Auth::check() ? route('discounts', ['busness' => $organisation->id]) : route('login')); ?>" target="_blank">
                <img class="lazy " src="<?php echo e(Str::startsWith($organisation->image, ['http://','https://']) ? $organisation->image : Storage::url($organisation->image)); ?>" alt="<?php echo e($organisation->name); ?> logo">
            </a>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="partner-slider-bottom partner-slider">
        <?php $__currentLoopData = $organisations_part2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!empty($organisation->image)): ?>
        <div class="parner-logo">
            <a href="<?php echo e(Auth::check() ? route('discounts', ['busness' => $organisation->id]) : route('login')); ?>" target="_blank">
                <img class="lazy " src="<?php echo e(Str::startsWith($organisation->image, ['http://','https://']) ? $organisation->image : Storage::url($organisation->image)); ?>" alt="<?php echo e($organisation->name); ?> logo">
            </a>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<!-- Video Review Section -->
<section class="video-review d-lg-block d-none">
    <div class="row g-0 align-items-center">
        <div class="col-md-6 col-12">
            <div class="video">
                <div class="ratio ratio-16x9">
                    <?php if(!empty($metaData['hero_video_src'])): ?>
                    <video 
                    class="video w-full rounded-md shadow-md" 
                    poster="<?php echo e(!empty($metaData['hero_video_poster']) ? Storage::url($metaData['hero_video_poster']) : asset('images/hero-img.jpg')); ?>" 
                    src="<?php echo e(Storage::url($metaData['hero_video_src'])); ?>" 
                    controls preload="metadata" controlslist="nodownload">
                </video>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <?php echo $__env->make('consumer.blocks.google-review', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
</section>

<!-- Social Feeds & Stripe Sections -->
<?php echo $__env->make('consumer.blocks.social-feeds', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>




<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.consumer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/home.blade.php ENDPATH**/ ?>