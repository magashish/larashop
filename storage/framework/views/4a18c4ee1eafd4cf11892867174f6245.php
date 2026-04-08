<div class="col-12 d-lg-none d-block">
    <div class="footer-mob">
        <ul class="list-unstyled d-flex align-items-center">
            <li><a href="<?php echo e(route('discounts')); ?>" class="menu-item <?php echo e(Request::routeIs('discounts') ? 'active' : ''); ?>"><img src="<?php echo e(asset('images/cart-icon.svg')); ?>"></a></li>
            <li><a href="<?php echo e(route('events')); ?>" class="menu-item <?php echo e(Request::routeIs('events') ? 'active' : ''); ?>"><img src="<?php echo e(asset('images/calendar-days-solid.svg')); ?>"></a></li>
            <li>
                <a href="<?php echo e(url('/')); ?>" class="menu-logo <?php echo e(Request::is('/') ? 'active' : ''); ?>"><img src="<?php echo e(asset('images/exhale--logo.png')); ?>"></a>
                <?php if(auth()->guard()->check()): ?>
                <?php if(Auth::user()->level === 'member'): ?>
                <a href="<?php echo e(route('subscriber.dashboard.index')); ?>" class="menu-item live-stream-icon"><img src="<?php echo e(asset('images/live-streaming.svg')); ?>"></a>
                <?php endif; ?>
                <?php if(Auth::user()->level === 'business'): ?>
                <a href="<?php echo e(route('subscriber.dashboard.index')); ?>" class="menu-item live-stream-icon"><img src="<?php echo e(asset('images/live-streaming.svg')); ?>"></a>
                <?php endif; ?>
                <?php endif; ?>
            </li>
            <li>
                <a href="<?php echo e(route('business.myaccount.index')); ?>" class="menu-item user-icon"><img src="<?php echo e(asset('images/user-regular.svg')); ?>"></a>
                <a href="<?php echo e(route('discounts')); ?> <?php echo e(Request::routeIs('discounts') ? 'active' : ''); ?>" class="menu-item search-icon"><img src="<?php echo e(asset('images/search-icon.svg')); ?>"></a>
            </li>
            <li>
                <span class="menu-item footer-menu-icon"><img src="<?php echo e(asset('images/hamburer-icon.png')); ?>"></span>
                <!-- <?php if(auth()->guard()->check()): ?>
                <?php if(Auth::user()->level === 'member'): ?>
                <a href="<?php echo e(route('subscriber.dashboard.index')); ?>" class="menu-item live-stream-icon"><img src="<?php echo e(asset('images/live-streaming.svg')); ?>"></a>
                <?php endif; ?>
                <?php if(Auth::user()->level === 'business'): ?>
                <a href="<?php echo e(route('subscriber.dashboard.index')); ?>" class="menu-item live-stream-icon"><img src="<?php echo e(asset('images/live-streaming.svg')); ?>"></a>
                <?php endif; ?>
                <?php endif; ?>    -->   
            </li>
        </ul>
    </div>
</div>
<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/includes/mobile-footer-navbar.blade.php ENDPATH**/ ?>