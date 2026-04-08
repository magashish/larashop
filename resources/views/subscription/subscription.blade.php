@extends('layouts.subscriber')
@section('content')

@php
    $upsellExpiry = session('upsell_expires', 0);
    $isUpsell     = session('upsell_active')
                    && session('upsell_plan_id') == $plan->id
                    && now()->timestamp <= $upsellExpiry;
    $finalPrice   = $isUpsell ? $plan->price * 0.50 : $plan->price;
@endphp

<div class="db-main-content">
    <div class="container-fluid">
        <div class="legal-doc-wrapper accordion-sec">
            <div class="container">
                <div class="p-4">
                    <div class="auth-box">
                        <div class="sec-head mb-4 text-center">
                            <h2 class="text-uppercase text-white">Secure Checkout</h2>
                        </div>
                        <div class="card">

                            {{-- Header --}}
                            <div class="card-header text-white text-center" style="background-color: #343a40;">
                                @if($isUpsell)
                                    <h4 class="mb-0">
                                        <del class="text fs-6">${{ number_format($plan->price, 2) }}</del>
                                        <span class="text-warning ms-2">${{ number_format($finalPrice, 2) }}</span>
                                        for the {{ $plan->name }} Plan
                                        <span class="badge bg-warning text-dark ms-2">50% OFF</span>
                                    </h4>
                                @else
                                    <h4 class="mb-0">You will be charged ${{ number_format($finalPrice, 2) }} for the {{ $plan->name }} Plan</h4>
                                @endif
                            </div>

                            <div class="card-body bg-dark text-white p-4">

                                {{-- Alerts --}}
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- ===== GATEWAY TOGGLE ===== --}}
                                <div class="mb-4">
                                    <p class="text-uppercase text-white small mb-2">Select Payment Method</p>
                                    <div class="row g-2">
                                        <div class="col-6 stripe">
                                            <input type="radio" class="btn-check" name="gateway_choice" id="gateway_stripe" value="stripe" autocomplete="off" checked>
                                            <label class="btn btn-outline-light w-100 py-2" for="gateway_stripe">
                                                💳 Card
                                            </label>
                                        </div>
                                        <div class="col-6 paypal">
                                            <input type="radio" class="btn-check" name="gateway_choice" id="gateway_paypal" value="paypal" autocomplete="off">
                                            <label class="btn btn-outline-warning w-100 py-2" for="gateway_paypal">
                                                🅿 PayPal
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary">

                                {{-- ===== STRIPE SECTION ===== --}}
                                <div id="stripe-section">
                                    <form id="payment-form" action="{{ route('subscription.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan"                     value="{{ $plan->id }}">
                                        <input type="hidden" name="type"                     value="{{ $plan->type }}">
                                        <input type="hidden" name="is_upsell"                value="{{ $isUpsell ? 1 : 0 }}">
                                        <input type="hidden" name="card_holder_name_hidden"  id="card-holder-name-hidden">
                                        <input type="hidden" name="card_holder_email_hidden" id="card-holder-email-hidden">
                                        <input type="hidden" name="payment_method_id"        id="payment-method-id-input">

                                        {{-- Name & Email --}}
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label small text-white">Name on Card</label>
                                                <input type="text" name="name" id="card-holder-name"
                                                    class="form-control bg-dark text-white border-secondary"
                                                    value="{{ old('name', auth()->user()->name ?? '') }}"
                                                    placeholder="John Smith">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-white">Email</label>
                                                <input type="email" name="email" id="card-holder-email"
                                                    class="form-control bg-dark text-white border-secondary"
                                                    value="{{ old('email', auth()->user()->email ?? '') }}"
                                                    placeholder="you@email.com">
                                            </div>
                                        </div>

                                        {{-- Apple Pay / Google Pay --}}
                                        <div class="mb-3" id="wallet-payment-section">
                                            <label class="form-label small text-white">Pay with Wallet</label>
                                            <div id="payment-request-button-container"></div>
                                            <div id="wallet-errors" class="text-danger mt-1 small"></div>
                                        </div>

                                        {{-- Saved Cards --}}
                                        @if($paymentMethods->isNotEmpty())
                                            <label class="form-label small text-white text-uppercase">Saved Cards</label>
                                            @forelse ($paymentMethods as $paymentMethod)
                                                <div class="d-flex align-items-center border border-secondary rounded-3 px-3 py-2 mb-2">
                                                    <input class="form-check-input me-3 flex-shrink-0" type="radio"
                                                        name="selected_payment_option"
                                                        id="pm-{{ $paymentMethod->id }}"
                                                        value="saved"
                                                        data-payment-method-id="{{ $paymentMethod->id }}"
                                                        {{ ($defaultPaymentMethod && $paymentMethod->id === $defaultPaymentMethod->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label flex-grow-1" for="pm-{{ $paymentMethod->id }}">
                                                        @if ($paymentMethod->type === 'card')
                                                            <span class="fw-semibold text-uppercase">{{ $paymentMethod->card->brand }}</span>
                                                            &nbsp;•••• {{ $paymentMethod->card->last4 }}
                                                            <span class="ms-2 small">{{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}</span>
                                                        @elseif ($paymentMethod->type === 'link')
                                                            Link — {{ $paymentMethod->link->email }}
                                                        @elseif ($paymentMethod->type === 'us_bank_account')
                                                            Bank •••• {{ $paymentMethod->us_bank_account->last4 }}
                                                        @else
                                                            {{ ucfirst($paymentMethod->type) }}
                                                        @endif
                                                    </label>
                                                    @if ($defaultPaymentMethod && $paymentMethod->id === $defaultPaymentMethod->id)
                                                        <span class="badge bg-secondary ms-2">Default</span>
                                                    @endif
                                                </div>
                                            @empty
                                            @endforelse
                                        @endif

                                        {{-- New Card --}}
                                        <div class="d-flex align-items-center border border-secondary border-dashed rounded-3 px-3 py-2 mb-3">
                                            <input class="form-check-input me-3 flex-shrink-0" type="radio"
                                                name="selected_payment_option"
                                                id="newCardOption" value="new"
                                                {{ $paymentMethods->isEmpty() || old('selected_payment_option') === 'new' ? 'checked' : '' }}>
                                            <label class="form-check-label text-white" for="newCardOption">
                                                + Enter new card details
                                            </label>
                                        </div>

                                        {{-- New Card Fields --}}
                                        <div id="new-card-details-section"
                                            style="{{ $paymentMethods->isEmpty() || old('selected_payment_option') === 'new' ? '' : 'display:none;' }}">
                                            <div class="mb-3">
                                                <label class="form-label small text-white">Card Number</label>
                                                <div id="card-number-element"
                                                    class="form-control bg-dark border-secondary"
                                                    style="height:42px; padding-top:10px;"></div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small text-white">Expiry</label>
                                                    <div id="card-expiry-element"
                                                        class="form-control bg-dark border-secondary"
                                                        style="height:42px; padding-top:10px;"></div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-white">CVC</label>
                                                    <div id="card-cvc-element"
                                                        class="form-control bg-dark border-secondary"
                                                        style="height:42px; padding-top:10px;"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="card-errors" class="text-danger small mb-3"></div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg fw-semibold" id="card-button"
                                                data-secret="{{ $intent->client_secret }}">
                                                🔒 Pay ${{ number_format($finalPrice, 2) }} {{ strtoupper(env('DEFAULT_CURRENCY', 'AUD')) }}
                                                @if($isUpsell) &nbsp;(50% Off) @endif
                                            </button>
                                        </div>
                                    </form>
                                </div>{{-- END stripe-section --}}

                                {{-- ===== PAYPAL SECTION ===== --}}
                                <div id="paypal-section" style="display:none;">
                                    <div class="alert alert-warning bg-opacity-10 border-warning rounded-3 mb-4">
                                        <small>ℹ️ You will be securely redirected to PayPal to complete your payment.</small>
                                    </div>
                                    <form action="{{ route('paypal.redirect') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan"      value="{{ $plan->id }}">
                                        <input type="hidden" name="type"      value="{{ $plan->type }}">
                                        <input type="hidden" name="is_upsell" value="{{ $isUpsell ? 1 : 0 }}">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-warning btn-lg fw-semibold text-dark">
                                                🅿 Pay ${{ number_format($finalPrice, 2) }} with PayPal
                                                @if($isUpsell) &nbsp;(50% Off) @endif
                                            </button>
                                        </div>
                                    </form>
                                </div>{{-- END paypal-section --}}

                                {{-- Secure Note --}}
                                <div class="text-center mt-3">
                                    <small class="text">🔒 256-bit SSL · Secured by Stripe &amp; PayPal</small>
                                </div>

                            </div>{{-- card-body --}}
                        </div>{{-- card --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    // Gateway Toggle
    document.querySelectorAll('input[name="gateway_choice"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('stripe-section').style.display = this.value === 'stripe' ? 'block' : 'none';
            document.getElementById('paypal-section').style.display = this.value === 'paypal'  ? 'block' : 'none';
        });
    });

    // Stripe
    const stripe   = Stripe('{{ env('STRIPE_KEY') }}');
    const elements = stripe.elements();
    const style = {
        base: {
            fontSize: '14px',
            color: '#fff',
            '::placeholder': { color: '#fff' },
            ':-webkit-autofill': {
                color: '#fff',
            },
        },
        invalid: { color: '#dc3545' },
    };

    const cardNumber = elements.create('cardNumber', { style });
    const cardExpiry = elements.create('cardExpiry', { style });
    const cardCvc    = elements.create('cardCvc',    { style });

    const form                  = document.getElementById('payment-form');
    const cardErrors            = document.getElementById('card-errors');
    const submitButton          = document.getElementById('card-button');
    const cardHolderName        = document.getElementById('card-holder-name');
    const cardHolderEmail       = document.getElementById('card-holder-email');
    const newCardDetailsSection = document.getElementById('new-card-details-section');
    const paymentMethodIdInput  = document.getElementById('payment-method-id-input');
    const hiddenName            = document.getElementById('card-holder-name-hidden');
    const hiddenEmail           = document.getElementById('card-holder-email-hidden');

    function updateCardVisibility() {
        const r = document.querySelector('input[name="selected_payment_option"]:checked');
        if (!r) return;
        if (r.value === 'new') {
            newCardDetailsSection.style.display = 'block';
            cardNumber.mount('#card-number-element');
            cardExpiry.mount('#card-expiry-element');
            cardCvc.mount('#card-cvc-element');
            paymentMethodIdInput.value = '';
            if (cardHolderName)  cardHolderName.disabled  = false;
            if (cardHolderEmail) cardHolderEmail.disabled = false;
        } else {
            newCardDetailsSection.style.display = 'none';
            cardNumber.unmount(); cardExpiry.unmount(); cardCvc.unmount();
            paymentMethodIdInput.value = r.dataset.paymentMethodId || '';
            if (cardHolderName)  cardHolderName.disabled  = true;
            if (cardHolderEmail) cardHolderEmail.disabled = true;
        }
        cardErrors.textContent = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCardVisibility();
        document.querySelectorAll('input[name="selected_payment_option"]').forEach(r => {
            r.addEventListener('change', updateCardVisibility);
        });
    });

    [cardNumber, cardExpiry, cardCvc].forEach(el => {
        el.on('change', e => { cardErrors.textContent = e.error ? e.error.message : ''; });
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        submitButton.disabled = true;
        cardErrors.textContent = '';
        const r = document.querySelector('input[name="selected_payment_option"]:checked');
        if (!r) return;
        hiddenName.value  = cardHolderName.value;
        hiddenEmail.value = cardHolderEmail.value;
        if (r.value === 'new') {
            const { setupIntent, error } = await stripe.confirmCardSetup(submitButton.dataset.secret, {
                payment_method: { card: cardNumber, billing_details: { name: cardHolderName.value, email: cardHolderEmail.value } }
            });
            if (error) { cardErrors.textContent = error.message; submitButton.disabled = false; }
            else { paymentMethodIdInput.value = setupIntent.payment_method; form.submit(); }
        } else { form.submit(); }
    });

    function handleCardAction(s) {
        stripe.handleAction(s).then(r => {
            if (r.error) { cardErrors.textContent = r.error.message; submitButton.disabled = false; }
            else { form.submit(); }
        });
    }
    @if (session()->has('payment_intent_client_secret') && session('requires_action'))
        handleCardAction("{{ session('payment_intent_client_secret') }}");
    @endif

    // Apple Pay / Google Pay
    const pr = stripe.paymentRequest({
        country: 'AU', currency: '{{ strtolower($plan->currency ?? "aud") }}',
        total: { label: '{{ $plan->name }} Plan', amount: {{ $finalPrice * 100 }} },
        requestPayerName: true, requestPayerEmail: true,
    });
    const prBtn = elements.create('paymentRequestButton', { paymentRequest: pr });
    pr.canMakePayment().then(result => {
        if (result) prBtn.mount('#payment-request-button-container');
        else document.getElementById('payment-request-button-container').style.display = 'none';
    });
    pr.on('paymentmethod', async function(ev) {
        document.getElementById('wallet-errors').textContent = '';
        submitButton.disabled = true;
        try {
            const { setupIntent, error } = await stripe.confirmCardSetup('{{ $intent->client_secret }}', { payment_method: ev.paymentMethod.id });
            if (error) { ev.complete('fail'); document.getElementById('wallet-errors').textContent = error.message; submitButton.disabled = false; }
            else { ev.complete('success'); paymentMethodIdInput.value = setupIntent.payment_method; form.submit(); }
        } catch (err) { ev.complete('fail'); document.getElementById('wallet-errors').textContent = err.message; submitButton.disabled = false; }
    });
</script>

@endsection