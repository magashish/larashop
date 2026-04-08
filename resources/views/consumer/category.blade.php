@extends('layouts.consumer')
@section('content')



<!-- Page Banner Sec Start -->
<section class="image-banner-sec">
    <div class="container">
        <div class="inner-content">
           <img src="{{ asset('images/Happy_winner_of_a_new_car.webp') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/discount_mobile.webp') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title">
            <div class="yellow-bg">FEEL GOOD</div>
            <div class="black-bg">SPEND LESS!</div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->





<!-- Categories & Offers Section -->
<section class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
             <div class="col-12">
                <div class="highlight-title d-lg-flex d-none">
                    <h2>SHOP BY <span>CATEGORY</span></h2>
                </div>
                <div class="bg-title small-title d-lg-none d-block">
                    <div class="yellow-bg">SHOP BY</div>
                    <div class="black-bg">CATEGORY</div>
                </div>
            </div>



            @include('consumer.blocks.category-organisations-offers', ['categories' => $categories ,'offerclass' => 'home-offer-detail' ])


        </div>
    </div>
</section>







<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.load-all-offers-btn').on('click', function() {
            var button = $(this);
            var targetTabId = button.data('target-tab'); 
            var hiddenOffers = $(targetTabId).find('.offer-item-col.d-none');
            hiddenOffers.removeClass('d-none');
            button.hide();
        });
    });
</script>


@endsection
