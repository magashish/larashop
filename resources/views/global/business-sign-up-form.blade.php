
               
                <div class="form-outer-wrapper mx-auto mt-5 pt-4 business-multistep-wrapper">
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

                            <div class="col-12 input-group-icon position-relative">
                               <i class="bi bi-instagram  input-icon"></i>
                               <input type="url" name="instagram" id="instagram"
                               class="form-control @error('instagram') is-invalid @enderror"
                               value="{{ old('instagram') }}"
                               placeholder="https://instagram.com/yourprofile">

                           </div>


                           <div class="col-12 input-group-icon position-relative">
                             <i class="bi bi-facebook  input-icon"></i>
                             <input type="url" name="facebook" id="facebook"
                             class="form-control @error('facebook') is-invalid @enderror"
                             value="{{ old('facebook') }}"
                             placeholder="https://facebook.com/yourpage">

                         </div>


                         <div class="col-12 input-group-icon position-relative">
                             <i class="bi bi-youtube  input-icon"></i>
                             <input type="url" name="youtube" id="youtube"
                             class="form-control @error('youtube') is-invalid @enderror"
                             value="{{ old('youtube') }}"
                             placeholder="https://youtube.com/yourchannel">

                         </div>


                         <div class="col-12 input-group-icon position-relative">
                          <i class="bi bi-tiktok  input-icon"></i>
                          <input type="url" name="tiktok" id="tiktok"
                          class="form-control @error('tiktok') is-invalid @enderror"
                          value="{{ old('tiktok') }}"
                          placeholder="https://tiktok.com/@yourprofile">

                      </div>


                      <div class="col-12 input-group-icon position-relative">
                        <i class="bi bi-twitter-x  input-icon"></i>
                        <input type="url" name="twitter" id="twitter"
                        class="form-control @error('twitter') is-invalid @enderror"
                        value="{{ old('twitter') }}"
                        placeholder="https://twitter.com/yourhandle">
                        @error('twitter')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 input-group-icon position-relative d-block">
                        <fieldset class="with-bg w-100">
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

                    <div class="col-12 input-group-icon position-relative">
                        <div class="custom-file-container w-100">
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
                    <li><i class="bi bi-circle-fill me-1"></i> Be at least 8 characters long</li>
                    <li><i class="bi bi-circle-fill me-1"></i> Be at max 32 characters</li>
                    <li><i class="bi bi-circle-fill me-1"></i> Include at least one UPPERCASE letter</li>
                    <li><i class="bi bi-circle-fill me-1"></i> Include at least one lowercase letter</li>
                    <li><i class="bi bi-circle-fill me-1"></i> Include at least one number</li>
                    <li><i class="bi bi-circle-fill me-1"></i> Include at least one special character, such as ~`!@#$%^&amp;*()?&lt;&gt;{}|[]\/-+</li>
                </ul>
            </div>
                    
            <!-- Form -->
            <div class="row">
                <div class="col-12 input-group-icon position-relative">
                    <i class="bi bi-person input-icon" aria-hidden="true"></i>
                    <input type="text" name="first_name" class="form-control" placeholder="First Name"
                    aria-label="First Name" required />
                </div>
                <div class="col-12 input-group-icon position-relative">
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

                <div class="col-12 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password" type="text" class="form-control" name="password" placeholder="Password" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-12 input-group-icon position-relative">
                    <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                    <input id="password-confirm" type="text" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                    <div class="invalid-feedback"></div>
                </div>


                <div class="col-12 input-group-icon position-relative">
                    <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                    <input name="mobile" type="tel" class="form-control" placeholder="Phone Number"
                    aria-label="Phone Number" required />
                </div>

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

<!-- <button type="submit" class="btn-next text-uppercase" aria-label="Next Step">NEXT &raquo;</button> -->
<p class="terms-text">More information on <a href="{{ route('supportandlegal') }}?tab=faq" tabindex="0" target="_blank">terms and conditions <i class="bi bi-chevron-right"></i> </a></p>

</div>


