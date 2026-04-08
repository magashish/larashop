@extends('layouts.frontapp')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    You will be charged ${{ number_format($plan->price, 2) }} for {{ $plan->name }} Plan
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="payment-form" action="{{ route('subscription.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" id="plan" value="{{ $plan->id }}">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="card-holder-name">Name on Card</label>
                                    <input type="text" name="name" id="card-holder-name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Name on the card" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Card details</label>
                                    <div id="card-element" class="form-control" style="height: 40px; padding-top: 10px;">
                                        {{-- A Stripe Element will be inserted here. --}}
                                    </div>
                                    {{-- Used to display form errors. --}}
                                    <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <hr>
                                <button type="submit" class="btn btn-primary" id="card-button" data-secret="{{ $intent->client_secret }}">Subscribe</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                        <hr>
                        <h4>Or Pay with:</h4>
                        <div id="payment-request-button" style="height: 40px; width: 200px;">
                            {{-- A Stripe Payment Request Button will be inserted here. --}}
                        </div>
                    </div>

                </div> {{-- End card-body --}}
            </div> {{-- End card --}}
        </div> {{-- End col-md-12 --}}
    </div> {{-- End row --}}
</div> {{-- End container-fluid --}}

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('cashier.key') }}'); // Use config('cashier.key') for Stripe Publishable Key

    const elements = stripe.elements();

    // Card Element setup
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a',
            },
        },
    });
    cardElement.mount('#card-element');

    // Display errors
    const cardErrors = document.getElementById('card-errors');
    cardElement.addEventListener('change', function(event) {
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    });

    const form = document.getElementById('payment-form');
    const cardBtn = document.getElementById('card-button');
    const cardHolderName = document.getElementById('card-holder-name');
    const planInput = document.getElementById('plan'); // Get the hidden plan input

    // Handle form submission for card payments
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        cardBtn.disabled = true;
        const { setupIntent, error } = await stripe.confirmCardSetup(
            cardBtn.dataset.secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardHolderName.value
                    }
                }
            }
        );

        if (error) {
            cardErrors.textContent = error.message;
            cardBtn.disabled = false;
        } else {
            let token = document.createElement('input');
            token.setAttribute('type', 'hidden');
            token.setAttribute('name', 'token');
            token.setAttribute('value', setupIntent.payment_method); // This is the payment_method ID
            form.appendChild(token);
            form.submit();
        }
    });

    // --- Google Pay / Apple Pay (Payment Request Button) ---
    if (stripe) {
        const paymentRequest = stripe.paymentRequest({
            country: 'US', // Your country code (e.g., 'US', 'GB', 'CA')
            currency: 'usd', // Your currency code (e.g., 'usd', 'gbp', 'eur')
            total: {
                label: {!! json_encode(strval($plan->name) . ' Plan') !!}, // CORRECTED: Ensure $plan->name is a string before concatenation and encoding
                amount: {{ (int)($plan->price * 100) }}, // CORRECTED: Explicitly cast to int for clean number
            },
            requestPayerName: true,
            requestPayerEmail: true,
            // You can add requestPayerPhone: true, requestShipping: true for more details
        });

        const prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest,
        });

        // Check the availability of the Payment Request API first.
        paymentRequest.canMakePayment().then(function(result) {
            if (result) {
                prButton.mount('#payment-request-button');
            } else {
                document.getElementById('payment-request-button').style.display = 'none';
                // You might hide the "Or Pay with:" text here too if no payment requests are available.
            }
        });

        // Handle the click event for the Payment Request Button
        paymentRequest.on('paymentmethod', function(ev) {
            stripe.confirmCardSetup(
                cardBtn.dataset.secret, // Re-using the client_secret from the setupIntent
                {
                    payment_method: ev.paymentMethod.id,
                }
            ).then(function(result) {
                if (result.error) {
                    // Inform the customer that something went wrong
                    cardErrors.textContent = result.error.message;
                    ev.complete('fail');
                } else {
                    // Payment successful. Send the paymentMethod.id and plan to your server.
                    let token = document.createElement('input');
                    token.setAttribute('type', 'hidden');
                    token.setAttribute('name', 'token');
                    token.setAttribute('value', ev.paymentMethod.id);
                    form.appendChild(token); // Append to the main form

                    // Ensure the plan ID is also submitted
                    let planHidden = document.createElement('input');
                    planHidden.setAttribute('type', 'hidden');
                    planHidden.setAttribute('name', 'plan');
                    planHidden.setAttribute('value', planInput.value); // Use value from existing hidden input
                    form.appendChild(planHidden);

                    form.submit(); // Submit the form to your subscription.create route
                    ev.complete('success');
                }
            });
        });
    }

</script>
@endsection
