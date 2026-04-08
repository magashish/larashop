@extends('layouts.consumer')
@section('content')


<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-lg-4">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/Happy_winner_of_a_new_car.webp') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/discount_mobile.webp') }}" class="d-lg-none d-block" alt="Banner Image">
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
                        {{-- <input class="btn reset-btn" onclick="resetfunction()"  type="reset"> --}}
                        <a href="{{ url()->current() }}" class="reset-btn">Reset</a>
                        <input class="btn submit-btn d-inline-block" type="submit" value="FILTER">
                    </div>  
                </div>
                <div class="col-lg-8">
                    <div class="search-btn d-flex align-items-center">
                        <input class="form-control" type="search" placeholder="Search for your offers here" name="search" value="{{ request('search') }}">
                        <button class="ms-3 d-lg-none" type="submit"><img src="{{ asset('images/search-icon.svg') }}" class="img-fluid"></button>
                    </div>
                </div>
            </div>                    
        </div>

    </form>

   @include('consumer.blocks.category-organisations-offers', ['categories' => $categories ,'business_id'=>$business_id,'offerclass' => 'offer-detail' ])


</div>
</section>

@include('global.featured-offer-slider')


@include('consumer.blocks.advertisement')
@include('consumer.blocks.social-feeds')


<style>
 .has-org {
    background: #fad604 !important;
}

</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function resetfunction() {
        document.getElementById("offers-search").reset();
    }
    jQuery(document).ready(function() {
      $('.load-all-offers-btn').on('click', function() {
        var button = $(this);
        var targetTabId = button.data('target-tab');
        var hiddenOffers = $(targetTabId).find('.offer-item-col.d-none');
        hiddenOffers.removeClass('d-none');
        button.hide();
    });
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
