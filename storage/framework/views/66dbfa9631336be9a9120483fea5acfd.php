 <div class="header-right d-xl-flex d-none">
 	<?php if(auth()->guard()->guest()): ?>
 	<?php if(Route::has('login')): ?>
 	<a class="login" href="<?php echo e(route('login')); ?>"><?php echo e(__('Log In')); ?></a>
 	<?php endif; ?>
 	<?php if(Route::has('register')): ?>
 	
    <a href="#" class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal"><?php echo e(__('Sign Up')); ?></a>
 	<?php endif; ?>
 	<?php else: ?> 
 	<?php if(Auth::user()->level === 'member'): ?> 
 	<div class="user-wrapper me-1">
        <div class="notifiy-dropdown dropdown">
            <a href="#" class="text-dark position-relative" id="notifyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <?php if(Auth::user()->unreadNotifications->count() > 0): ?>
                <span class="notification-count position-absolute border border-light rounded-circle">
                    <?php echo e(Auth::user()->unreadNotifications->count()); ?>

                </span>
                <?php endif; ?>
            </a>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                
                <?php if(Auth::user()->unreadNotifications->count() > 0): ?>
                <div class="sec-head d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-uppercase text-white">NOTIFICATIONS</h6>
                    <a href="<?php echo e(route('notifications.markAllAsRead')); ?>" class="text mb-0">Mark All As Read</a>
                </div>
                <?php endif; ?>
                
                <div class="notification-wrapper">
                    <?php $__empty_1 = true; $__currentLoopData = Auth::user()->unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="notify-box">
                        <a href="<?php echo e(route('notifications.read', $notification->id)); ?>">
                            <div class="inner-content d-flex align-items-center gap-2">
                                <img src="<?php echo e(asset('images/bell-regular.svg')); ?>" class="img-fluid">

                                <div>
                                    <h6 class="text-white text-uppercase mb-0"> <?php echo e($notification->data['title']); ?></h6>
                                    <p class="text-white mb-0">
                                        <?php echo e($notification->data['message']); ?>

                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="notify-box">
                        <p class="text-white mb-0 text-center">No new notifications</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


 		


       <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
           <?php if(Auth::user()->profile_image): ?>
           <img  src="<?php echo e(Storage::url(Auth::user()->profile_image)); ?>" alt="Profile Image" class="user-img me-2 previewImage">
           <?php else: ?>
           <img src="<?php echo e(asset('images/user.svg')); ?>" alt="User" class="user-img me-2 previewImage">
           <?php endif; ?>
       </a>
       <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
           <li><a class="dropdown-item" href="<?php echo e(route('subscriber.dashboard.index')); ?>">Dashboard</a> </li>
           <li><a class="dropdown-item" href="<?php echo e(route('subscription.manage')); ?>">Your Packages & Subscriptions</a> </li>
           <li><a class="dropdown-item" href="<?php echo e(route('logout')); ?>" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                <?php echo csrf_field(); ?>
            </form></li>
            
        </ul>
    </div>
</div>
<?php endif; ?>
<?php if(Auth::user()->level === 'business'): ?> 
<div class="">
 <a href="<?php echo e(route('business.myaccount.index')); ?>" class="d-flex align-items-center text-decoration-none" >
    <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="User" class="user-img me-2 previewImage">
</a>
</div>
<?php endif; ?>
<?php endif; ?>


<div class="coming-soon-event">
    <i class="bi bi-list" data-bs-toggle="offcanvas" data-bs-target="#ComingSoonMenu" aria-controls="ComingSoonMenu"></i>

    <!-- Side Menu Content -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="ComingSoonMenu" aria-labelledby="ComingSoonMenuLabel">
      <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
        <div class="img-wrapper text-center">
            <img src="/images/footer-logo.svg">
            <p class="mb-0">Don't forget to breathe...</p>
        </div>
        <div class="sec-btns">
            <a href="/events" class="btn d-block">Events</a>
            <a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Mechcandise</a>
            <a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Charity</a>
        </div>
        <div class="auth-btns d-flex align-items-center justify-content-center gap-3 position-relative pb-5">

            <?php if(auth()->guard()->guest()): ?>
            <?php if(Route::has('login')): ?>
            <a class="login" href="<?php echo e(route('login')); ?>"><?php echo e(__('Log In')); ?></a>
            <?php endif; ?>
            <?php if(Route::has('register')): ?>
            <a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal"><?php echo e(__('Sign Up')); ?></a>
            <?php endif; ?>
            <?php else: ?> 
            <a class="login" href="<?php echo e(route('logout')); ?>" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                <?php echo csrf_field(); ?>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

</div>


<!-- Mobile User Wrapper -->
<div class="user-mob-wrapper user-wrapper d-xl-none d-flex">

    <div class="profile-wrapper dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if(auth()->guard()->check()): ?>
            <?php if(Auth::user()->profile_image): ?>
            <img src="<?php echo e(Storage::url(Auth::user()->profile_image)); ?>" alt="Profile Image" class="user-img me-2 previewImage">
            <?php else: ?>
            <img src="<?php echo e(asset('images/user.svg')); ?>" alt="User" class="user-img me-2 previewImage">
            <?php endif; ?>
            <?php else: ?>
            <img src="<?php echo e(asset('images/user.svg')); ?>" alt="User" class="user-img me-2 previewImage">
            <?php endif; ?>
        
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">

        <div class="text-center">
            <div class="dropdown-logo">
                <img src="<?php echo e(asset('/images/footer-logo.svg')); ?>">
            </div>
        </div>


        <?php if(Auth::check() && (Auth::user()->level === 'member' || Auth::user()->level === 'business')): ?>
        <div class="profile-info d-flex align-items-center">
            <?php if(Auth::user()->profile_image): ?>
            <img  src="<?php echo e(Storage::url(Auth::user()->profile_image)); ?>" alt="Profile Image" class="user-img me-2 previewImage">
            <?php else: ?>
            <img src="<?php echo e(asset('images/user.svg')); ?>" alt="User" class="user-img me-2 previewImage">
            <?php endif; ?>
            <div>
                <h6 class="text-white text-uppercase mb-0"><?php echo e(Auth::user()->name); ?></h6>
                <?php if(Auth::user()->level === 'member'): ?> 
                <a href="<?php echo e(route('subscriber.dashboard.index')); ?>" class="mb-0">View Profile</a>
                <?php endif; ?> 
            </div>
        </div>
    


    <?php else: ?>
    <div class="profile-info info-logout-user d-flex align-items-center">
        <img src="<?php echo e(asset('images/logout-user.svg')); ?>" alt="User" class="loggedin-user-img user-img me-1">
        <div>
            
            <a   data-bs-toggle="modal" data-bs-target="#SignUpModal">Sign Up</a> / <a href="<?php echo e(route('login')); ?>">Login</a>
        </div>
    </div>
    <?php endif; ?>





    <div class="mob-menu">
        <ul class="list-unstyled">
            <li>
                <a class="<?php echo e(Request::is('/') ? 'active' : ''); ?>" href="<?php echo e(url('/')); ?>"><span><img src="<?php echo e(asset('images/home-icon.svg')); ?>"></span> Home</a>
            </li>
            <li>
                <a class="<?php echo e(Request::routeIs('packages') ? 'active' : ''); ?>" href="<?php echo e(route('packages')); ?>"><span><img src="<?php echo e(asset('images/tags-solid-icon.svg')); ?>"></span> Giveaways</a>
            </li>
            <li>
                <a class="<?php echo e(Request::routeIs('discounts') ? 'active' : ''); ?>" href="<?php echo e(route('discounts')); ?>"><span><img src="<?php echo e(asset('images/piggy-bank-solid.svg')); ?>"></span> Discounts</a>
            </li>
            <li>
                <a class="<?php echo e(Request::routeIs('businessportal') ? 'active' : ''); ?>" href="<?php echo e(route('businessportal')); ?>"><span><img src="<?php echo e(asset('images/handshake-solid.svg')); ?>"></span> Business Portal</a>
            </li>
            <!-- <li>
                <a  class="<?php echo e(Request::routeIs('events') ? 'active' : ''); ?>" href="<?php echo e(route('events')); ?>"><span><img src="<?php echo e(asset('images/calendar-days-solid.svg')); ?>"></span> Events</a>
            </li> -->
            <li>
                <a  class="<?php echo e(Request::routeIs('events') ? 'active' : ''); ?>" href="#"><span><img src="<?php echo e(asset('images/calendar-days-solid.svg')); ?>"></span> Coming Soon</a>
                <ul class="coming-soon-submenu p-2 text-center mt-3">
                    <li><a href="/events" class="btn d-block">Events</a></li>
                    <li><a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Mechcandise</a></li>
                    <li><a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Charity</a></li>
                </ul>
            </li>
            <li>
                <a class="<?php echo e(Request::routeIs('supportandlegal') ? 'active' : ''); ?>" href="<?php echo e(route('supportandlegal')); ?>"><span><img src="<?php echo e(asset('images/scale-balanced-solid.svg')); ?>"></span> Support & Legal</a>
            </li>

            <?php if(Auth::check() && (Auth::user()->level === 'member' || Auth::user()->level === 'business')): ?>
            <li>
                <a href="<?php echo e(route('logout')); ?>" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span><img src="<?php echo e(asset('images/right-from-bracket-solid.svg')); ?>"></span>  Logout</a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </li>
            <?php else: ?>
            <li>
                <a class="<?php echo e(Request::routeIs('login') ? 'active' : ''); ?>" href="<?php echo e(route('login')); ?>"><span><img src="<?php echo e(asset('images/right-from-bracket-solid.svg')); ?>"></span> Login</a>
            </li>
            <?php endif; ?>



        </ul>
    </div>
</div>
</div>
</div>

<!-- Modal -->
<div class="modal welcomeModal comingSoonModal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      <div class="modal-body p-0">
        <div class="row mx-0">
            <div class="col-sm-6 px-0">
                <div class="welcome-wrapper h-100 text-center">
                    <div class="welcome-img mx-3">
                        <div class="mob-logo">
                            <img src="images/logo.svg" class="img-fluid d-sm-none d-block">
                        </div>
                        <div class="sec-head text-uppercase text-center">Coming Soon</div>
                        <p class="mb-0 d-sm-block d-none">Keep and eye out for these new and exciting Xhale features.</p>                                
                    </div>
                </div>
            </div>
            <div class="col-sm-6 px-0">
                <div class="welcome-content text-center h-100">
                    <div class="modal-logo text-center mb-5 d-sm-block d-none">
                        <img src="images/logo-light.svg" class="img-fluid mx-auto">
                        <p class="mt-3 mb-0 text-white">Don't forget to breath...</p>
                    </div>
                    <div class="action-btns">
                        <p class="mb-4 text-white d-sm-none d-block">Keep and eye out for these new and exciting Xhale features.</p>
                        <a href="#" class="d-block signup">Events</a>
                        <a href="#" class="d-block login business-signup-btn my-3">Mechcandise</a>
                        <a href="#" class="d-block login">Charity</a>
                        <span class="skip-text mt-3 mb-0 text-uppercase">
                            <?php if(auth()->guard()->guest()): ?>
                            <?php if(Route::has('login')): ?>
                            <a href="<?php echo e(route('login')); ?>"><?php echo e(__('Log In')); ?></a>
                            <?php endif; ?>
                            <?php if(Route::has('register')): ?>
                            / 
                            <a   data-bs-toggle="modal" data-bs-target="#SignUpModal"><?php echo e(__('Sign Up')); ?></a>
                            <?php endif; ?>
                            <?php else: ?> 
                            <a  href="<?php echo e(route('logout')); ?>" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                            <?php endif; ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>





<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/global/nav-login-signup-btn.blade.php ENDPATH**/ ?>