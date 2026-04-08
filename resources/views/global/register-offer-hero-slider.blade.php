 <div class="hero-slider-section">
     <div class="hero-slider hero-sliderLogin-sec" id="hero-slider-login-sec">
        @foreach (is_featured_offers() as $offer)
        <div class="slider-item">
            <div class="innner-content">
                <div class="item-content">
                    <div class="sec-tag d-flex align-item-center gap-2">
                        @if ($offer->categories->isNotEmpty())
                        @php
                        $category = $offer->categories->first(); 
                        @endphp
                        @if ($category->icon)
                        <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}" class="img-fluid">
                        @endif
                        <p class="mb-0">{{ $category->name }}</p>
                        @endif
                    </div>
                    <h3 class="my-2">
                        @if ($offer->organisation)
                        {{ $offer->organisation->title }}
                        @else
                        Organization Name
                        @endif
                    </h3>
                    <p> {{ html_entity_decode($offer->title) }}</p>
                    @if(auth()->check())
                    {{-- Logged-in → go to discounts --}}
                    <a href="{{ route('discounts') }}?busness={{ $offer->organisation->id }}" 
                     class="btn btn-with-icon d-flex align-items-center justify-content-between">
                     EXPLORE NOW
                     <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 
                            0s-12.5 32.8 0 45.3L338.8 224H32c-17.7 0-32 14.3-32 
                            32s14.3 32 32 32h306.7L233.4 393.4c-12.5 12.5-12.5 
                            32.8 0 45.3s32.8 12.5 45.3 
                            0l160-160z"/>
                        </svg>
                    </span>
                </a>
                @else
                {{-- Logged-out → show Bootstrap modal --}}
                <a href="javascript:void(0)" 
                class="btn btn-with-icon d-flex align-items-center justify-content-between" 
                data-bs-toggle="modal" data-bs-target="#subscribeModal">
                EXPLORE NOW
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path d="M438.6 278.6c12.5-12.5 12.5-32.8 
                        0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 
                        0s-12.5 32.8 0 45.3L338.8 224H32c-17.7 
                        0-32 14.3-32 32s14.3 32 32 
                        32h306.7L233.4 393.4c-12.5 
                        12.5-12.5 32.8 0 45.3s32.8 
                        12.5 45.3 0l160-160z"/>
                    </svg>
                </span>
            </a>
            @endif

        </div>
        <div class="item-img">
          @php
          $firstAttachment = $offer->attachments->first();
          @endphp
          @if ($firstAttachment) 
          <img src="{{ Storage::url($firstAttachment->file_image) }}" class="img-thumbnail" alt="Offer Image">
          @else
          <img src="{{ asset('images/ubx.png') }}" class="img-fluid" alt="Default Offer Image">
          @endif
      </div>
  </div>
</div>
@endforeach
</div>
</div>




{{-- Welcome Modal HTML (placed just before closing </body>) --}}
<div class="modal welcomeModal fade" id="subscribeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            <div class="modal-body p-0">
                <div class="row mx-0">
                    <div class="col-sm-6 px-0 d-sm-block d-none">
                        <div class="welcome-wrapper h-100">
                            <div class="sec-head text-uppercase text-center">Welcome</div>
                            <div class="bottom-content pt-sm-0 pt-5">
                                <div class="welcome-img">
                                    <div class="mob-logo">
                                        <img src="{{ asset('images/logo-full-black.svg') }}" class="img-fluid d-sm-none d-block">
                                    </div>
                                    <img src="{{ asset('images/Lady2.png') }}" class="d-sm-block d-none img-fluid">
                                    <img src="{{ asset('images/Lady.png') }}" class="d-sm-none d-block img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 px-0">
                        <div class="welcome-content text-center h-100">
                            <div class="modal-logo text-center mb-5">
                                <img src="{{ asset('images/logo-light.svg') }}" class="img-fluid mx-auto">
                                <p class="mt-3 mb-0 text-white">
                                  Join today and unlock exclusive discounts, exciting rewards, and special giveaways—just for our members.
                              </p>
                            </div>
                            <div class="action-btns">
                                <a href="{{ route('register') }}" class="d-block signup">Sign Up</a>
                                <a href="{{ route('businessportal') }}" class="d-block login business-signup-btn my-3 text-white">Business Sign Up</a>
                                <a href="{{ route('login') }}" class="d-block login">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END Welcome Modal HTML --}}

