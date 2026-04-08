<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <?php echo $__env->make('global.head-google-tag-manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <meta charset="utf-8">
    <meta name="fo-verify" content="55411b5e-09d7-4550-8c61-4ea5884199fd" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo e(asset('/images/exhale--logo.png')); ?>" sizes="48x48">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 

    <link defer href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link defer href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css" rel="stylesheet">




    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css">
    <link rel="stylesheet" type="text/css" href="https://www.xhale.com.au/build/assets/app-DbaZCfaT.css">
    <link rel="stylesheet" type="text/css" href="https://www.xhale.com.au/build/assets/style-ESbPOoOf.css">
    
    

    <!-- Vite CSS -->
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/sass/app.scss',
        'resources/css/slick.css',
        'resources/css/external.css',
        'resources/css/style.css'
        ]); ?>


    </head>
    <?php
    $pageClass = str_replace('/', '-', request()->path() ?: 'home');
    ?>
    <body class="<?php echo e(Auth::check() ? 'logged-in dashboard-header' : 'logged-out'); ?> page-<?php echo e($pageClass); ?> <?php echo e($bodyClass ?? ''); ?>" data-is-guest="<?php echo e(Auth::guest() ? 'true' : 'false'); ?>">

        <?php echo $__env->make('global.body-google-tag-manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="loader-wrapper">
            <div id="loader">
                <img src="<?php echo e(asset('/images/logo.svg')); ?>">
            </div>
        </div> 

        <?php if(auth()->guard()->guest()): ?>
        <div class="announcement-bar">
            <div class="container">
                <?php if(!empty($showLandingBar)): ?>
                <div class="announcement-bar-cash-giveaway">
                    <?php echo $__env->make('consumer.blocks.sticky-top-cash-giveaway-time', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->make('consumer.blocks.top-cash-giveaway-time', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php else: ?>
                <div class="announcement-bar-subscribe">
                <a href="<?php echo e(route('register')); ?>" class="announcement-link">
                    <p>Get More. Save More. Live Better. <strong>Subscribe Today!</strong></p>
                </a>
                <div class="top-social-links">
                    <?php echo $__env->make('includes.top-social-links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; ?>

        <div id="main_wrap">
            <div id="navbar_wrap">
                <?php echo $__env->make('includes.consumer-navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


            </div>
            <div id="site_wrap">
                <main>
                    <?php echo $__env->yieldContent('content'); ?>
                </main>
            </div>

            <div id="footer">

                <footer class="footer <?php if(Auth::check()): ?> logged-in  <?php else: ?> logged-out <?php endif; ?>">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-1 col-md-4 footer-logo-col">
                                <div class="footer-logo">
                                    <a  href="<?php echo e(url('/')); ?>">
                                        <img src="/images/footer-logo.svg">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-6">
                                <div class="footer-menu my-lg-0 my-4">
                                   <?php echo $__env->make('includes.consumer-footer-navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                   <?php echo $__env->make('includes.social-links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                               </div>
                           </div>
                           <div class="col-xl-3 col-lg-4 col-md-8 newsletter-col">
                            <div class="newsletter-wrapper">
                                <h6>Subscribe to Newsletter</h6>
                                <form id="newsletter-form">
                                    <div class="position-relative">
                                        <input type="email" name="email" id="newsletter-email" class="form-control" placeholder="Your email address here" required>
                                        <button type="submit" class="btn btn-primary">SUBSCRIBE</button>
                                    </div>
                                    <div id="newsletter-message" class="mt-2"></div>
                                </form>
                            </div>
                        </div>
                        <?php echo $__env->make('includes.mobile-footer-navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <style>

        .highlighted .offer-col-inner {
            box-shadow: 0 0 0 5px #FFD700, 0 4px 10px rgba(0, 0, 0, 0.4);
            border-radius: 20px;
        }

    </style>






<?php echo $__env->make('global.user-terms-accepted-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 

<!-- Modal -->
<?php if(!request()->routeIs('register')): ?>
<div class="modal welcomeModal SignUpModal fade" id="SignUpModal" tabindex="-1" aria-labelledby="SignUpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      <div class="modal-body p-0">
        <div class="row">
         <?php echo $__env->make('global.register-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
     </div>
 </div>
</div>
</div>
</div>
<?php endif; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<?php if(url()->current() !== url('/subscription-package')): ?>
<?php echo $__env->make('global.stripe', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>



<!-- FlipClock -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>





<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
<script>
   $(document).ready(function() {
    // Wrap all lozad images dynamically with loader
    $('.lozad').each(function() {
        if (!$(this).parent().hasClass('lozad-wrapper')) {
            $(this).wrap('<div class="lozad-wrapper w-100"></div>');
            $(this).parent().append('<div class="lozad-loader"></div>');
        }
    });

    // Initialize Lozad
    const observer = lozad('.lozad', {
        threshold: 0.1,
        loaded: function(el) {
            $(el).attr('data-loaded', 'true'); 
            $(el).fadeIn(500); 
            $(el).siblings('.lozad-loader').fadeOut(900, function() {
                $(this).remove();
            });
        }
    });

    observer.observe();
});

</script>


<?php echo $__env->yieldPushContent('scripts'); ?> 
<script>
    window.appConfig = {
        routes: {
            discounts: "<?php echo e(route('discounts')); ?>",
            register: "<?php echo e(route('register')); ?>",
            login: "<?php echo e(route('login')); ?>",
            loginCheckRedirection: "<?php echo e(route('logincheckredirection')); ?>",
            ajaxSubscribe: "<?php echo e(route('ajax.subscribe')); ?>"
        },
        csrfToken: "<?php echo e(csrf_token()); ?>"
    };
</script>



<!-- Vite JS -->
<?php echo app('Illuminate\Foundation\Vite')([
    'resources/js/app.js',
    'resources/js/consumer.js',
    'resources/js/flipclock.js'
    ]); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>


</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/laravel/xhalenew/resources/views/layouts/consumer.blade.php ENDPATH**/ ?>