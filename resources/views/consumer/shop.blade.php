@extends('layouts.consumer')
@section('content')


<!-- Page Banner Sec Start -->
<section class="image-banner-sec shopfilter-banner">
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


<!-- Categories Sec Start -->
<section class="cat-listing-sec pt-0">            
    <div class="container">   

       <form method="POST" id="offers-search">          

        <div class="inner-wrapper px-xl-5 px-3 py-xl-4 py-3">
            <div class="row">
                <div class="col-12 order-lg-1 order-2">

                    <!-- Filter Sec Start -->
                    <div class="filter-wrapper mb-4 mt-lg-0 mt-4">
                        <div class="row align-items-center g-lg-5 g-1 filter-btns-reset-submit ">
                            <div class="col-xl-4 col-md-12 shop-btns-reset-submit">
                                <div class="filter-btns d-flex align-items-center gap-1 ">
                                    <input class="btn reset-btn" onclick="resetfunction()"  type="reset">
                                    <input class="btn submit-btn" type="submit" value="FILTER">
                                </div>  
                            </div>
                            <div class="col-xl-8 col-md-12">
                                <input class="form-control" type="search" placeholder="Search for your questions here..." name="search">                   
                            </div>
                        </div>                    
                    </div>
                    <!-- Filter Sec End -->
                    
                </div>

                <div class="col-lg-4 mt-2 order-lg-2 order-1">
                    <div class="cat-box-outer">
                        <div class="accordion" id="accordionPanelsStayOpenExample">
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                CATEGORIES
                                <span class="plus-icon">+</span>
                                <span class="minus-icon">-</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                            <ul class="list-unstyled">
                             @foreach ($categories->sortBy('position') as $index => $category)
                             <li>
                                <input class="category-checkbox toggle-checkbox" type="checkbox" value="{{ $category->id }}" name="categories[]" id="toggle-{{ $category->id }}">
                                <label class="label-text" s for="toggle-{{ $category->id }}">
                                    {{ strtoupper($category->name) }}
                                    <span class="plus-icon">+</span>
                                    <span class="minus-icon">-</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    BUSINESS STATE
                    <span class="plus-icon">+</span>
                    <span class="minus-icon">-</span>
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
              <div class="accordion-body">



                @php
                $australianStates = [
                    'NSW' => 'New South Wales',
                    'VIC' => 'Victoria',
                    'QLD' => 'Queensland',
                    'WA'  => 'Western Australia',
                    'SA'  => 'South Australia',
                    'TAS' => 'Tasmania',
                    'ACT' => 'Australian Capital Territory',
                    'NT'  => 'Northern Territory',
                ];
                @endphp

                <ul class="list-unstyled">
                    @foreach ($australianStates as $abbr => $state)
                    <li>
                        <input class="address-type-checkbox toggle-checkbox" type="checkbox"
                        value="{{ $abbr }}" name="state[]" id="toggle-{{ $abbr }}">
                        <label class="label-text" for="toggle-{{ $abbr }}">
                            {{ $state }}
                            <span class="plus-icon">+</span>
                            <span class="minus-icon">-</span>
                        </label>
                    </li>
                    @endforeach
                </ul>




            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            BUSINESS STORES
            <span class="plus-icon">+</span>
            <span class="minus-icon">-</span>
        </button>
    </h2>
    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
      <div class="accordion-body">

         @php
         $addressTypes = [
            'online_only' => 'Online',
            'physical_location' => 'In Store',
        ];
        @endphp

        <ul class="list-unstyled">
            @foreach ($addressTypes as $value => $label)
            <li>
                <input class="featured-checkbox toggle-checkbox"
                type="checkbox"
                value="{{ $value }}"
                name="offer_address_type[]"
                id="toggle-{{ $value }}"
                {{ is_array(old('offer_address_type')) && in_array($value, old('offer_address_type')) ? 'checked' : '' }}>
                <label class="label-text" for="toggle-{{ $value }}">
                    {{ $label }}
                    <span class="plus-icon">+</span>
                    <span class="minus-icon">-</span>
                </label>
            </li>
            @endforeach
        </ul>


    </div>
</div>
</div>

<div class="accordion-item">
    <h2 class="accordion-header" id="headingFive">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
        FEATURED SPECIALS
        <span class="plus-icon">+</span>
        <span class="minus-icon">-</span>
    </button>
</h2>
<div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
  <div class="accordion-body">

   @php
   $featuredOptions = [
    'no' => 'No',
    'yes' => 'Yes',
];
@endphp
<ul class="list-unstyled">
    @foreach ($featuredOptions as $value => $label)
    <li>
        <input class="featured-checkbox toggle-checkbox"
        type="checkbox"
        value="{{ $value }}"
        name="is_featured[]"
        id="toggle-{{ $value }}"
        {{ is_array(old('is_featured')) && in_array($value, old('is_featured')) ? 'checked' : '' }}>
        <label class="label-text" for="toggle-{{ $value }}">
            {{ $label }}
            <span class="plus-icon">+</span>
            <span class="minus-icon">-</span>
        </label>
    </li>
    @endforeach
</ul>

</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-8 mt-2 order-3 ">
    <div class="tab-content-inner mt-0 p-0 ps-lg-4 ps-3 pe-lg-0 pe-3">
        <div class="row align-items-end" id="offer-list">
            @include('consumer.partials.organisations-list')
        </div>
        <div  class="row">
          <div class="action-btn text-center mt-3 mb-3">
            <button id="load-more" class="btn btn-with-icon  align-items-center gap-2 mx-auto" data-page="2"><span id="load-more-text"> VIEW MORE</span>
                <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"></path></svg></span></button>
            </div>
        </div> 
    </div>
</div>
</div>
</div>
</form>
</div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function () {

       if (window.innerWidth < 992) {
        $('.accordion .accordion-collapse').removeClass('show');  
        $('.accordion-button').addClass('collapsed');               
    }

    // Show View More initially if more pages
    @if($hasMore)
    $('#load-more').show();
    @endif

    function fetchOffers(page = 1, append = false) {
        let formData = $('#offers-search').serialize() + '&page=' + page;

        $.ajax({
            url: "{{ route('shop') }}",
            type: "POST",
            data: formData,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            beforeSend: function () {
                $('#load-more-text').text('Loading...');
            },
            success: function (response) {
                if (append) {
                    $('#offer-list').append(response.html);
                } else {
                    $('#offer-list').html(response.html);
                }

                if (response.hasMore) {
                    $('#load-more').show().data('page', page + 1);
                    $('#load-more-text').text('VIEW MORE');
                } else {
                    $('#load-more').hide();
                    $('#load-more-text').text('');
                }
            }
        });
    }

    $('.category-checkbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('.category-checkbox').not(this).prop('checked', false);
        }
    });

    // Submit form or filters
    $(document).on('submit change', '#offers-search input', function (e) {
        e.preventDefault();
        fetchOffers(1, false);
        $('html, body').animate({
            scrollTop: $('#offer-list').offset().top - 50
        }, 800);


    }); 

    // Load more button
    $(document).on('click', '#load-more', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        fetchOffers(page, true);
    });

    // Reset filters
    $(document).on('click', '.reset-btn', function (e) {
        e.preventDefault();
        $('#offers-search')[0].reset();
        fetchOffers(1, false);
    });
    $(document).on('click', '.submit-btn', function (e) {
        e.preventDefault();
        fetchOffers(1, false);
    });

});
</script>




@endsection
