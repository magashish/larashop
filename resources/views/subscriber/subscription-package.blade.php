@extends('layouts.consumer')
@section('content')

<div class="hero-sec login-sec mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">
            <div class="col-xl-6 col-12">
                <div class="container">
                    <div class="hero-content signup-multistep-wrapper">

                        <div class="bg-title mx-0 mb-5">
                            <div class="yellow-bg">SIGN UP TO</div>
                            <div class="black-bg">XHALE TODAY!</div>
                            <p>Become a loyal member and start unlocking exclusive discounts from hundreds of local gems and big-name brands. Plus, rack up points for your chance to win exciting cash and prize giveaways!</p>
                        </div>

                        <div class="partners-form-box auth-box py-4">
                            <div class="form-outer-wrapper mx-auto pt-4">
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
                                            <div class="step-circle active" onclick="displayStep(2)">2 <i class="bi bi-check2"></i></div>
                                            <div class="step-title text-uppercase">BILLING DETAILS</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- =============================================
                                     STRIPE MAIN FORM
                                ============================================== --}}
                                <form id="payment-form" class="mt-4" action="{{ route('subscription_package_store') }}" method="POST">
                                    @csrf

                                    <div class="step step-2">
                                        <div class="step-inner-content mb-2 py-3 px-4">

                                            {{-- Alerts --}}
                                            @if($errors->any())
                                                <div class="alert alert-danger">
                                                    @foreach($errors->all() as $error)
                                                        <div>{{ $error }}</div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="package-price-wrapper">
                                                <div class="package-label d-flex align-items-center justify-content-between">
                                                    <h6>ITEM</h6>
                                                    <h6>COST</h6>
                                                </div>

                                                @php
                                                    $data = planPackageList();
                                                    $subscriptions = $data['subscriptions'];
                                                    $packages = $data['packages'];
                                                @endphp

                                                <div class="item-list">
                                                    <h5 class="mb-2 text-white">Subscription</h5>
                                                    @foreach($subscriptions as $key => $subscription)
                                                        <div class="form-check mb-2">
                                                            <div class="position-relative">
                                                                <input class="form-check-input" type="radio"
                                                                    name="selected_plan_id"
                                                                    id="subscriptionRadio{{ $subscription->id }}"
                                                                    value="{{ $subscription->id }}"
                                                                    data-type="subscription"
                                                                    @if($key === 0) checked @endif>
                                                                <label class="form-check-label d-flex justify-content-between"
                                                                    for="subscriptionRadio{{ $subscription->id }}">
                                                                    <span class="package-name">{{ $subscription->name }}</span>
                                                                    <span class="price">${{ number_format($subscription->price, 2) }}</span>
                                                                </label>
                                                            </div>
                                                            <div class="payment_description">{!! $subscription->payment_description !!}</div>
                                                        </div>
                                                    @endforeach

                                                    <hr>

                                                    <h5 class="mt-4 mb-2 text-white">Single Purchase Packages</h5>
                                                    @foreach($packages as $key => $plan)
                                                        <div class="form-check mb-2">
                                                            <div class="position-relative">
                                                                <input class="form-check-input" type="radio"
                                                                    name="selected_plan_id"
                                                                    id="planRadio{{ $plan->id }}"
                                                                    value="{{ $plan->id }}"
                                                                    data-type="package">
                                                                <label class="form-check-label d-flex justify-content-between"
                                                                    for="planRadio{{ $plan->id }}">
                                                                    <span class="package-name">{{ $plan->name }}</span>
                                                                    <span class="price">${{ number_format($plan->price, 2) }}</span>
                                                                </label>
                                                            </div>
                                                            <div class="payment_description">{!! $plan->payment_description !!}</div>
                                                        </div>
                                                    @endforeach
                                                    <hr>
                                                </div>
                                            </div>

                                            {{-- ===== GATEWAY TOGGLE ===== --}}
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <input type="radio" class="btn-check" name="gateway_sub_pkg" id="gw_stripe_sub_pkg" value="stripe" autocomplete="off" checked>
                                                    <label class="btn btn-outline-light w-100 py-2" for="gw_stripe_sub_pkg">💳 Card</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="radio" class="btn-check" name="gateway_sub_pkg" id="gw_paypal_sub_pkg" value="paypal" autocomplete="off">
                                                    <label class="btn btn-outline-warning w-100 py-2" for="gw_paypal_sub_pkg">🅿 PayPal</label>
                                                </div>
                                            </div>

                                            {{-- ===== STRIPE SECTION ===== --}}
                                            <div id="stripe-section-sub-pkg">

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
                                                            <div id="card-errors" class="text-danger mt-2"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="terms-wrapper">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-check">
                                                                <input name="terms_accepted" class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" required>
                                                                <label class="form-check-label" for="flexCheckDefault">
                                                                    I agree that I have read our <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">terms and conditions & I am over 18 years of age</a>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-next submit-btn text-uppercase w-100" id="card-button">Submit</button>

                                                <input type="hidden" name="payment_method" id="payment-method-id">
                                                <input type="hidden" name="selected_plan_id" id="pr_selected_plan_id_hidden">

                                            </div>{{-- END stripe-section --}}

                                            {{-- ===== PAYPAL SECTION ===== --}}
                                            <div id="paypal-section-sub-pkg" style="display:none;">

                                                <div class="terms-wrapper mt-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="flexCheckPayPalSubPkg">
                                                                <label class="form-check-label" for="flexCheckPayPalSubPkg">
                                                                    I agree that I have read our <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">terms and conditions & I am over 18 years of age</a>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="sub-pkg-paypal-terms-err" class="text-danger small mb-1"></div>
                                                <div id="sub-pkg-paypal-plan-err"  class="text-danger small mb-2"></div>

                                                <button type="button"
                                                    class="btn btn-warning text-dark fw-bold text-uppercase w-100"
                                                    onclick="handlePayPalSubPkg()">
                                                    🅿 Pay with PayPal
                                                </button>

                                            </div>{{-- END paypal-section --}}

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
                                </form>{{-- END payment-form --}}

                                {{-- =============================================
                                     PAYPAL HIDDEN FORM — BAHAR hai payment-form se
                                     Stripe submit listener intercept nahi karega
                                ============================================== --}}
                                <form id="paypal-hidden-form-sub-pkg"
                                    action="{{ route('subscription_package_store') }}"
                                    method="POST"
                                    style="display:none;">
                                    @csrf
                                    <input type="hidden" name="selected_plan_id" id="sub-pkg-paypal-plan">
                                    <input type="hidden" name="payment_method"   value="">
                                    <input type="hidden" name="terms_accepted"   value="1">
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12 position-relative">
                <div class="hero-img" style="background-image: url(images/sign_up.png);"></div>
                <div class="hero-slider-section">
                    @include('global.offer-hero-slider')
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>

/* ================================================
   GATEWAY TOGGLE
================================================= */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[name="gateway_sub_pkg"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const stripeSection = document.getElementById('stripe-section-sub-pkg');
            const paypalSection = document.getElementById('paypal-section-sub-pkg');
            if (this.value === 'stripe') {
                stripeSection.style.display = 'block';
                paypalSection.style.display = 'none';
            } else {
                stripeSection.style.display = 'none';
                paypalSection.style.display = 'block';
            }
        });
    });
});

/* ================================================
   PAYPAL HANDLER
   Logged-in user — sirf plan select + terms check
   payment_method empty → controller PayPal detect karega
================================================= */
function handlePayPalSubPkg() {
    const termsEl  = document.getElementById('flexCheckPayPalSubPkg');
    const termsErr = document.getElementById('sub-pkg-paypal-terms-err');
    const planErr  = document.getElementById('sub-pkg-paypal-plan-err');

    if (termsErr) termsErr.textContent = '';
    if (planErr)  planErr.textContent  = '';

    // Terms check
    if (!termsEl || !termsEl.checked) {
        if (termsErr) {
            termsErr.textContent = 'Please agree to the terms and conditions.';
        }
        return;
    }

    // Plan check
    const selectedPlan = document.querySelector('#payment-form input[name="selected_plan_id"]:checked');
    if (!selectedPlan) {
        if (planErr) {
            planErr.textContent = 'Please select a plan.';
        }
        return;
    }

    // Fill hidden form and submit
    document.getElementById('sub-pkg-paypal-plan').value = selectedPlan.value;
    document.getElementById('paypal-hidden-form-sub-pkg').submit();
}

/* ================================================
   STRIPE LOGIC — original unchanged
================================================= */
document.addEventListener('DOMContentLoaded', () => {
    const stripeKey = "{{ config('services.stripe.key') }}";
    if (!stripeKey) return console.error('Stripe publishable key missing');

    const stripe   = Stripe(stripeKey);
    const elements = stripe.elements();

    const form          = document.getElementById('payment-form');
    const cardMount     = document.getElementById('card-element');
    const cardButton    = document.getElementById('card-button');
    const planRadios    = document.querySelectorAll('input[name="selected_plan_id"]');
    const termsCheckbox = document.getElementById('flexCheckDefault');
    const walletOverlay = document.getElementById('wallet-overlay');

    let cardErrors = document.getElementById('card-errors');
    if (!cardErrors) {
        cardErrors = document.createElement('div');
        cardErrors.id = 'card-errors';
        cardErrors.className = 'text-danger mt-2';
        cardMount.after(cardErrors);
    }
    const walletErrors = document.getElementById('wallet-errors') || cardErrors;

    /* --- Overlay --- */
    function toggleWalletOverlay() {
        if (!walletOverlay) return;
        if (termsCheckbox && termsCheckbox.checked) {
            walletOverlay.style.display = 'none';
            walletErrors.textContent = '';
            cardErrors.textContent   = '';
        } else {
            walletOverlay.style.display = 'block';
        }
    }

    if (walletOverlay) {
        walletOverlay.addEventListener('click', (e) => {
            e.stopPropagation();
            walletErrors.textContent = 'Please agree to the terms and conditions before using Google/Apple Pay.';
        });
    }

    if (termsCheckbox) termsCheckbox.addEventListener('change', toggleWalletOverlay);

    /* --- Stripe Card --- */
    const card = elements.create('card', {
        style: { base: { fontSize: '16px', color: '#32325d' } }
    });

    let cardMounted = false;
    function mountCardIfVisible() {
        const step2 = document.querySelector('.step-2');
        if (step2 && !cardMounted && getComputedStyle(step2).display !== 'none') {
            card.mount(cardMount);
            cardMounted = true;
        }
    }

    const step2 = document.querySelector('.step-2');
    if (step2) new MutationObserver(mountCardIfVisible).observe(step2, { attributes: true, attributeFilter: ['style', 'class'] });
    mountCardIfVisible();

    /* --- Plan Helpers --- */
    function getSelectedPlan() {
        let planId = null, name = 'Subscription', priceCents = 0;
        const activeRadio = document.querySelector('input[name="selected_plan_id"]:checked');
        if (activeRadio) {
            planId = activeRadio.value;
            const label = activeRadio.closest('.form-check').querySelector('.form-check-label');
            if (label) {
                const priceEl = label.querySelector('.price');
                const nameEl  = label.querySelector('.package-name');
                if (nameEl) name = nameEl.textContent.trim().split('-')[0].trim();
                if (priceEl) priceCents = Math.round(parseFloat(priceEl.textContent.replace(/[$,]/g, '')) * 100);
            }
        }
        return { planId, name, priceCents };
    }

    function setHiddenInputs(planId, paymentMethodId) {
        let pm = document.getElementById('payment-method-id') ||
            Object.assign(document.createElement('input'), { type: 'hidden', name: 'payment_method', id: 'payment-method-id' });
        if (!pm.parentElement) form.appendChild(pm);
        pm.value = paymentMethodId;

        let plan = form.querySelector('input[name="selected_plan_id"][type="hidden"]') ||
            Object.assign(document.createElement('input'), { type: 'hidden', name: 'selected_plan_id' });
        if (!plan.parentElement) form.appendChild(plan);
        plan.value = planId;
    }

    /* --- Form Submit --- */
    form.addEventListener('submit', async e => {
        if (termsCheckbox && !termsCheckbox.checked) {
            e.preventDefault();
            cardErrors.textContent = 'Please agree to the terms and conditions before proceeding.';
            return;
        }
        if (form.dataset.wallet === 'true') return;
        e.preventDefault();
        const { planId } = getSelectedPlan();
        if (!planId) { cardErrors.textContent = 'Please select a plan'; return; }
        cardButton.disabled = true;
        cardButton.textContent = 'Processing...';
        const { paymentMethod, error } = await stripe.createPaymentMethod({ type: 'card', card });
        if (error) { cardErrors.textContent = error.message; cardButton.disabled = false; cardButton.textContent = 'Submit'; return; }
        setHiddenInputs(planId, paymentMethod.id);
        form.submit();
    });

    /* --- Google Pay / Apple Pay / Link Pay --- */
    let paymentRequest = null;

    function initPaymentRequest() {
        const plan      = getSelectedPlan();
        const container = document.getElementById('payment-request-button-container');
        if (!container || !plan.planId || plan.priceCents <= 0) { if (container) container.style.display = 'none'; return; }
        if (paymentRequest) { paymentRequest.update({ total: { label: plan.name, amount: plan.priceCents } }); return; }

        paymentRequest = stripe.paymentRequest({
            country: 'AU', currency: 'aud',
            total: { label: plan.name, amount: plan.priceCents },
            requestPayerName: true, requestPayerEmail: true
        });

        const prButton = elements.create('paymentRequestButton', { paymentRequest });

        paymentRequest.canMakePayment().then(result => {
            if (result) { prButton.mount(container); container.style.display = 'block'; toggleWalletOverlay(); }
            else container.style.display = 'none';
        });

        paymentRequest.on('paymentmethod', ev => {
            // Terms check
            if (termsCheckbox && !termsCheckbox.checked) {
                ev.complete('fail');
                walletErrors.textContent = 'Please agree to the terms and conditions.';
                return;
            }
            const currentPlan = getSelectedPlan();
            if (!currentPlan.planId) { ev.complete('fail'); walletErrors.textContent = 'Please select a plan.'; return; }
            setHiddenInputs(currentPlan.planId, ev.paymentMethod.id);
            form.dataset.wallet = 'true';
            ev.complete('success');
            form.submit();
        });
    }

    initPaymentRequest();
    planRadios.forEach(r => r.addEventListener('change', initPaymentRequest));
});
</script>

@endsection