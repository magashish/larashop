 <div class="signup-multistep-wrapper package-form-wrapper {{ strtolower($package_subscription_plan->name) }}">
   <div class="partners-form-box auth-box p-xl-0">
     <div class="pop-up-head">
         <img src="https://www.xhale.com.au/images/logo.svg">
     </div>

     <div class="access-title d-xl-block d-none">
        <p class="mb-0">{{  $package_subscription_plan->name }}</p>
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

    <form id="payment-form" class="mt-4" action="{{ route('UserRegister') }}" method="POST">
        @csrf
        <!-- Step 1 form fields here -->
        <div class="step step-1">                              
          <div class="step-inner-content mb-3 px-4">
            <!-- Form -->
            <div class="row">




               <div class="col-12 input-group-icon position-relative">
                <i class="bi bi-person input-icon" aria-hidden="true"></i>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Full Name... *" required>
                @error('name')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>




            <div class="col-12 input-group-icon position-relative">
                <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                <input id="signupemail" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}"
                placeholder="Email Address...*" required>
                <div class="invalid-feedback"></div>

            </div>



            <div class="col-12 input-group-icon position-relative">
                <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                <input name="mobile" type="text" class="form-control" id="mobile" placeholder="Phone number...*" aria-label="Phone Number" value="{{ old('mobile') }}" required />
                <div class="invalid-feedback d-block">
                </div>
            </div>










        </div>
        <button type="button" class="btn btn-primary sign-up-next-step  btn-next text-uppercase w-100">Next &raquo;</button>                                                
    </div>


    <div class="form-footer text-center mt-2">
        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-underline">Login here</a></p>
    </div>


    <div class="social-icons-text text-center mt-4 mb-3">
     <p class="position-relative mb-0"><span>Or continue with</span></p>
 </div>
 @include('global.social-link')

</div>

<!-- Step 2 form fields here -->
<div class="step step-2">                              
  <div class="step-inner-content mb-2 py-3 px-4">
    <button type="button" class="btn btn-primary sign-up-prev-step w-100 ps-2"><i class="bi bi-chevron-left"></i> Edit Contact Detail</button>
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

            {{-- SUBSCRIPTION TITLE --}}
            <h5 class="mb-2 text-white">Subscription</h5>

            @foreach($subscriptions as $key => $subscription)
            <div class="form-check mb-2">
             <div class="position-relative">
                <input
                class="form-check-input"
                type="radio"
                name="selected_plan_id"
                id="subscriptionRadio{{ $subscription->id }}"
                value="{{ $subscription->id }}"
                @if($subscription->id == $package_subscription_plan->id) checked @endif
                >
                <label class="form-check-label d-flex justify-content-between" for="subscriptionRadio{{ $subscription->id }}">
                    <span class="package-name">
                        {{ $subscription->name }}
                    </span>
                    <span class="price">
                        ${{ number_format($subscription->price, 2) }}
                    </span>
                </label>
            </div>
            <div class="payment_description">
               {!! $subscription->payment_description !!}
           </div>

       </div>
       @endforeach
       <hr>
       {{-- PACKAGE TITLE --}}
       <h5 class="mt-4 mb-2 text-white">Single Purchase Packages</h5>
       @foreach($packages as $key => $plan)
       <div class="form-check mb-2">
        <div class="position-relative">
            <input
            class="form-check-input"
            type="radio"
            name="selected_plan_id"
            id="planRadio{{ $plan->id }}"
            value="{{ $plan->id }}"
            @if($plan->id == $package_subscription_plan->id) checked @endif
            >
            <label class="form-check-label d-flex justify-content-between" for="planRadio{{ $plan->id }}">
                <span class="package-name">
                    {{ $plan->name }}
                </span>
                <span class="price">
                    ${{ number_format($plan->price, 2) }}
                </span>
            </label>
        </div>
        <div class="payment_description">
           {!! $plan->payment_description !!}
       </div>
   </div>
   @endforeach

   <hr>
</div>
</div>



<div class="row mb-3" id="wallet-payment-section">
    <div class="col-xl-12">
        <label class="form-label" style="color: #ffffff;">Pay with Wallet</label>
        <div class="position-relative">
            <div id="payment-request-button-container"></div>
            <div id="wallet-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; cursor: pointer; display: none;"></div>
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
              <input name="terms_accepted" class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
              <label class="form-check-label" for="flexCheckDefault">
                I agree that I have read our <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">terms and conditions & I am over 18 years of age</a>
            </label>
        </div>
    </div>

</div>
</div>

{{-- Main submit button for the form, which could also be used for card details submission --}}
<button type="submit" class="btn btn-next submit-btn text-uppercase w-100" id="card-button">Submit</button>

{{-- Hidden input for payment method ID --}}
<input type="hidden" name="payment_method" id="payment-method-id">
<input type="hidden" name="selected_plan_id" id="pr_selected_plan_id_hidden"> {{-- Ensure this hidden field is for PR button if you use it --}}





</div>

<div class="payment-partner-wrapper pt-2">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <img src="{{ asset('images/payment-img.png') }}" class="img-fluid">
        </div>
        <div class="col-sm-6">
            <p class="mb-0">Trusted, Encrypted & Secure Payments</p>
        </div>
    </div>
</div>
</div>
</form>
</div>                             
</div>
</div>
