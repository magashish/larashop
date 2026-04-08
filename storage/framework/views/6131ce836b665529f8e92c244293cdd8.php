<ul class="list-unstyled d-flex align-items-center justify-content-lg-start justify-content-center flex-wrap gap-1">
    <li class="<?php echo e(Request::is('/') ? 'active' : ''); ?>">
        <a href="<?php echo e(url('/')); ?>">Home</a>
    </li>
    <li class="<?php echo e(Request::routeIs('packages') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('packages')); ?>">Giveaways</a>
    </li>
    <li class="<?php echo e(Request::routeIs('discounts') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('discounts')); ?>">Discounts</a>
    </li>
    <li class="<?php echo e(Request::routeIs('businessportal') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('businessportal')); ?>">Business Portal</a>
    </li>
    <li class="<?php echo e(Request::routeIs('events') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('events')); ?>">Events</a>
    </li>
    <li class="<?php echo e(Request::routeIs('supportandlegal') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('supportandlegal')); ?>">Support & Legal</a>
    </li>
</ul><?php /**PATH /Applications/MAMP/htdocs/laravel/xhalenew/resources/views/includes/consumer-footer-navbar.blade.php ENDPATH**/ ?>