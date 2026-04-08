 	<?php if(auth()->guard()->guest()): ?>
 	<?php if(Route::has('login')): ?>
 	<a class="login" href="<?php echo e(route('login')); ?>"><?php echo e(__('Log In')); ?></a>
 	<?php endif; ?>
 	<?php if(Route::has('register')): ?>
 	<a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal"><?php echo e(__('Sign Up')); ?></a>
 	<?php endif; ?>
 	<?php else: ?> 
 	<?php if(Auth::user()->level === 'member'): ?> 
 	<a class="login" href="<?php echo e(route('subscriber.dashboard.index')); ?>">Dashboard</a> 
 	<?php endif; ?>
 	<?php if(Auth::user()->level === 'business'): ?> 
 	<a class="signup" href="<?php echo e(route('business.myaccount.index')); ?>">My Account</a>
 	<?php endif; ?>
 	<?php endif; ?>
<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/global/login-signup-btn.blade.php ENDPATH**/ ?>