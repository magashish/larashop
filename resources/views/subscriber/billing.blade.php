@extends('layouts.subscriber') {{-- Your main layout file --}}

@section('content')
    <div class="db-main-content">
        <div class="container-fluid">
            <div class="account-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="auth-box py-4">
                            <div class="sec-head mb-4">
                                <h2 class="text-uppercase text-white">BILLING</h2>
                            </div>
                            <div class="billing-wrapper">
                                {{-- The form to manage payment methods --}}
                                <form id="payment-form" action="{{ route('billing.payment_methods_save') }}" method="POST">
                                    @csrf

                                    {{-- Display success messages --}}
                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    {{-- Display validation and Stripe errors --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="accordion" id="paymentAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingCard">
                                                <div class="d-flex align-items-center w-100">
                                                    {{-- This radio is for the accordion state, not for actual submission --}}
                                                    <input class="form-check-input mt-0 me-3" type="radio" name="paymentMethodOption" value="card" checked data-bs-toggle="collapse" data-bs-target="#collapseCard" aria-expanded="true" aria-controls="collapseCard">
                                                    <button class="accordion-button justify-content-between flex-wrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCard" aria-expanded="true" aria-controls="collapseCard">
                                                        <span class="text-uppercase d-lg-inline-block d-none">YOUR PAYMENT METHOD</span>
                                                        <span class="text-lg-center text-start" style="font-weight: 500;">Credit/Debit Card</span>
                                                        <span><img src="{{ asset('images/payment-method.png') }}" class="ms-auto" style="width: 80px;object-fit: contain;height: 24px;" alt="Credit Card Logos"></span>
                                                    </button>
                                                </div>
                                            </h2>
                                            <div id="collapseCard" class="accordion-collapse collapse show" aria-labelledby="headingCard" data-bs-parent="#paymentAccordion">
                                                <div class="accordion-body">
                                                    {{-- Saved Cards Section --}}
                                                    @if ($paymentMethods->isNotEmpty())
                                                        <div class="mb-3">
                                                            <h5 class="mb-3">Choose a Saved Card:</h5>
                                                            @foreach ($paymentMethods as $paymentMethod)
                                                                <div class="form-check mb-2 d-flex align-items-center">
                                                                    <input class="form-check-input me-2" type="radio" name="card_option" id="card-{{ $paymentMethod->id }}" value="saved"
                                                                           data-payment-method-id="{{ $paymentMethod->id }}"
                                                                           {{ ($defaultPaymentMethod && $paymentMethod->id === $defaultPaymentMethod->id) ? 'checked' : '' }}>
                                                                    {{-- <label class="form-check-label flex-grow-1" for="card-{{ $paymentMethod->id }}">
                                                                        {{ $paymentMethod->card->brand }} ending in {{ $paymentMethod->card->last4 }} (Expires {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }})
                                                                        @if ($defaultPaymentMethod && $paymentMethod->id === $defaultPaymentMethod->id)
                                                                            <span class="badge ms-2">Default</span>
                                                                        @endif
                                                                    </label> --}}

                                                                    <label class="form-check-label flex-grow-1" for="card-{{ $paymentMethod->id }}">

                                                                        @if($paymentMethod->type === 'card')
                                                                        {{ $paymentMethod->card->brand }} ending in {{ $paymentMethod->card->last4 }}
                                                                        (Expires {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }})

                                                                        @elseif($paymentMethod->type === 'link')
                                                                        Link ({{ $paymentMethod->link->email }})

                                                                        @else
                                                                        {{ ucfirst($paymentMethod->type) }} Payment Method
                                                                        @endif

                                                                        @if ($defaultPaymentMethod && $paymentMethod->id === $defaultPaymentMethod->id)
                                                                        <span class="badge ms-2">Default</span>
                                                                        @endif

                                                                    </label>



                                                                    {{-- Delete button for non-default cards --}}
                                                                    @if (!$defaultPaymentMethod || $paymentMethod->id !== $defaultPaymentMethod->id)
                                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto delete-pm-btn" data-payment-method-id="{{ $paymentMethod->id }}">Delete</button>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            <div class="form-check mb-3">
                                                                <input class="form-check-input" type="radio" name="card_option" id="newCardOption" value="new"
                                                                       {{ $paymentMethods->isEmpty() || !$defaultPaymentMethod || old('card_option') === 'new' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="newCardOption">
                                                                    Use a new card
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- If no saved cards, automatically select "Use a new card" --}}
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="radio" name="card_option" id="newCardOption" value="new" checked>
                                                            <label class="form-check-label" for="newCardOption">
                                                                Enter new card details
                                                            </label>
                                                        </div>
                                                    @endif

                                                    {{-- Stripe Card Element for new cards --}}
                                                    <div id="new-card-input-section" style="{{ ($paymentMethods->isNotEmpty() && ($defaultPaymentMethod && old('card_option') !== 'new')) ? 'display: none;' : '' }}">
                                                        <label class="form-label">Card details</label>
                                                        <div id="card-element" class="form-control">
                                                            </div>
                                                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                                        <div class="row mt-3">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">CARD HOLDER NAME</label>
                                                                <input type="text" id="card-holder-name" class="form-control" placeholder="Card Holder Name" value="{{ auth()->user()->name ?? '' }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">EMAIL</label>
                                                                <input type="email" id="card-holder-email" class="form-control" placeholder="Email" value="{{ auth()->user()->email ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                              
                                        {{--
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingGpay">
                                                <div class="d-flex align-items-center w-100">
                                                    <input class="form-check-input mt-0 me-3" type="radio" name="paymentMethodOption" value="gpay" data-bs-toggle="collapse" data-bs-target="#collapseGpay" aria-expanded="false" aria-controls="collapseGpay">
                                                    <button class="accordion-button collapsed justify-content-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGpay" aria-expanded="false" aria-controls="collapseGpay">
                                                        <img src="{{ asset('images/google-pay.png') }}" style="width: 120px;object-fit: contain;height: 24px;" alt="Google Pay Logo">
                                                    </button>
                                                </div>
                                            </h2>
                                            <div id="collapseGpay" class="accordion-collapse collapse" aria-labelledby="headingGpay" data-bs-parent="#paymentAccordion">
                                                <div class="accordion-body text-center">
                                                    <p>Use your Google Pay account to complete the transaction.</p>
                                                    <button type="button" class="btn btn-dark rounded-pill px-5" id="gpay-button">Pay with GPay</button>
                                                    <div id="gpay-errors" role="alert" class="text-danger mt-2"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingPaypal">
                                                <div class="d-flex align-items-center w-100">
                                                    <input class="form-check-input mt-0 me-3" type="radio" name="paymentMethodOption" value="paypal" data-bs-toggle="collapse" data-bs-target="#collapsePaypal" aria-expanded="false" aria-controls="collapsePaypal">
                                                    <button class="accordion-button collapsed justify-content-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaypal" aria-expanded="false" aria-controls="collapsePaypal">
                                                        <img src="{{ asset('images/paypal.png') }}" style="width: 120px;object-fit: contain;height: 24px;" alt="PayPal Logo">
                                                    </button>
                                                </div>
                                            </h2>
                                            <div id="collapsePaypal" class="accordion-collapse collapse" aria-labelledby="headingPaypal" data-bs-parent="#paymentAccordion">
                                                <div class="accordion-body text-center">
                                                    <p>You will be redirected to PayPal to complete your purchase securely.</p>
                                                    <button type="button" class="btn btn-dark rounded-pill px-5" id="paypal-button">Pay with PayPal</button>
                                                    <div id="paypal-errors" role="alert" class="text-danger mt-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                        --}}
                                    </div>

                                    <input type="hidden" name="payment_method_id" id="payment-method-id-input">
                                    {{-- This input tells the backend if it's a new card being added or an existing one --}}
                                    <input type="hidden" name="is_new_card" id="is-new-card-input" value="0">
                                    {{-- This input captures the main payment method type (currently fixed to 'card' for this form) --}}
                                    <input type="hidden" name="payment_type" id="payment-type-input" value="card">

                                    <div class="action-btns d-flex flex-wrap align-items-center justify-content-end gap-3 mt-md-5 mt-4">
                                        <button type="button" class="btn cancel-btn text-uppercase" onclick="window.location='{{ url()->previous() }}'">CANCEL</button>
                                        <button type="submit" class="btn btn-primary edit-btn text-uppercase" id="submit-button">SAVE</button>
                                    </div>
                                </form>

                                {{-- Form for deleting payment methods (dynamic) --}}
                                {{-- This form is hidden and used by JavaScript to submit DELETE requests --}}
                                <form id="delete-pm-form" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE') {{-- Required for DELETE requests in Laravel --}}
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // --- Stripe Elements Setup ---
        const stripe = Stripe('{{ env('STRIPE_KEY') }}'); // Use STRIPE_KEY for publishable key
        const elements = stripe.elements();
        const card = elements.create('card', {
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

        // Mount the card element to the div, but only if the 'new card' option is initially selected or no saved cards exist.
        card.mount('#card-element');

        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // --- DOM Elements ---
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const paymentMethodIdInput = document.getElementById('payment-method-id-input');
        const paymentTypeInput = document.getElementById('payment-type-input');
        const newCardInputSection = document.getElementById('new-card-input-section');
        const cardHolderNameInput = document.getElementById('card-holder-name');
        const cardHolderEmailInput = document.getElementById('card-holder-email');


        // --- Accordion and Radio Button Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodRadios = document.querySelectorAll('input[name="paymentMethodOption"]');
            const cardOptionRadios = document.querySelectorAll('input[name="card_option"]');
            function updateCardInputVisibility() {
                const selectedPaymentMethodType = document.querySelector('input[name="paymentMethodOption"]:checked').value;
                if (selectedPaymentMethodType === 'card') {
                    const selectedCardOption = document.querySelector('input[name="card_option"]:checked');
                    if (!selectedCardOption || selectedCardOption.value === 'new') {
                        newCardInputSection.style.display = 'block';
                        card.update({disabled: false});
                        card.focus();
                    } else {
                        newCardInputSection.style.display = 'none';
                        card.update({disabled: true});
                        paymentMethodIdInput.value = selectedCardOption.dataset.paymentMethodId;
                    }
                } else {
                    newCardInputSection.style.display = 'none';
                    card.update({disabled: true});
                    paymentMethodIdInput.value = ''; // Clear PM ID if not card
                }
            }

            updateCardInputVisibility();

            paymentMethodRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateCardInputVisibility();
                    // Set the payment type for the backend
                    paymentTypeInput.value = this.value;
                });
            });
            cardOptionRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateCardInputVisibility();
                });
            });
            paymentTypeInput.value = document.querySelector('input[name="paymentMethodOption"]:checked').value;
        });


        // --- Form Submission Logic ---
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            submitButton.disabled = true; 
            const selectedPaymentMethodType = document.querySelector('input[name="paymentMethodOption"]:checked').value;
            if (selectedPaymentMethodType === 'card') {
                const selectedCardOption = document.querySelector('input[name="card_option"]:checked');

                if (selectedCardOption.value === 'saved') {
                    form.submit();
                } else { 
                    stripe.createPaymentMethod({
                        type: 'card',
                        card: card,
                        billing_details: {
                            name: cardHolderNameInput.value,
                            email: cardHolderEmailInput.value,
                        },
                    }).then(function(result) {
                        if (result.error) {
                            document.getElementById('card-errors').textContent = result.error.message;
                            submitButton.disabled = false;
                        } else {
                            paymentMethodIdInput.value = result.paymentMethod.id;
                            form.submit();
                        }
                    });
                }
            } else if (selectedPaymentMethodType === 'gpay') {
                document.getElementById('gpay-errors').textContent = 'Google Pay integration not fully implemented yet. Please use Credit/Debit Card.';
                submitButton.disabled = false;

            } else if (selectedPaymentMethodType === 'paypal') {
                document.getElementById('paypal-errors').textContent = 'PayPal integration not fully implemented yet. Please use Credit/Debit Card.';
                submitButton.disabled = false;
            }
        });

        function handleCardAction(clientSecret) {
            stripe.handleCardAction(clientSecret).then(function(result) {
                if (result.error) {
                    document.getElementById('card-errors').textContent = result.error.message;
                    submitButton.disabled = false;
                } else {
                    form.submit();
                }
            });
        }

        @if (session()->has('payment_intent_client_secret') && session('requires_action'))
            handleCardAction("{{ session('payment_intent_client_secret') }}");
        @endif

         document.querySelectorAll('.delete-pm-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Confirm with the user before deleting
                if (confirm('Are you sure you want to delete this payment method? This action cannot be undone.')) {
                    const paymentMethodIdToDelete = this.dataset.paymentMethodId;
                    const deleteForm = document.getElementById('delete-pm-form');

                    // Dynamically set the form's action URL to include the PM ID for deletion
                    deleteForm.action = `/payment-methods-delete/${paymentMethodIdToDelete}`;
                    deleteForm.submit(); // Submit the hidden form
                }
            });
        });
    </script>
@endsection