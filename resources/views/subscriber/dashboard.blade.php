@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="row g-md-5">
            <div class="col-lg-8">
                <div class="user-info-box">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="profile-img h-100">
                                <label for="imageInput">
                                  <div class="image-upload-box d-flex align-items-center justify-content-center position-relative" id="previewBox">
                                    @if(Auth::user()->profile_image)
                                    <img id="previewImage" src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="img-fluid previewImage" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                    <img id="previewImage" src="{{ asset('images/upload.png') }}" alt="Preview" class="img-fluid previewImage">
                                    @endif
                                    <div class="edit-icon"><i class="bi bi-pencil-square"></i></div>
                                </div>
                            </label>
                            <input type="file" id="imageInput" accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="profile-info ps-sm-0 ps-3 pe-xl-5 pe-sm-3 pe-3 py-sm-5 py-4">
                            <h2 class="text-uppercase mb-4 ps-sm-4">Hi, <span>{{ Auth::user()->name }}</span></h2>
                            <p class="mb-0">Welcome back! 🎉 This is your personal Xhale dashboard — here you can view your entries into our latest cash and prize draws, stay updated on upcoming events, and easily manage your profile details. Let’s make your Xhale experience even better!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="entries-box mt-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="box-inner h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="box-title text-center mb-sm-3 mb-2">
                                    <h6 class="mb-0 text-uppercase">GET MORE ENTRIES</h6>
                                </div>
                                <div class="box-content h-100">
                                    <div class="entries-package-slider">
                                        @foreach($plans as $plan)
                                        @php
                                        $price = number_format($plan->price, 2, '.', '');
                                        [$whole, $decimal] = explode('.', $price);
                                        @endphp
                                        <div class="item-box {{ strtolower($plan->name) }}">
                                            <div class="plan-name text-uppercase mb-2">
                                                {{ $plan->name }}
                                            </div>
                                            <div class="plan-content">
                                                <div class="plan-price">
                                                     <sup>$</sup>{{ $whole }}<sup>.{{ $decimal }}</sup>
                                                    <p>{{ $plan->price_description }}</p>
                                                    <div class="plan-details">{!! $plan->description !!}</div>
                                                </div>
                                                <div class="pricing-button">
                                                    @php
                                                    $buttonText = "LET'S DO IT";
                                                    $actionRoute = route('subscription.show', $plan->id);
                                                    @endphp
                                                    <a class="text-uppercase" href="{{ $actionRoute }}">
                                                        {{ $buttonText }}
                                                    </a>
                                                </div>
                                                <div class="plicing-info"> {!! $plan->sub_description !!}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="box-inner h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="box-title text-center mb-sm-3 mb-2">
                                    <h6 class="mb-0 text-uppercase">TOTAL ENTRIES</h6>
                                </div>
                                {!! generate_user_progress_circle() !!}
                         {{--        @php
                                $entriesData = current_user_entries_percentage();
                                $percentage = $entriesData['percentage'];
                                $dashoffset = 377 * (1 - min($percentage, 100) / 100);
                                @endphp
                                <div class="box-content d-flex align-items-center justify-content-center h-100">
                                    <div class="progress-circle">
                                        <svg width="150" height="150">
                                            <circle class="progress-bg" cx="75" cy="75" r="60" />
                                            <circle class="progress-value" 
                                            cx="75" 
                                            cy="75" 
                                            r="60"
                                            stroke-dasharray="377"
                                            stroke-dashoffset="{{ $dashoffset }}" />
                                        </svg>
                                        <div class="progress-text">
                                            {{ round($percentage) }}%<br>
                                            <small>{{ $entriesData['current_entries'] }}/{{ $entriesData['top_entries'] }}</small>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="user-fav mt-4">
                <div class="sec-head">
                    <h2 class="mb-0 ps-4"><span class="text-uppercase">Featured Offers</span></h2>
                    <div class="user-fav-slider explore-now-outer">
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
                                    <a href="{{ route('discounts') }}?busness={{ $offer->organisation->id }}" class="btn btn-with-icon d-flex align-items-center justify-content-between">
                                        EXPLORE NOW
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                                        </span>
                                    </a>
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
            </div>
        </div>
        <div class="col-lg-4">

            <div class="calendar-wrapper mt-md-0 mt-5 mb-5">
                 <div id="calendar"></div>
                 <div class="cash-giveaways-wrapper" id="calendar-result"></div>
            </div>

            {{-- <div class="cash-giveaways-wrapper" id="calendar-result"></div> --}}


       </div>
   </div>
</div>
</div>



@endsection