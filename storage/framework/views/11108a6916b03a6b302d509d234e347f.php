<form id="payment-form-<?php echo e($formId); ?>"
    class="payment-form mt-4"
    data-form-id="<?php echo e($formId); ?>"
    action="<?php echo e(route('UserRegister')); ?>"
    method="POST">
    <?php echo csrf_field(); ?>

    
    <div class="row g-2">
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="name" id="name-<?php echo e($formId); ?>"
                    class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>"
                    placeholder="Full Name *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email-<?php echo e($formId); ?>"
                    class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('email')); ?>"
                    placeholder="Email Address *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" name="mobile" id="mobile-<?php echo e($formId); ?>"
                    class="form-control <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('mobile')); ?>"
                    placeholder="Phone Number *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
    </div>

    <?php
        $data = planPackageList();
        $subscriptions = $data['subscriptions'];
        $packages = $data['packages'];
    ?>

    <div class="package-price-wrapper mt-3" style="display: none;">
        <div class="item-list">
            <?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check mb-2">
                    <div class="position-relative">
                        <input class="form-check-input" type="radio"
                            name="selected_plan_id"
                            id="subscriptionRadio<?php echo e($subscription->id); ?>-<?php echo e($formId); ?>"
                            value="<?php echo e($subscription->id); ?>"
                            data-type="subscription"
                            <?php if($subscription->id === $formId): ?> checked <?php endif; ?>>
                        <label class="form-check-label d-flex justify-content-between"
                            for="subscriptionRadio<?php echo e($subscription->id); ?>-<?php echo e($formId); ?>">
                            <span class="package-name"><?php echo e($subscription->name); ?></span>
                            <span class="price">$<?php echo e(number_format($subscription->price, 2)); ?></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check mb-2">
                    <div class="position-relative">
                        <input class="form-check-input" type="radio"
                            name="selected_plan_id"
                            id="planRadio<?php echo e($plan->id); ?>-<?php echo e($formId); ?>"
                            value="<?php echo e($plan->id); ?>"
                            data-type="package"
                            <?php if($plan->id === $formId): ?> checked <?php endif; ?>>
                        <label class="form-check-label d-flex justify-content-between"
                            for="planRadio<?php echo e($plan->id); ?>-<?php echo e($formId); ?>">
                            <span class="package-name"><?php echo e($plan->name); ?></span>
                            <span class="price">$<?php echo e(number_format($plan->price, 2)); ?></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="row g-2 mt-3 mb-2">
        <div class="col-6 stripe">
            <input type="radio" class="btn-check"
                name="gateway_choice_<?php echo e($formId); ?>"
                id="gw_stripe_<?php echo e($formId); ?>"
                value="stripe" autocomplete="off" checked>
            <label class="btn btn-outline-light w-100 py-2" for="gw_stripe_<?php echo e($formId); ?>">💳 Card</label>
        </div>
        <div class="col-6 paypal">
            <input type="radio" class="btn-check"
                name="gateway_choice_<?php echo e($formId); ?>"
                id="gw_paypal_<?php echo e($formId); ?>"
                value="paypal" autocomplete="off">
            <label class="btn btn-outline-warning w-100 py-2" for="gw_paypal_<?php echo e($formId); ?>">🅿 PayPal</label>
        </div>
    </div>

    
    <div id="stripe-section-<?php echo e($formId); ?>">

        <div class="row mb-3 mt-3">
            <div class="col-xl-12">
                <label class="form-label text-white">Pay with Wallet</label>
                <div class="position-relative">
                    <div id="payment-request-button-container-<?php echo e($formId); ?>"></div>
                    <div id="wallet-overlay-<?php echo e($formId); ?>"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;cursor:pointer;display:none;">
                    </div>
                </div>
                <div id="wallet-errors-<?php echo e($formId); ?>" class="text-danger mt-2"></div>
            </div>
        </div>

        <div class="card-detail-wrapper my-3">
            <div class="row">
                <div class="form-group">
                    <label class="card-label mb-1">Card details</label>
                    <div id="card-element-<?php echo e($formId); ?>" class="form-control stripe-card-element"></div>
                    <div id="card-errors-<?php echo e($formId); ?>" class="text-danger mt-2" role="alert"></div>
                </div>
            </div>
        </div>

        <div class="terms-wrapper mb-3">
            <div class="form-check">
                <input name="terms_accepted" class="form-check-input"
                    type="checkbox" value="1"
                    id="flexCheckDefault-<?php echo e($formId); ?>" required>
                <label class="form-check-label" for="flexCheckDefault-<?php echo e($formId); ?>">
                    I agree that I have read our
                    <a href="<?php echo e(route('supportandlegal')); ?>#terms-conditions" target="_blank">
                        terms and conditions & I am over 18 years of age
                    </a>
                </label>
            </div>
        </div>

        <div class="access-btn">
            <button type="submit"
                class="btn btn-next submit-btn text-uppercase w-100"
                id="card-button-<?php echo e($formId); ?>">
                <?php echo e($buttonText); ?>

            </button>
        </div>

        <input type="hidden" name="payment_method" id="payment-method-id-<?php echo e($formId); ?>">
        <input type="hidden" name="selected_plan_id" id="pr_selected_plan_id_hidden-<?php echo e($formId); ?>">

    </div>

    
    <div id="paypal-section-<?php echo e($formId); ?>" style="display:none;">

        <div class="terms-wrapper mb-3 mt-3">
            <div class="form-check">
                <input name="terms_accepted" class="form-check-input" type="checkbox"
                    id="flexCheckPayPal-<?php echo e($formId); ?>" value="1">
                <label class="form-check-label" for="flexCheckPayPal-<?php echo e($formId); ?>">
                    I agree that I have read our
                    <a href="<?php echo e(route('supportandlegal')); ?>#terms-conditions" target="_blank">
                        terms and conditions & I am over 18 years of age
                    </a>
                </label>
            </div>
        </div>

        <div id="paypal-plan-error-<?php echo e($formId); ?>" class="text-danger small mb-2"></div>

        <div class="access-btn">
            <button type="button"
                class="btn btn-warning text-dark fw-bold text-uppercase w-100"
                onclick="handlePayPalClick('<?php echo e($formId); ?>')">
                🅿 Pay with PayPal
            </button>
        </div>

    </div>

</form>


<form id="paypal-hidden-form-<?php echo e($formId); ?>"
    action="<?php echo e(route('UserRegister')); ?>"
    method="POST"
    style="display:none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="selected_plan_id" id="paypal-plan-input-<?php echo e($formId); ?>">
    <input type="hidden" name="name"             id="paypal-name-input-<?php echo e($formId); ?>">
    <input type="hidden" name="email"            id="paypal-email-input-<?php echo e($formId); ?>">
    <input type="hidden" name="mobile"           id="paypal-mobile-input-<?php echo e($formId); ?>">
    <input type="hidden" name="terms_accepted"   value="1">
    <input type="hidden" name="payment_method"   value="">
</form><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/pop-up-subscription-register-form.blade.php ENDPATH**/ ?>