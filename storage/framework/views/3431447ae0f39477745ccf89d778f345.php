<div class="signup-multistep-wrapper">
    <div class="partners-form-box auth-box p-xl-0">
        <div class="pop-up-head">
            <img src="https://www.xhale.com.au/images/logo.svg">
        </div>
        <div class="form-outer-wrapper mx-auto pt-4 px-sm-5 py-4">
            <div class="progress-wrapper">
                <div class="progress px-1" style="height: 3px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="step-container d-flex justify-content-between">
                    <div class="step-number-outer">
                        <div class="step-circle" onclick="displayStep(1)">1 <i class="bi bi-check2"></i></div>
                        <div class="step-title text-uppercase">CONTACT DETAILS</div>
                    </div>
                    <div class="step-number-outer">
                        <div class="step-circle" onclick="displayStep(2)">2 <i class="bi bi-check2"></i></div>
                        <div class="step-title text-uppercase">BILLING DETAILS</div>
                    </div>
                </div>
            </div>

            
            <form id="payment-form" class="mt-4" action="<?php echo e(route('UserRegister')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <!-- Step 1 -->
                <div class="step step-1">
                    <div class="step-inner-content mb-3 px-4">
                        <div class="row">
                            <div class="col-12 input-group-icon position-relative">
                                <i class="bi bi-person input-icon" aria-hidden="true"></i>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>"
                                    placeholder="Full Name... *" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-12 input-group-icon position-relative">
                                <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                                <input id="signupemail" type="email"
                                    class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="email" value="<?php echo e(old('email')); ?>"
                                    placeholder="Email Address...*" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 input-group-icon position-relative">
                                <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                                <input name="mobile" type="text" class="form-control" id="mobile"
                                    placeholder="Phone number...*" aria-label="Phone Number"
                                    value="<?php echo e(old('mobile')); ?>" required />
                                <div class="invalid-feedback d-block"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary sign-up-next-step btn-next text-uppercase w-100">Next &raquo;</button>
                    </div>

                    <div class="form-footer text-center mt-2">
                        <p class="mb-0">Already have an account? <a href="<?php echo e(route('login')); ?>" class="text-decoration-underline">Login here</a></p>
                    </div>

                    <div class="social-icons-text text-center mt-4 mb-3">
                        <p class="position-relative mb-0"><span>Or continue with</span></p>
                    </div>
                    <?php echo $__env->make('global.social-link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <!-- Step 2 -->
                <div class="step step-2">
                    <div class="step-inner-content mb-2 py-3 px-4">
                        <button type="button" class="btn btn-primary sign-up-prev-step w-100 ps-2">
                            <i class="bi bi-chevron-left"></i> Edit Contact Detail
                        </button>

                        <div class="package-price-wrapper">
                            <div class="package-label d-flex align-items-center justify-content-between">
                                <h6>ITEM</h6>
                                <h6>COST</h6>
                            </div>

                            <?php
                                $data = planPackageList();
                                $subscriptions = $data['subscriptions'];
                                $packages = $data['packages'];
                            ?>

                            <div class="item-list">
                                <h5 class="mb-2 text-white">Subscription</h5>
                                <?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check mb-2">
                                        <div class="position-relative">
                                            <input class="form-check-input" type="radio"
                                                name="selected_plan_id"
                                                id="subscriptionRadio<?php echo e($subscription->id); ?>"
                                                value="<?php echo e($subscription->id); ?>"
                                                data-type="subscription"
                                                <?php if($key === 0): ?> checked <?php endif; ?>>
                                            <label class="form-check-label d-flex justify-content-between"
                                                for="subscriptionRadio<?php echo e($subscription->id); ?>">
                                                <span class="package-name"><?php echo e($subscription->name); ?></span>
                                                <span class="price">$<?php echo e(number_format($subscription->price, 2)); ?></span>
                                            </label>
                                        </div>
                                        <div class="payment_description"><?php echo $subscription->payment_description; ?></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <hr>
                                <h5 class="mt-4 mb-2 text-white">Single Purchase Packages</h5>
                                <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check mb-2">
                                        <div class="position-relative">
                                            <input class="form-check-input" type="radio"
                                                name="selected_plan_id"
                                                id="planRadio<?php echo e($plan->id); ?>"
                                                value="<?php echo e($plan->id); ?>"
                                                data-type="package">
                                            <label class="form-check-label d-flex justify-content-between"
                                                for="planRadio<?php echo e($plan->id); ?>">
                                                <span class="package-name"><?php echo e($plan->name); ?></span>
                                                <span class="price">$<?php echo e(number_format($plan->price, 2)); ?></span>
                                            </label>
                                        </div>
                                        <div class="payment_description"><?php echo $plan->payment_description; ?></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <hr>
                            </div>
                        </div>

                        
                        <div class="row g-2 mb-3">
                            <div class="col-6 stripe">
                                <input type="radio" class="btn-check" name="gateway_choice_main" id="gw_stripe_main" value="stripe" autocomplete="off" checked>
                                <label class="btn btn-outline-light w-100 py-2" for="gw_stripe_main">💳 Card</label>
                            </div>
                            <div class="col-6 paypal">
                                <input type="radio" class="btn-check" name="gateway_choice_main" id="gw_paypal_main" value="paypal" autocomplete="off">
                                <label class="btn btn-outline-warning w-100 py-2" for="gw_paypal_main">🅿 PayPal</label>
                            </div>
                        </div>

                        
                        <div id="stripe-section-main">

                            <div class="row mb-3" id="wallet-payment-section">
                                <div class="col-xl-12">
                                    <label class="form-label" style="color: #ffffff;">Pay with Wallet</label>
                                    <div class="position-relative">
                                        <div id="payment-request-button-container"></div>
                                        <div id="wallet-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;cursor:pointer;display:none;"></div>
                                    </div>
                                    <div id="wallet-errors" class="text-danger mt-2"></div>
                                </div>
                            </div>

                            <div class="card-detail-wrapper my-3">
                                <div class="row">
                                    <div class="form-group">
                                        <label class="card-label mb-1">Card details</label>
                                        <div id="card-element" class="form-control stripe-card-element"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="terms-wrapper">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-check">
                                            <input name="terms_accepted" class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" required>
                                            <label class="form-check-label" for="flexCheckDefault">
                                                I agree that I have read our <a href="<?php echo e(route('supportandlegal')); ?>#terms-conditions" target="_blank">terms and conditions & I am over 18 years of age</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-next submit-btn text-uppercase w-100" id="card-button">Submit</button>

                            <input type="hidden" name="payment_method" id="payment-method-id">
                            <input type="hidden" name="selected_plan_id" id="pr_selected_plan_id_hidden">

                        </div>

                        
                        <div id="paypal-section-main" style="display:none;">

                            <div class="terms-wrapper mt-3 mb-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="flexCheckPayPalMain">
                                            <label class="form-check-label" for="flexCheckPayPalMain">
                                                I agree that I have read our <a href="<?php echo e(route('supportandlegal')); ?>#terms-conditions" target="_blank">terms and conditions & I am over 18 years of age</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="paypal-plan-error-main" class="text-danger small mb-2"></div>

                            <button type="button" class="btn btn-warning text-dark fw-bold text-uppercase w-100"
                                onclick="handlePayPalClickMain()">
                                🅿 Pay with PayPal
                            </button>

                        </div>

                        <div class="payment-partner-wrapper pt-2">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <img src="images/payment-img.png" class="img-fluid">
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-0">Trusted, Encrypted & Secure Payments</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

            
            <form id="paypal-hidden-form-main" action="<?php echo e(route('UserRegister')); ?>" method="POST" style="display:none;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="selected_plan_id" id="paypal-plan-main">
                <input type="hidden" name="name"             id="paypal-name-main">
                <input type="hidden" name="email"            id="paypal-email-main">
                <input type="hidden" name="mobile"           id="paypal-mobile-main">
                <input type="hidden" name="terms_accepted"   value="1">
                <input type="hidden" name="payment_method"   value="">
            </form>

        </div>
    </div>
</div><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/global/register-form.blade.php ENDPATH**/ ?>