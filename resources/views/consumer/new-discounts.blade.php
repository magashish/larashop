@extends('layouts.consumer')
@section('content')


<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-lg-4">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/discount.png') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/3423423.jpg') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title">
            <div class="yellow-bg">FEEL GOOD</div>
            <div class="black-bg"><h1>SPEND LESS!</h1></div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->


<!-- Categories Sec Start -->
<section class="cat-listing-sec pt-0">            
    <div class="container">   

     <form method="GET" id="offers-search">             
        <!-- Filter Sec Start -->
        <div class="filter-wrapper mb-4 ">
            <div class="row align-items-center">
                <div class="col-md-4 d-lg-block d-none">
                    <div class="filter-btns d-flex align-items-center gap-1">
                        <input class="btn reset-btn" onclick="resetfunction()"  type="reset">
                        <input class="btn submit-btn d-inline-block" type="submit" value="FILTER">
                    </div>  
                </div>
                <div class="col-lg-8">
                    <input class="form-control" type="search" placeholder="Search for your offers here" name="search" value="{{ request('search') }}">                   
                </div>
            </div>                    
        </div>

    </form>
    <!-- Filter Sec End -->



    <!-- Categories & Offers Section -->
    <div class="categories-tabs-sec mb-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 tabs-content-col">
                    <nav class="categories-tabs">
                        <div class="scroll-wrapper">
                            <button class="scroll-btn left" aria-label="Scroll left">‹</button>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                @php $firstCategory = $categories->sortBy('position')->first(); @endphp
                                @foreach ($categories->sortBy('position') as $index => $category)
                                <button class="nav-link {{ $category->id == $firstCategory->id ? 'active' : '' }}"
                                    id="nav-{{ $category->id }}-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-{{ $category->id }}"
                                    type="button" role="tab"
                                    aria-controls="nav-{{ $category->id }}"
                                    aria-selected="{{ $category->id == $firstCategory->id ? 'true' : 'false' }}">
                                    @if ($category->icon)
                                    <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }} icon">
                                    @endif
                                    <p>{{ strtoupper($category->name) }}</p>
                                </button>
                                @endforeach
                            </div>
                            <button class="scroll-btn right" aria-label="Scroll right">›</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                      @foreach ($categories->sortBy('position') as $index => $category)
                      <div class="tab-pane fade {{ $category->id == $firstCategory->id ? 'show active' : '' }}"
                         id="nav-{{ $category->id }}"
                         role="tabpanel"
                         aria-labelledby="nav-{{ $category->id }}-tab"
                         tabindex="0">
                         <div class="tab-content-inner">
                            <h2 class="d-lg-block d-none">{{ strtoupper($category->name) }}</h2>
                            @php
                            $allLiveOffers = $category->offers->filter(function($offer) {
                                $today = \Carbon\Carbon::now();
                                return $offer->start_date <= $today && $offer->end_date >= $today;
                            });
                            $organisationsToShowInitially = 10;
                            $totalOffers = $allLiveOffers->count();

                            if ($totalOffers < 8) {
                                $sliderCount = 1;
                            } elseif ($totalOffers >= 8 && $totalOffers <= 9) {
                                $sliderCount = 1;
                            } elseif ($totalOffers >= 14 && $totalOffers <= 20) {
                                $sliderCount = 2;
                            } elseif ($totalOffers >= 21) {
                                $sliderCount = 3;
                            } else {
                                $sliderCount = 1;
                            }
                            $offerChunks = $sliderCount > 0 ? $allLiveOffers->chunk(ceil($totalOffers / $sliderCount)) : collect([$allLiveOffers]);
                            @endphp

                            <!-- Desktop Grid -->
                            <div class="d-lg-block d-none">
                            @if($allLiveOffers->isNotEmpty())
                            <div class="row desktop-offers offers-container">
                                @foreach($allLiveOffers as $index => $offer)
                                @php
                                $organisation = $offer->organisation;
                                $firstAttachment = $offer->attachments->first() ?? null;
                                $organisationLogo = $organisation && $organisation->image && Storage::disk('public')->exists($organisation->image) ? Storage::url($organisation->image) : asset('images/ubx.png');
                                $categoryTitle = $offer->categories->first() ? $offer->categories->first()->name : 'N/A';
                                @endphp
                                <div class="{{  $index }} offer-item-{{ $organisation->id }} col offer-item-col {{ $index >= $organisationsToShowInitially ? 'd-none' : '' }}">
                                    <div class="offer-col offer-detail" data-id="{{ $organisation->id }}">
                                        @if ($offer->percentage_off && $offer->percentage_off > 0)
                                        <div class="offer-percent">{{ round($offer->percentage_off) }}% Off</div>
                                        @endif
                                        <div class="offer-col-inner">
                                            <div class="ubx-col">
                                                <img src="{{ $organisationLogo }}" alt="{{ $organisation->title ?? 'Logo' }}">
                                                <span>{{ $categoryTitle }}</span>
                                            </div>
                                            <div class="offer-col-img">
                                                @if ($firstAttachment)
                                                <img src="{{ Storage::url($firstAttachment->file_image) }}" class="cat-offer-img" alt="Offer Image">
                                                @else
                                                <img src="{{ asset('images/ubx.png') }}" class="cat-offer-img" alt="Default Offer Image">
                                                @endif
                                            </div>
                                            <div class="ubx-bottom">{{ $organisation->title ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="no-offers-message-container">
                                <div class="col-12">
                                    <p class="ps-5">No offers available for this category yet.</p>
                                </div>
                            </div>
                            @endif

                            <!-- Desktop Load More -->
                            @if($totalOffers > $organisationsToShowInitially)
                            <div class="action-btn text-center mt-4">
                                <a class="load-all-offers-btn signup" data-target-tab="#nav-{{ $category->id }}">View More</a>
                            </div>
                            @endif
                            </div>

                            <!-- Mobile Slider -->
                            @if($sliderCount > 0)
                            <div class="mobile-offers mobile-offers-slider d-lg-none">
                                <h2>{{ strtoupper($category->name) }}</h2>
                                @foreach($offerChunks as $chunkIndex => $chunk)
                                <div class="offer-slide offer-slide-{{ $chunkIndex }}">
                                    @foreach($chunk as $offer)
                                    @php
                                    $organisation = $offer->organisation;
                                    $firstAttachment = $offer->attachments->first() ?? null;
                                    $organisationLogo = $organisation && $organisation->image && Storage::disk('public')->exists($organisation->image) ? Storage::url($organisation->image) : asset('images/ubx.png');
                                    $categoryTitle = $offer->categories->first() ? $offer->categories->first()->name : 'N/A';
                                    @endphp
                                    <div class="col offer-item-{{ $organisation->id }} offer-item-col">
                                        <div class="offer-col offer-detail" data-id="{{ $organisation->id }}">
                                            @if ($offer->percentage_off && $offer->percentage_off > 0)
                                            <div class="offer-percent">{{ round($offer->percentage_off) }}% Off</div>
                                            @endif
                                            <div class="offer-col-inner">
                                                <div class="ubx-col">
                                                    <img src="{{ $organisationLogo }}" alt="{{ $organisation->title ?? 'Logo' }}">
                                                    <span>{{ $categoryTitle }}</span>
                                                </div>
                                                <div class="offer-col-img">
                                                    @if ($firstAttachment)
                                                    <img src="{{ Storage::url($firstAttachment->file_image) }}" class="cat-offer-img" alt="Offer Image">
                                                    @else
                                                    <img src="{{ asset('images/ubx.png') }}" class="cat-offer-img" alt="Default Offer Image">
                                                    @endif
                                                </div>
                                                <div class="ubx-bottom">{{ $organisation->title ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
</section>

@include('consumer.blocks.advertisement')
@include('consumer.blocks.social-feeds')



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function resetfunction() {
      document.getElementById("offers-search").reset();
  }

  $('.load-all-offers-btn').on('click', function() {
    var button = $(this);
    var targetTabId = button.data('target-tab');
    var hiddenOffers = $(targetTabId).find('.offer-item-col.d-none');
    hiddenOffers.removeClass('d-none');
    button.hide();
});


</script>

@if($popupcheck && $business_id)
<script>
    $(document).ready(function() {
        @if ($popupcheck && $business_id)
        const businessId = "{{ $business_id }}";
        $.ajax({
            url: `/organisation/${businessId}?check_only_auth=true`,
            method: 'GET',
            success: function(response) {
                if (response.logged_in) {
                    $.ajax({
                        url: `/organisation/${businessId}`,
                        method: 'GET',
                        success: function(responseHtml) {
                            $('body').append(responseHtml);
                            const $offerModal = $('#offerModal');
                            $offerModal.one('hidden.bs.modal', function() {
                                $(this).remove();
                            });
                            $offerModal.modal('show');
                            const $tabs = $('.work-tabs-inner .nav-tabs');
                            if ($(window).width() < 990) {
                              if (!$tabs.hasClass('slick-initialized')) {
                                $tabs.slick({
                                  slidesToShow: 1,
                                  slidesToScroll: 1,
                                  arrows: true,
                                  dots: false,
                                  infinite: false,
                                  variableWidth: true
                              });
                            }
                        } else {
                          if ($tabs.hasClass('slick-initialized')) {
                            $tabs.slick('unslick');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.log(xhr.responseText);
                    alert('Failed to load offer details. Please try again.');
                }
            });
                } else {
                    window.location.href = '{{ route('register') }}';
                }
            },
            error: function(xhr, status, error) {
                alert('Failed to load offer details. Please try again.');
            }
        });
        @endif
    });
</script>
@endif

@endsection
