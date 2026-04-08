<header>
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-12 head">
                    <div class="header-inner">
                        <div class="logo">
                            <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                                <?php echo e(config('app.name', 'Laravel')); ?>

                                <img src="<?php echo e(asset('images/logo.svg')); ?>">
                            </a>
                        </div>
                        <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->level === 'member' || Auth::user()->level === 'business'): ?>
                        <div class="auth-log-in"> <p class="mb-0">Hi,<span><?php echo e(Auth::user()->name); ?></span></p></div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="navbar">
                            <ul class="main-menu"> 
                                <li class="<?php echo e(Request::is('/') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(url('/')); ?>">Home</a>
                                </li>
                                <li class="<?php echo e(Request::routeIs('about_us') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('about_us')); ?>">About Us</a>
                                </li>

                                <li class="<?php echo e(Request::routeIs('packages') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('packages')); ?>">Giveaways</a>
                                </li>

                                <li class="<?php echo e(Request::routeIs('shop') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('shop')); ?>">Discounts</a>
                                </li>

                                <li class="<?php echo e(Request::routeIs('businessportal') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('businessportal')); ?>">Business Portal</a>
                                </li>

                                <?php if(auth()->guard()->guest()): ?>
                                <li class="d-block d-xl-none <?php echo e(Request::routeIs('login') ? 'active' : ''); ?>">
                                    <a class="login" href="<?php echo e(route('login')); ?>"><?php echo e(__('Log In')); ?></a>
                                </li>
                                <li class="d-block d-xl-none <?php echo e(Request::routeIs('register') ? 'active' : ''); ?>">
                                    <a  class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">
                                        <?php echo e(__('Sign Up')); ?>

                                    </a>
                                </li>
                                <?php else: ?>
                               <li class="d-block d-xl-none"> <a class="login" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                </form>?</li>
                                <?php endif; ?>

                            </ul>
                        </div>
                        <?php echo $__env->make('global.nav-login-signup-btn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <div class="menu_icon">
                            <span></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>

<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/includes/consumer-navbar.blade.php ENDPATH**/ ?>