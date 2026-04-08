@extends('layouts.consumer')
@section('content')

<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>Checkout</h2>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form id="checkoutForm" action="{{ route('shop.checkout.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Left: Address + Shipping --}}
                <div class="col-lg-7">

                    {{-- Billing Address --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-dark text-white fw-semibold">Billing Details</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_first_name" class="form-control" value="{{ old('billing_first_name', $user?->first_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_last_name" class="form-control" value="{{ old('billing_last_name', $user?->last_name) }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="billing_email" class="form-control" value="{{ old('billing_email', $user?->email) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="billing_phone" class="form-control" value="{{ old('billing_phone', $user?->mobile) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select name="billing_state" id="billingState" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(['NSW','VIC','QLD','WA','SA','TAS','ACT','NT'] as $st)
                                            <option value="{{ $st }}" @selected(old('billing_state') === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Postcode <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_postcode" class="form-control" value="{{ old('billing_postcode') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="billing_country" class="form-control" value="{{ old('billing_country', 'AU') }}" maxlength="2" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ship to different address --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent fw-semibold d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" id="shipToBilling" name="ship_to_billing" value="1" checked>
                            <label class="form-check-label" for="shipToBilling">Ship to billing address</label>
                        </div>
                        <div class="card-body d-none" id="shippingAddressBlock">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="shipping_first_name" class="form-control" value="{{ old('shipping_first_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="shipping_last_name" class="form-control" value="{{ old('shipping_last_name') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address') }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">City</label>
                                    <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <select name="shipping_state" class="form-select">
                                        <option value="">Select</option>
                                        @foreach(['NSW','VIC','QLD','WA','SA','TAS','ACT','NT'] as $st)
                                            <option value="{{ $st }}" @selected(old('shipping_state') === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Postcode</label>
                                    <input type="text" name="shipping_postcode" class="form-control" value="{{ old('shipping_postcode') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="shipping_country" class="form-control" value="{{ old('shipping_country', 'AU') }}" maxlength="2">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="card border-0 shadow-sm mb-4" id="shippingMethodCard">
                        <div class="card-header bg-transparent fw-semibold">Shipping Method</div>
                        <div class="card-body" id="shippingRatesContainer">
                            <p class="text-muted small">Enter your billing state to see shipping options.</p>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent fw-semibold">Order Notes (Optional)</div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-dark text-white fw-semibold"><i class="bi bi-lock-fill me-2"></i>Secure Payment</div>
                        <div class="card-body">
                            <div id="stripe-card-element" class="form-control p-3" style="min-height: 45px;"></div>
                            <div id="stripe-card-errors" class="text-danger mt-2 small" role="alert"></div>
                            <input type="hidden" name="payment_intent_id" id="paymentIntentId">
                            <input type="hidden" name="shipping_rate_id" id="selectedShippingRateId">
                        </div>
                    </div>
                </div>

                {{-- Right: Order Summary --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header bg-dark text-white fw-semibold">Your Order</div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-semibold">{{ $item->product->name }}</span>
                                            <small class="text-muted d-block">× {{ $item->quantity }}</small>
                                        </td>
                                        <td class="text-end pe-3">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="ps-3">Subtotal</td>
                                        <td class="text-end pe-3">${{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    @if($discountAmount > 0)
                                    <tr class="text-success">
                                        <td class="ps-3">Discount ({{ $coupon?->code }})</td>
                                        <td class="text-end pe-3">-${{ number_format($discountAmount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr id="shippingRow" class="d-none">
                                        <td class="ps-3">Shipping</td>
                                        <td class="text-end pe-3" id="shippingCostDisplay">$0.00</td>
                                    </tr>
                                    @if($taxRate)
                                    <tr>
                                        <td class="ps-3">{{ $taxRate->name }} ({{ number_format($taxRate->rate * 100, 2) }}%)</td>
                                        <td class="text-end pe-3" id="taxDisplay">${{ number_format($taxAmount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="table-dark fw-bold fs-6">
                                        <td class="ps-3">Total</td>
                                        <td class="text-end pe-3" id="grandTotal">
                                            ${{ number_format($subtotal - $discountAmount + $taxAmount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <button type="submit" id="placeOrderBtn" class="btn btn-success w-100 btn-lg rounded-pill">
                                <i class="bi bi-lock-fill me-2"></i>Place Order
                            </button>
                            <p class="text-center text-muted small mt-2"><i class="bi bi-shield-check me-1"></i>Secured by Stripe</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config("services.stripe.key") }}');
const elements = stripe.elements();
const cardElement = elements.create('card', {style: {base: {fontSize: '16px', color: '#424770'}}});
cardElement.mount('#stripe-card-element');
cardElement.on('change', function(event) {
    document.getElementById('stripe-card-errors').textContent = event.error ? event.error.message : '';
});

// Ship to billing toggle
document.getElementById('shipToBilling').addEventListener('change', function() {
    document.getElementById('shippingAddressBlock').classList.toggle('d-none', this.checked);
});

// Load shipping rates when state changes
document.getElementById('billingState').addEventListener('change', loadShippingRates);

function loadShippingRates() {
    const state   = document.getElementById('billingState').value;
    const country = document.querySelector('[name="billing_country"]').value || 'AU';
    if (!state) return;

    fetch(`{{ route('shop.checkout.shipping-rates') }}?state=${state}&country=${country}`)
        .then(r => r.json())
        .then(rates => {
            const container = document.getElementById('shippingRatesContainer');
            if (!rates.length) {
                container.innerHTML = '<p class="text-muted small">No shipping options available for your location.</p>';
                return;
            }
            let html = '<div class="list-group list-group-flush">';
            rates.forEach((rate, i) => {
                const rateLabel = rate.type === 'free' ? 'Free' : `$${rate.rate.toFixed(2)}`;
                html += `<label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div><input type="radio" name="_shipping_display" value="${rate.id}" class="me-2 shipping-rate-radio" ${i===0?'checked':''} onchange="updateShipping(${rate.rate}, '${rateLabel}', ${rate.id})">
                    ${rate.name}</div>
                    <strong>${rateLabel}</strong></label>`;
            });
            html += '</div>';
            container.innerHTML = html;
            if (rates.length > 0) {
                updateShipping(rates[0].rate, rates[0].type === 'free' ? 'Free' : `$${rates[0].rate.toFixed(2)}`, rates[0].id);
            }
        });
}

function updateShipping(rate, label, rateId) {
    document.getElementById('selectedShippingRateId').value = rateId;
    document.getElementById('shippingRow').classList.remove('d-none');
    document.getElementById('shippingCostDisplay').textContent = label;
    recalcTotal(rate);
}

function recalcTotal(shippingCost) {
    const subtotal  = {{ $subtotal }};
    const discount  = {{ $discountAmount }};
    const taxRate   = {{ $taxRate ? $taxRate->rate : 0 }};
    const taxable   = subtotal - discount;
    const tax       = Math.round(taxable * taxRate * 100) / 100;
    const total     = taxable + shippingCost + tax;

    document.getElementById('taxDisplay') && (document.getElementById('taxDisplay').textContent = '$' + tax.toFixed(2));
    document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
}

// Form submit
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    const shippingRateId = document.getElementById('selectedShippingRateId').value;

    try {
        // Create payment intent
        const resp = await fetch('{{ route("shop.checkout.payment-intent") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({shipping_rate_id: shippingRateId})
        });
        const data = await resp.json();

        if (!data.client_secret) throw new Error('Payment intent failed');

        // Confirm payment
        const {paymentIntent, error} = await stripe.confirmCardPayment(data.client_secret, {
            payment_method: {card: cardElement}
        });

        if (error) {
            document.getElementById('stripe-card-errors').textContent = error.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Place Order';
            return;
        }

        document.getElementById('paymentIntentId').value = paymentIntent.id;
        this.submit();
    } catch(err) {
        document.getElementById('stripe-card-errors').textContent = 'An error occurred. Please try again.';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Place Order';
    }
});
</script>
@endpush
@endsection
