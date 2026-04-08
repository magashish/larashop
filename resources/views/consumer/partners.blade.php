@extends('layouts.consumer')
@section('content')

<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-lg-4 pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/partner.png') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/partner-mob.png') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title">
            <div class="yellow-bg text-uppercase">YOUR OFFERS</div>
            <div class="black-bg text-uppercase"><h1>OUR COMMUNITY</h1></div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->

<!-- Text Banner Sec Start -->
<!-- <section class="packages-quote-sec pt-lg-0 pb-5 d-lg-block d-none">
    <div class="container ">
        <div class="row">
            <div class="content-box">
                <div class="image-box">
                    <img src="images/Screenshot_2025-06-26_125700-removebg-preview.png" alt="">
                </div>
                <p class="medium">Your wellness destination to access deals, support local and prioritise your
                wellbeing and be in for the chance to win cash and prizes!</p>
            </div>
        </div>
    </div>
</section> -->
<!-- Text Banner Sec End -->

<section class="partners-form-box pt-lg-0 pt-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="registration-card">
                <div class="outer-border"></div>

                <!-- decorative-logo-top-left -->
                <div class="logo-top-left d-lg-block d-none">
                    <img src="images/exhale--logo.png" alt="">
                </div>

                <!-- decorative-logo-bottom-right -->
                <div class="logo-bottom-right">
                    <img src="images/XHALE-SECOND-LOGO.png" alt="">
                </div>






                <!-- Header Text -->
                <p class="header-text text-center mb-5">
                    CREATE A <span class="highlight">BUSINESS ACCOUNT</span>
                </p>

                <div class="form-outer-wrapper mx-auto mt-5 pt-4">
                  <div class="progress px-1" style="height: 3px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="step-container d-flex justify-content-between">
                    <div class="step-number-outer">
                        <div class="step-circle" onclick="displayStep(1)">1 <i class="bi bi-check2"></i></div>
                        <div class="step-title text-uppercase">BUSINESS DETAILS</div>
                    </div>
                    <div class="step-number-outer">
                        <div class="step-circle" onclick="displayStep(2)">2 <i class="bi bi-check2"></i></div>
                        <div class="step-title text-uppercase">CONTACT DETAILS</div>
                    </div>
                    <div class="step-number-outer">
                        <div class="step-circle" onclick="displayStep(3)">3 <i class="bi bi-check2"></i></div>
                        <div class="step-title text-uppercase">OFFER DETAILS</div>
                    </div>
                </div>

                <form id="multi-step-form" class="mt-5" method="POST" enctype="multipart/form-data" action="{{ route('createbusinessaccount')}}">
                    @csrf
                    <!-- Step 1 form fields here -->
                    <div class="step step-1">                              
                      <div class="step-inner-content mb-3">
                        <!-- Form -->
                        <div class="row">





                            <div class="col-12 input-group-icon position-relative">
                                <i class="bi bi-person input-icon" aria-hidden="true"></i>
                                <input type="text" name="business_ame" class="form-control" placeholder="Business Name" required />
                            </div>  

                            <div class="col-12 input-group-icon position-relative">
                                <i class="bi bi-person input-icon" aria-hidden="true"></i>
                                <input type="text" name="abn" class="form-control" placeholder="ABN / ACN" />
                            </div> 

                            <div class="col-md-6 input-group-icon position-relative">
                               <i class="bi bi-instagram  input-icon"></i>
                               <input type="url" name="instagram" id="instagram"
                               class="form-control @error('instagram') is-invalid @enderror"
                               value="{{ old('instagram') }}"
                               placeholder="https://instagram.com/yourprofile">

                           </div>


                           <div class="col-md-6 input-group-icon position-relative">
                             <i class="bi bi-facebook  input-icon"></i>
                             <input type="url" name="facebook" id="facebook"
                             class="form-control @error('facebook') is-invalid @enderror"
                             value="{{ old('facebook') }}"
                             placeholder="https://facebook.com/yourpage">

                         </div>


                         <div class="col-md-6 input-group-icon position-relative">
                             <i class="bi bi-youtube  input-icon"></i>
                             <input type="url" name="youtube" id="youtube"
                             class="form-control @error('youtube') is-invalid @enderror"
                             value="{{ old('youtube') }}"
                             placeholder="https://youtube.com/yourchannel">

                         </div>


                         <div class="col-md-6 input-group-icon position-relative">
                          <i class="bi bi-tiktok  input-icon"></i>
                          <input type="url" name="tiktok" id="tiktok"
                          class="form-control @error('tiktok') is-invalid @enderror"
                          value="{{ old('tiktok') }}"
                          placeholder="https://tiktok.com/@yourprofile">

                      </div>


                      <div class="col-md-6 input-group-icon position-relative">
                        <i class="bi bi-twitter-x  input-icon"></i>
                        <input type="url" name="twitter" id="twitter"
                        class="form-control @error('twitter') is-invalid @enderror"
                        value="{{ old('twitter') }}"
                        placeholder="https://twitter.com/yourhandle">
                        @error('twitter')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>



                   {{--    <div class="col-12 input-group-icon position-relative">
                        <i class="bi bi-person input-icon" aria-hidden="true"></i>
                        <input type="text" name="business_website" class="form-control" placeholder="Business Website" />
                    </div> 
                    <div class="col-12 input-group-icon position-relative">
                        <i class="bi bi-building input-icon" aria-hidden="true"></i>
                        <select name="industry_type" class="form-select form-control">
                            <option value="" disabled selected>Select Industry Type</option>
                            <option value="Health">Health</option>
                            <option value="Beauty">Beauty</option>
                            <option value="Fitness">Fitness</option>
                            <option value="Food">Food</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Retail">Retail</option>
                            <option value="Services">Services</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>  --}}

                    <div class="col-12 input-group-icon position-relative">
                        <fieldset class="with-bg">
                            <legend class="fs-6 text-muted w-auto"><i class="bi bi-globe"></i> Business Address Type</legend>
                            <div class="mb-1 @error('organisation_address_type') is-invalid @enderror">
                                <div class="d-flex flex-wrap gap-sm-3 gap-1">
                                    @foreach (\App\Enums\OrganisationAddressType::labels() as $value => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input address-type-radio" type="radio" 
                                        name="organisation_address_type" id="address_type_{{ $value }}" 
                                        value="{{ $value }}"
                                        {{ old('organisation_address_type', \App\Enums\OrganisationAddressType::ONLINE) == $value ? 'checked' : '' }}>
                                        <label class="form-check-label" for="address_type_{{ $value }}">
                                            <span class="d-flex align-items-center">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('organisation_address_type')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </fieldset>
                        <div id="businessphysicalAddressFields" class="col-12 input-group-icon position-relative mt-3 mb-0">
                            <i class="bi bi-person input-icon" aria-hidden="true"></i>
                            <input type="text" id="business_address_search" name="business_address_search" class="form-control" placeholder="Business Address" />
                        </div> 
                        <div id="business_address_lookup_status" class="">
                        </div>
                    </div>



                    {{-- <div class="col-12 input-group-icon position-relative">
                        <i class="bi bi-person input-icon" aria-hidden="true"></i>
                        <input type="file" name="image" id="image" accept="image/*"
                        class="form-control @error('image') is-invalid @enderror" placeholder="Organisation Logo"> 
                    </div> --}}

                    <div class="col-12 input-group-icon position-relative">
                        <div class="custom-file-container">
                           <i class="bi bi-image input-icon" aria-hidden="true"></i>
                           <label for="image" class="custom-file-label form-control @error('image') is-invalid @enderror">
                            Add Logo 
                        </label>
                        <input type="file" name="image" id="customimage" accept="image/*" class="custom-file-input">
                    </div>
                </div>


                </div>
            </div>
            <div class="action-btns">
                <button type="button" class="btn btn-primary next-step btn-next text-uppercase">Next <i class="bi bi-chevron-double-right"></i></button>
            </div>
            
        </div>

        <!-- Step 2 form fields here -->
        <div class="step step-2">                              
          <div class="step-inner-content mb-3">


            <div class="mb-4 p-3 border rounded bg-light">
                <h5 class="fw-bold mb-3">Passwords must:</h5>
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-circle-fill me-2"></i> Be at least 8 characters long</li>
                    <li><i class="bi bi-circle-fill me-2"></i> Be at max 32 characters</li>
                    <li><i class="bi bi-circle-fill me-2"></i> Include at least one UPPERCASE letter</li>
                    <li><i class="bi bi-circle-fill me-2"></i> Include at least one lowercase letter</li>
                    <li><i class="bi bi-circle-fill me-2"></i> Include at least one number</li>
                    <li><i class="bi bi-circle-fill me-2"></i> Include at least one special character, such as ~`!@#$%^&amp;*()?&lt;&gt;{}|[]\/-+</li>
                </ul>
            </div>
                    
            <!-- Form -->
            <div class="row">
                <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-person input-icon" aria-hidden="true"></i>
                    <input type="text" name="first_name" class="form-control" placeholder="First Name"
                    aria-label="First Name" required />
                </div>
                <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-person input-icon" aria-hidden="true"></i>
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                    aria-label="Last Name" required />
                </div>

                <!-- In your step 2 section -->
                <div class="col-sm-12 input-group-icon position-relative">
                    <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required />
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password" type="text" class="form-control" name="password" placeholder="Password" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password-confirm" type="text" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                    <div class="invalid-feedback"></div>
                </div>


                <div class="col-12 input-group-icon position-relative">
                    <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                    <input name="mobile" type="tel" class="form-control" placeholder="Phone Number"
                    aria-label="Phone Number" required />
                </div>





      {{--           <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password" type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    value="{{ old('password') }}"
                    placeholder="Enter password" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password-confirm" type="password"
                    class="form-control"
                    name="password_confirmation"
                    value="{{ old('password_confirmation') }}"
                    placeholder="Re-enter password" required>
                </div> --}}






            </div>
        </div>
        <div class="action-btns">
            <button type="button" class="btn btn-primary prev-step"><i class="bi bi-chevron-double-left"></i> Previous</button> 
            <button type="button" class="btn btn-primary next-step btn-next text-uppercase">Next <i class="bi bi-chevron-double-right"></i></button>
        </div>
    </div>

    <!-- Step 3 form fields here -->
    <div class="step step-3">
      <div class="step-inner-content mb-3">


        <div class="col-12 input-group-icon position-relative">
            <i class="bi bi-person input-icon" aria-hidden="true"></i>
            <input type="text" name="title" class="form-control" placeholder="Offer Title" required />
        </div> 

        <fieldset class="border rounded p-3 mb-3">
            <legend class="float-none w-auto px-2 fs-6 fw-semibold">If redemption by entering a promo code in your website checkout process</legend>
                <div id="custom_redemption_process_wrap">
                     <div class="mb-4 input-group-icon position-relative">
                        <i class="bi bi-tag input-icon"></i>
                        <input type="text" 
                        class="form-control" 
                        id="custom_redemption_code" 
                        name="custom_redemption_code" 
                        value=""
                        placeholder="Enter custom promo code">
                    </div>
                    <div class="mb-4 input-group-icon position-relative">
                       <i class="bi bi-link-45deg input-icon"></i>
                       <input type="url" 
                       class="form-control" 
                       id="custom_redemption_url" 
                       name="custom_redemption_url" 
                       value=""
                       placeholder="https://example.com/checkout (optional)">
                   </div>
                   <div class="mb-4 input-group-icon position-relative">
                      <i class="bi bi-info-circle input-icon"></i>
                      <textarea class="form-control" 
                      id="custom_redemption_description" 
                      name="custom_redemption_description" 
                      rows="3"
                      placeholder="Enter any special instructions for redemption..."></textarea>
                  </div>
                </div>
        </fieldset>


  <fieldset class="custom-offer-wrapper border rounded p-3 mb-3">
    <legend class="float-none w-auto px-2 fs-6 fw-semibold">Fixed Price, Percentage or Custom Offer?</legend>

    <div class="row">
        <div class="col-md-4 mb-3 input-group-icon">
            <div class="position-relative">
                <i class="bi bi-currency-dollar input-icon"></i>
                <input type="number" 
                step="0.01" 
                class="form-control" 
                id="full_price" 
                name="full_price" 
                value="0" 
                required
                placeholder="Full price (0.00)">
            </div>
        </div>

        <div class="col-md-4 mb-3 input-group-icon">
            <div class="position-relative">
                <i class="bi bi-currency-dollar input-icon"></i>
                <input type="number" 
                step="0.01" 
                class="form-control" 
                id="discount_price" 
                name="discount_price" 
                value="0" 
                required
                placeholder="Discounted price (0.00)">
            </div>
        </div>

        <div class="col-md-4 mb-3 input-group-icon">
            <div class="position-relative">
               <i class="bi bi-percent input-icon"></i>
               <input type="number" 
               step="0.01" 
               class="form-control"
               id="percentage_off"
               name="percentage_off" 
               value=""
               placeholder="Percentage off (%)">
            </div>
       </div>
   </div>

   <div class="mb-3 input-group-icon position-relative">
    <i class="bi bi-pencil-square input-icon"></i>
    <textarea class="form-control" 
    id="custom_saving" 
    name="custom_saving" 
    rows="2"
    placeholder="Custom saving text (max 128 characters)"></textarea>
</div>
</fieldset>










<div class="mb-4">
    <fieldset class="with-bg">
        <legend class="fs-6 text-muted"><i class="bi bi-globe"></i> Offer Address Type</legend>
        <div class="mb-1 @error('offer_address_type') is-invalid @enderror">
            <div class="d-flex flex-wrap gap-sm-3 gap-1">
                @foreach (\App\Enums\OfferAddressType::labels() as $value => $label)
                <div class="form-check form-check-inline">
                    <input class="form-check-input offer_address-type-radio" type="radio" 
                    name="offer_address_type" id="offer_address_type_{{ $value }}" 
                    value="{{ $value }}"
                    {{ old('offer_address_type', \App\Enums\OfferAddressType::ONLINE) == $value ? 'checked' : '' }}>
                    <label class="form-check-label" for="offer_address_type_{{ $value }}">
                        <span class="d-flex align-items-center">
                            {{ $label }}
                        </span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('offer_address_type')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
        </div>
    </fieldset>
    <div id="offerphysicalAddressFields" class="col-12 input-group-icon position-relative mt-4">
        <i class="bi bi-person input-icon" aria-hidden="true"></i>
        <input type="text" id="offer_address_search" name="offer_address_search" class="form-control" placeholder="Offer Address" />
    </div> 
    <div id="offer_address_lookup_status" class="mt-2">
    </div>
</div>


<div class="mb-3">
    <input type="file" name="image_files[]" id="image_files" accept="image/*" class="form-control" multiple>
</div>



<div class="mb-4">
    <fieldset class="with-bg">
        <!-- Required Terms Agreement -->
        <div class="form-check mb-2">
            <input class="form-check-input @error('terms_agreement') is-invalid @enderror" 
            type="checkbox" 
            id="terms_agreement" 
            name="terms_agreement"
            required>
            <label class="form-check-label" for="terms_agreement">
                I confirm the above details are correct and agree to Xhale's 
                <a href="{{ route('supportandlegal') }}?tab=faq" target="_blank">business partner terms & conditions</a>.
            </label>
            @error('terms_agreement')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        
        <!-- Optional Newsletter Subscription -->
        <div class="form-check">
            <input class="form-check-input" 
            type="checkbox" 
            id="newsletter_subscription" 
            name="newsletter_subscription">
            <label class="form-check-label" for="newsletter_subscription">
                Subscribe me to Xhale's business partner updates and news (optional)
            </label>
        </div>
    </fieldset>
</div>

</div>

<div class="action-btns">
    <button type="button" class="btn btn-primary prev-step"><i class="bi bi-chevron-double-left"></i> Previous</button>
    <button type="submit" class="btn btn-next text-uppercase">Submit</button>
</div>
</div>
</form>
</div>

<!-- <button type="submit" class="btn-next text-uppercase" aria-label="Next Step">NEXT &raquo;</button> -->
<p class="terms-text">More information on <a href="{{ route('termsandconditions') }}" tabindex="0" target="_blank">terms and conditions <i class="bi bi-chevron-right"></i> </a></p>
</div>
</div>
</div>

</section>


<section class="real-results">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 px-0">
                <div class="result-image-box">
                    <img src="images/border-image.png" alt="">
                </div>
            </div>
            <div class="col-lg-7 px-0">
                <div class="results-info">
                    <div class="hero-content-box">
                        <div class="bg-title">
                            <div class="yellow-bg text-uppercase">FREE MARKETING</div>
                            <div class="black-bg text-uppercase">REAL RESULTS</div>
                        </div>
                    </div>
                    <div class="results-content-box">
                        <p>Partnering with Xhale means aligning your business with a growing wellness community
                            that’s
                            passionate about supporting local. We give you free exposure across our platform,
                            social
                            media, and email campaigns — plus access to thousands of motivated subscribers
                            actively
                            looking for exclusive offers from businesses like yours. It’s marketing that doesn’t
                            cost
                        you a cent, just a great deal for our members. </p>
                        <p class="fw-bold">Let us drive real traffic, engagement, and loyalty straight to
                        your door.</p>
                        <div class="action-btn">
                            @include('global.login-signup-btn')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="second-tabs-sec pt-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="work-tabs-inner">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="work-tab" data-bs-toggle="tab"
                            data-bs-target="#work-tab-pane" type="button" role="tab"
                            aria-controls="work-tab-pane" aria-selected="false">HOW IT WORKS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rewards-tab" data-bs-toggle="tab"
                            data-bs-target="#rewards-tab-pane" type="button" role="tab"
                            aria-controls="rewards-tab-pane" aria-selected="false">REWARDS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="community-tab" data-bs-toggle="tab"
                            data-bs-target="#community-tab-pane" type="button" role="tab"
                            aria-controls="community-tab-pane" aria-selected="true">COMMUNITY</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lifestyle-tab" data-bs-toggle="tab"
                            data-bs-target="#lifestyle-tab-pane" type="button" role="tab"
                            aria-controls="lifestyle-tab-pane" aria-selected="false">LIFESTYLE</button>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="work-tab-pane" role="tabpanel" aria-labelledby="work-tab"
                        tabindex="0">
                        <div class="work-tab-content-col">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-12 order-lg-1 order-2">
                                    <div class="work-tab-img">
                                        <img src="images/Mobile-App-Members-portal.png">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12 order-lg-2 order-1">
                                    <div class="work-tab-content text-lg-start text-center">
                                        <div class="highlight-title">
                                            <h3>Unlock Savings, Rewards on <span>iOS!</span></h3>
                                        </div>
                                        <p>Discover the ultimate app for exclusive deals, wellness rewards,
                                            and cash giveaways—right at your fingertips. Xhale is optimized
                                            for your iOS device, ensuring a smooth and seamless experience.
                                        </p>
                                        <p>Tap into a world of savings and exciting prizes with just one
                                        click. Start your journey to self-care and rewards today</p>

                                        <div class="action-btn">
                                            @include('global.login-signup-btn')
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="rewards-tab-pane" role="tabpanel"
                    aria-labelledby="rewards-tab" tabindex="0">

                    <div class="work-tab-content-col">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-12 order-lg-1 order-2">
                                <div class="work-tab-img">
                                    <img src="images/Hand-Holding-Phone.png">
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 order-lg-2 order-1">
                                <div class="work-tab-content text-lg-start text-center">
                                    <div class="highlight-title">
                                        <h3>What are the <span>Rewards</span></h3>
                                    </div>
                                    <p>Xhale is a modern day coupon offering discounts and a monthly 10k
                                        giveaway to subscribers. Businesses get free advertising on our
                                    platforms and are entered into a monthly 1k giveaway.</p>

                                    <div class="action-btn">
                                        @include('global.login-signup-btn')
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="tab-pane fade show active" id="community-tab-pane" role="tabpanel"
                aria-labelledby="community-tab" tabindex="0">

                <div class="work-tab-content-col">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-12 order-lg-1 order-2">
                            <div class="work-tab-img">
                                <img src="images/diverse-people.png">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 order-lg-2 order-1">
                            <div class="work-tab-content text-lg-start text-center">
                                <div class="highlight-title">
                                    <h3>BUILDING A <span>COMMUNITY</span></h3>
                                </div>
                                <p>Xhale connects small businesses with a network of engaged
                                    subscribers, helping businesses grow while offering users
                                exclusive deals and rewards.</p>

                                <div class="action-btn">
                                    @include('global.login-signup-btn')
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="lifestyle-tab-pane" role="tabpanel"
            aria-labelledby="lifestyle-tab" tabindex="0">

            <div class="work-tab-content-col">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12 order-lg-1 order-2">
                        <div class="work-tab-img">
                            <img src="images/Happy-Couple.png">
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 order-lg-2 order-1">
                        <div class="work-tab-content text-lg-start text-center">
                            <div class="highlight-title">
                                <h3>Financial Relief for <span>YOU!</span></h3>
                            </div>
                            <p>Xhale offers financial relief to everyday Australians by making
                                self-care and treating yourself more affordable through
                            exclusive discounts and rewards.</p>

                            <div class="action-btn">
                                @include('global.login-signup-btn')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</section>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBA6MvU0Rnsol0PvKQmm5QXmw0q6rUJ4lE&libraries=places"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('customimage');
        const fileLabel = document.querySelector('.custom-file-label');

        fileInput.addEventListener('change', function(event) {
            const fileName = event.target.files.length > 0 ? event.target.files[0].name : 'Add Logo';
            fileLabel.textContent = fileName;
        });
    });
</script>



<script>
    $(document).ready(function() {
        // Initialize Business Address functionality
        initAddressAutocomplete(
            '#businessphysicalAddressFields',
            '#business_address_search',
            '#business_address_lookup_status',
            '.address-type-radio'
            );

        // Initialize Offer Address functionality
        initAddressAutocomplete(
            '#offerphysicalAddressFields',
            '#offer_address_search',
            '#offer_address_lookup_status',
            '.offer_address-type-radio'
            );

        function initAddressAutocomplete(containerId, inputId, statusDivId, radioSelector) {
            const $addressFieldsContainer = $(containerId);
            const $addressSearchInput = $(inputId);
            const $statusDiv = $(statusDivId);
            let autocomplete = null;

            function populateAddressFields(place) {
                $addressSearchInput.val(place.formatted_address || '');
                $statusDiv.empty();

                if (!place.address_components) return;
                
                const componentMapping = {
                    'street_number': 'street_number',
                    'route': 'street_name',
                    'locality': 'city',
                    'administrative_area_level_1': 'state',
                    'postal_code': 'postcode',
                    'country': 'country'
                };

                place.address_components.forEach(component => {
                    component.types.forEach(type => {
                        if (componentMapping[type]) {
                            const customName = componentMapping[type];
                            const value = component.short_name;
                            const fieldPrefix = containerId.includes('offer') ? 'offer_' : 'business_';
                            $statusDiv.append(`
                                <input type="hidden" name="${fieldPrefix}address_components[${customName}]" value="${value}">
                            `);
                        }
                    });
                });

                if (place.geometry?.location) {
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    const fieldPrefix = containerId.includes('offer') ? 'offer_' : 'business_';
                    $statusDiv.append(`
                        <input type="hidden" name="${fieldPrefix}latitude" value="${lat}">
                        <input type="hidden" name="${fieldPrefix}longitude" value="${lng}">
                    `);
                }
            }

            function initAutocomplete() {
                if (autocomplete) return;
                
                autocomplete = new google.maps.places.Autocomplete($addressSearchInput[0], {
                    types: ['address'],
                    componentRestrictions: { 'country': ['au'] },
                    fields: ['address_components', 'formatted_address', 'geometry']
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $statusDiv.html('<div class="alert alert-warning">No details available for this address</div>');
                        return;
                    }
                    populateAddressFields(place);
                });
            }

            function togglePhysicalAddressFields() {
                const selectedType = $(radioSelector + ':checked').val();
                
                if (selectedType === 'physical_location') {
                    $addressFieldsContainer.show();
                    $addressSearchInput.prop('required', true);
                    initAutocomplete();
                } else {
                    $addressFieldsContainer.hide();
                    $addressSearchInput.prop('required', false).val('');
                    $statusDiv.empty();
                    
                    if (autocomplete) {
                        google.maps.event.clearInstanceListeners($addressSearchInput[0]);
                        autocomplete = null;
                    }
                }
            }

            $(radioSelector).on('change', togglePhysicalAddressFields);
            togglePhysicalAddressFields();
        }
    });
</script>



@endsection
