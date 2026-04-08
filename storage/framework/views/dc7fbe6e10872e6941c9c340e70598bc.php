 <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
            <?php echo e(config('app.name', 'Laravel')); ?>

            <img src="<?php echo e(asset('/images/logo.svg')); ?>">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo e(__('Toggle navigation')); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                <?php if(auth()->guard()->guest()): ?>
                <?php if(Route::has('login')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                </li>
                <?php endif; ?>

                <?php if(Route::has('register')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('register')); ?>"><?php echo e(__('Register')); ?></a>
                </li>
                <?php endif; ?>
                <?php else: ?> 


                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                </li>

                <?php if(Auth::user()->level === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('admin.statsdashboard')); ?>">
                        <?php echo e(__('Stats')); ?>

                    </a>
                </li>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view offers')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('offers.index')); ?>"><?php echo e(__('Offers')); ?>

                        <?php
                        $offersCount = get_offers_count();
                        ?>
                        <?php if($offersCount > 0): ?>
                        <span class="badge"><?php echo e($offersCount); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view organisations')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('organisations.index')); ?>"><?php echo e(__('Businesses')); ?>

                        <?php
                        $organisationsCount = get_organisations_count();
                        ?>
                        <?php if($organisationsCount > 0): ?>
                        <span class="badge"><?php echo e($organisationsCount); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view promo_codes')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('promo_codes.index')); ?>"><?php echo e(__('Promo Codes')); ?></a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view prize_draws')): ?>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownprizedraws" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                       Draws
                   </a>
                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownprizedraws">
                    <a class="dropdown-item" href="<?php echo e(route('prize_draws.index')); ?>"><?php echo e(__('Prize Draws')); ?></a>
                    <a class="dropdown-item" href="<?php echo e(route('entries.index')); ?>"><?php echo e(__('Prize Draws Entries')); ?></a>
                    <a class="dropdown-item" href="<?php echo e(route('admin.prizedrawlog')); ?>"><?php echo e(__('Prize Draw Logs')); ?></a>
                </div>
            </li> 
            <?php endif; ?>




               
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view faqs')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('faqs.index')); ?>"><?php echo e(__('FAQs')); ?></a>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view categories', 'view users', 'view admins'])): ?>
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Platform Setup
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?php echo e(route('plans.index')); ?>"><?php echo e(__('Subscription Plans')); ?></a>
                        <a class="dropdown-item" href="<?php echo e(route('pages.index')); ?>"><?php echo e(__('Pages')); ?></a>
                        <a class="dropdown-item" href="<?php echo e(route('google-reviews.index')); ?>"><?php echo e(__('Google reviews')); ?></a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view categories')): ?>
                        <a class="dropdown-item" href="<?php echo e(route('categories.index')); ?>"><?php echo e(__('Categories')); ?></a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="<?php echo e(route('events.index')); ?>"><?php echo e(__('Events')); ?></a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view users')): ?>
                        <a class="dropdown-item" href="<?php echo e(route('users.index')); ?>"><?php echo e(__('Users')); ?></a>
                        <a class="dropdown-item" href="<?php echo e(route('unregisteredusers.index')); ?>"><?php echo e(__('Unregistere Users')); ?></a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view admins')): ?>
                        <a class="dropdown-item" href="<?php echo e(route('admins.index')); ?>"><?php echo e(__('Admins')); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </li>
                
                 

                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <?php echo e(Auth::user()->name); ?>

                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?php echo e(route('admin.logout')); ?>"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <?php echo e(__('Logout')); ?>

                    </a>

                    <form id="logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</nav><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/includes/navbar.blade.php ENDPATH**/ ?>