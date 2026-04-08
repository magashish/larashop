<form id="payment-form-{{ $formId }}"
    class="payment-form mt-4"
    data-form-id="{{ $formId }}"
    action="{{ route('UserRegister') }}"
    method="POST">
    @csrf

    {{-- Contact Details --}}
    <div class="row g-2">
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="name" id="name-{{ $formId }}"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', auth()->user()->name ?? '') }}"
                    placeholder="Full Name *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email-{{ $formId }}"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="Email Address *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" name="mobile" id="mobile-{{ $formId }}"
                    class="form-control @error('mobile') is-invalid @enderror"
                    value="{{ old('mobile') }}"
                    placeholder="Phone Number *" required>
                <div class="invalid-feedback d-block"></div>
            </div>
        </div>
    </div>

    @php
        $data = planPackageList();
        $subscriptions = $data['subscriptions'];
        $packages = $data['packages'];
    @endphp

    <div class="package-price-wrapper mt-3" style="display: none;">
        <div class="item-list">
            @foreach($subscriptions as $key => $subscription)
                <div class="form-check mb-2">
                    <div class="position-relative">
                        <input class="form-check-input" type="radio"
                            name="selected_plan_id"
                            id="subscriptionRadio{{ $subscription->id }}-{{ $formId }}"
                            value="{{ $subscription->id }}"
                            data-type="subscription"
                            @if($subscription->id === $formId) checked @endif>
                        <label class="form-check-label d-flex justify-content-between"
                            for="subscriptionRadio{{ $subscription->id }}-{{ $formId }}">
                            <span class="package-name">{{ $subscription->name }}</span>
                            <span class="price">${{ number_format($subscription->price, 2) }}</span>
                        </label>
                    </div>
                </div>
            @endforeach
            @foreach($packages as $key => $plan)
                <div class="form-check mb-2">
                    <div class="position-relative">
                        <input class="form-check-input" type="radio"
                            name="selected_plan_id"
                            id="planRadio{{ $plan->id }}-{{ $formId }}"
                            value="{{ $plan->id }}"
                            data-type="package"
                            @if($plan->id === $formId) checked @endif>
                        <label class="form-check-label d-flex justify-content-between"
                            for="planRadio{{ $plan->id }}-{{ $formId }}">
                            <span class="package-name">{{ $plan->name }}</span>
                            <span class="price">${{ number_format($plan->price, 2) }}</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ===== GATEWAY TOGGLE ===== --}}
    <div class="row g-2 mt-3 mb-2">
        <div class="col-6 stripe">
            <input type="radio" class="btn-check"
                name="gateway_choice_{{ $formId }}"
                id="gw_stripe_{{ $formId }}"
                value="stripe" autocomplete="off" checked>
            <label class="btn btn-outline-light w-100 py-2" for="gw_stripe_{{ $formId }}">💳 Card</label>
        </div>
        <div class="col-6 paypal">
            <input type="radio" class="btn-check"
                name="gateway_choice_{{ $formId }}"
                id="gw_paypal_{{ $formId }}"
                value="paypal" autocomplete="off">
            <label class="btn btn-outline-warning w-100 py-2" for="gw_paypal_{{ $formId }}">🅿 PayPal</label>
        </div>
    </div>

    {{-- ===== STRIPE SECTION ===== --}}
    <div id="stripe-section-{{ $formId }}">

        <div class="row mb-3 mt-3">
            <div class="col-xl-12">
                <label class="form-label text-white">Pay with Wallet</label>
                <div class="position-relative">
                    <div id="payment-request-button-container-{{ $formId }}"></div>
                    <div id="wallet-overlay-{{ $formId }}"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;cursor:pointer;display:none;">
                    </div>
                </div>
                <div id="wallet-errors-{{ $formId }}" class="text-danger mt-2"></div>
            </div>
        </div>

        <div class="card-detail-wrapper my-3">
            <div class="row">
                <div class="form-group">
                    <label class="card-label mb-1">Card details</label>
                    <div id="card-element-{{ $formId }}" class="form-control stripe-card-element"></div>
                    <div id="card-errors-{{ $formId }}" class="text-danger mt-2" role="alert"></div>
                </div>
            </div>
        </div>

        <div class="terms-wrapper mb-3">
            <div class="form-check">
                <input name="terms_accepted" class="form-check-input"
                    type="checkbox" value="1"
                    id="flexCheckDefault-{{ $formId }}" required>
                <label class="form-check-label" for="flexCheckDefault-{{ $formId }}">
                    I agree that I have read our
                    <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">
                        terms and conditions & I am over 18 years of age
                    </a>
                </label>
            </div>
        </div>

        <div class="access-btn">
            <button type="submit"
                class="btn btn-next submit-btn text-uppercase w-100"
                id="card-button-{{ $formId }}">
                {{ $buttonText }}
            </button>
        </div>

        <input type="hidden" name="payment_method" id="payment-method-id-{{ $formId }}">
        <input type="hidden" name="selected_plan_id" id="pr_selected_plan_id_hidden-{{ $formId }}">

    </div>{{-- END stripe-section --}}

    {{-- ===== PAYPAL SECTION ===== --}}
    <div id="paypal-section-{{ $formId }}" style="display:none;">

        <div class="terms-wrapper mb-3 mt-3">
            <div class="form-check">
                <input name="terms_accepted" class="form-check-input" type="checkbox"
                    id="flexCheckPayPal-{{ $formId }}" value="1">
                <label class="form-check-label" for="flexCheckPayPal-{{ $formId }}">
                    I agree that I have read our
                    <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">
                        terms and conditions & I am over 18 years of age
                    </a>
                </label>
            </div>
        </div>

        <div id="paypal-plan-error-{{ $formId }}" class="text-danger small mb-2"></div>

        <div class="access-btn">
            <button type="button"
                class="btn btn-warning text-dark fw-bold text-uppercase w-100"
                onclick="handlePayPalClick('{{ $formId }}')">
                🅿 Pay with PayPal
            </button>
        </div>

    </div>{{-- END paypal-section --}}

</form>

{{--
    IMPORTANT: PayPal hidden form — BAHAR hai payment-form se
    Alag form hai taake Stripe submit listener intercept na kare
--}}
<form id="paypal-hidden-form-{{ $formId }}"
    action="{{ route('UserRegister') }}"
    method="POST"
    style="display:none;">
    @csrf
    <input type="hidden" name="selected_plan_id" id="paypal-plan-input-{{ $formId }}">
    <input type="hidden" name="name"             id="paypal-name-input-{{ $formId }}">
    <input type="hidden" name="email"            id="paypal-email-input-{{ $formId }}">
    <input type="hidden" name="mobile"           id="paypal-mobile-input-{{ $formId }}">
    <input type="hidden" name="terms_accepted"   value="1">
    <input type="hidden" name="payment_method"   value="">
</form>