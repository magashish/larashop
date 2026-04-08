@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="legal-doc-wrapper accordion-sec">
            <div class="container">
                <div class="sec-head">
                    <h2 class="mb-5">Legal <span>Documentation</span></h2>
                </div>
                <div class="inner-wrapper p-sm-4 p-3">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <!-- First item open by default -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed d-flex justify-content-between gap-3"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">                                                    
                                Privacy Policy
                                <img src="images/chevron-down-solid.svg" alt="arrow" class="arrow-icon img-fluid">
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse show"
                        aria-labelledby="flush-headingOne"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body px-sm-4 px-3 pb-sm-4 pb-3">
                            @include('global.privacy-policy')
                        </div>
                    </div>
                </div>

                <!-- Other Question Items -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingTwo">
                        <button class="accordion-button collapsed d-flex justify-content-between gap-3"
                        type="button" data-bs-toggle="collapse"
                        data-bs-target="#flush-collapseTwo" aria-expanded="false"
                        aria-controls="flush-collapseTwo">
                        Membership - Terms & Conditions
                        <img src="images/chevron-down-solid.svg" alt="arrow" class="arrow-icon img-fluid">
                    </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse"
                aria-labelledby="flush-headingTwo"
                data-bs-parent="#accordionFlushExample">
                <div class="accordion-body px-sm-4 px-3 pb-sm-4 pb-3">
                     @include('global.membership-terms-and-conditions')
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed d-flex justify-content-between gap-3"
                type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseThree" aria-expanded="false"
                aria-controls="flush-collapseThree">
                Website - Terms & Conditions
                <img src="images/chevron-down-solid.svg" alt="arrow" class="arrow-icon img-fluid">
            </button>
        </h2>
        <div id="flush-collapseThree" class="accordion-collapse collapse"
        aria-labelledby="flush-headingThree"
        data-bs-parent="#accordionFlushExample">
        <div class="accordion-body px-sm-4 px-3 pb-sm-4 pb-3">
            @include('global.website-terms-and-conditions')
        </div>
    </div>
</div>

{{-- <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingFour">
        <button class="accordion-button collapsed d-flex justify-content-between gap-3"
        type="button" data-bs-toggle="collapse"
        data-bs-target="#flush-collapseFour" aria-expanded="false"
        aria-controls="flush-collapseFour">
        Packages Terms and Conditions
        <img src="images/chevron-down-solid.svg" alt="arrow" class="arrow-icon img-fluid">
    </button>
</h2>
<div id="flush-collapseFour" class="accordion-collapse collapse"
aria-labelledby="flush-headingFour"
data-bs-parent="#accordionFlushExample">
<div class="accordion-body px-sm-4 px-3 pb-sm-4 pb-3">
    Lorem, ipsum dolor sit, amet consectetur adipisicing elit. Ea asperiores perferendis, quaerat sapiente numquam ut distinctio recusandae aut rerum officia incidunt quibusdam corporis debitis blanditiis quidem harum reiciendis, natus. Dolore.
</div>
</div>
</div> --}}

<div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingFive">
        <button class="accordion-button collapsed d-flex justify-content-between gap-3"
        type="button" data-bs-toggle="collapse"
        data-bs-target="#flush-collapseFive" aria-expanded="false"
        aria-controls="flush-collapseFive">
        FAQS
        <img src="images/chevron-down-solid.svg" alt="arrow" class="arrow-icon img-fluid">
    </button>
</h2>
<div id="flush-collapseFive" class="accordion-collapse collapse"
aria-labelledby="flush-headingFive"
data-bs-parent="#accordionFlushExample">
<div class="accordion-body px-sm-4 px-3 pb-sm-4 pb-3">




   
<div class="faq-list">
    @foreach ($faqs as $faq)
        <div class="faq-item mb-4">
            <h3 class="faq-question">{{ $faq->question }}</h3>
            <div class="faq-answer">
                {!! $faq->answer !!}
            </div>
        </div>
    @endforeach
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

@endsection