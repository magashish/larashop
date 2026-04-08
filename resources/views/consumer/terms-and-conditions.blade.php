@extends('layouts.consumer')
@section('content')

<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="images/support-banner.png" class="d-lg-block d-none" alt="Banner Image">
            <img src="images/support-mob-banner.jpg" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title">
            <div class="yellow-bg text-uppercase">NEED HELP?</div>
            <div class="black-bg text-uppercase">START HERE</div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->

<!-- Text Banner Sec Start -->
<section class="packages-quote-sec pt-0 d-lg-block d-none">
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
</section>

<!-- Text Banner Sec End -->

<section class="support-form pt-lg-0 pt-2">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="work-tabs-inner">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="work-tab" data-bs-toggle="tab" data-bs-target="#work-tab-pane" type="button" role="tab"
                            aria-controls="work-tab-pane" aria-selected="false"> CONTACT US </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq-tab-pane" type="button" role="tab" aria-controls="faq-tab-pane" aria-selected="false">FAQ </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="legal-doc" data-bs-toggle="tab" data-bs-target="#legal-doc-pane" type="button" role="tab" aria-controls="legal-doc-pane" aria-selected="true">LEGAL DOCUMENTATION</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="work-tab-pane" role="tabpanel" aria-labelledby="work-tab"
                        tabindex="0">
                        <div class="work-tab-content-col">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <!-- Header Text -->
                                    <p class="header-text text-center mb-sm-5 mb-3">
                                        HAVE A <span class="highlight"> QUESTION?</span>
                                    </p>

                                    <p class="terms-text">Check out our <a class="blue-text"
                                        href="#">FAQ</a> page for answers to common
                                        queries. If you can’t find what you need, simply fill out the form
                                    below — an Xhale team member will be in touch shortly!</p>



                                    <form id="multi-step-form"  class="mt-5" method="POST" action="{{ route('contactus') }}">
                                        @csrf
                                        <div class="step-inner-content mb-3">
                                            <div class="row">
                                                <div class="col-12 input-group-icon position-relative">
                                                    <i class="bi bi-person input-icon" aria-hidden="true"></i>
                                                    <input type="text" class="form-control" placeholder="Name*" name="name" required>
                                                    <div class="invalid-feedback"></div> </div>

                                                    <div class="col-sm-6 input-group-icon position-relative">
                                                        <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                                                        <input type="email" class="form-control" placeholder="Email Address*" name="email" required>
                                                        <div class="invalid-feedback"></div> </div>

                                                        <div class="col-sm-6 input-group-icon position-relative">
                                                            <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                                                            <input type="tel" class="form-control" placeholder="Phone Number*" name="phonenumber" required>
                                                            <div class="invalid-feedback"></div> </div>

                                                            <div class="col-12 input-group-icon position-relative">
                                                                <i class="bi bi-chat input-icon chatty" aria-hidden="true"></i>
                                                                <textarea class="form-control" rows="4" placeholder="How can we help?" id="" name="msg" required></textarea>
                                                                <div class="invalid-feedback"></div> </div>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-submit text-uppercase" aria-label="Submit enquiry form"> Submit Enquiry
                                                        </button>

                                                        <div id="contact-us-result" class="mt-3" style="display: none;">
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="tab-pane fade" id="faq-tab-pane" role="tabpanel"
                                    aria-labelledby="faq-tab" tabindex="0">
                                    <div class="work-tab-content-col">
                                        <div class="container py-4">
                                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                                <div class="accordion accordion-flush" id="accordionFlushExample"> 
                                                    @foreach ($faqs as $index => $faq)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="flush-heading{{ $index }}">
                                                            <button class="accordion-button @if($index !== 0) collapsed @endif d-flex gap-3"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#flush-collapse{{ $index }}"
                                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                            aria-controls="flush-collapse{{ $index }}">
                                                            <span class="icon-question" aria-hidden="true"><i class="bi bi-question-circle"></i></span>
                                                            {{-- Display the FAQ Question --}}
                                                            {{ $faq->question }}
                                                        </button>
                                                    </h2>
                                                    <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse @if($index === 0) show @endif"
                                                    aria-labelledby="flush-heading{{ $index }}"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body px-5 pb-4">
                                                        {!! $faq->answer !!}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="legal-doc-pane" role="tabpanel"
                        aria-labelledby="legal-doc" tabindex="0">

                        <div class="work-tab-content-col">
                            <div class="container py-4">
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    <!-- First item open by default -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button collapsed d-flex gap-3"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseOne" aria-expanded="false"
                                            aria-controls="flush-collapseOne">
                                            <span class="icon-question" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
                                            Privacy Policies
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="flush-headingOne"
                                    data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body px-5 pb-4">
                                        Lorem, ipsum dolor sit, amet consectetur adipisicing elit. Ea asperiores perferendis, quaerat sapiente numquam ut distinctio recusandae aut rerum officia incidunt quibusdam corporis debitis blanditiis quidem harum reiciendis, natus. Dolore.
                                    </div>
                                </div>
                            </div>

                            <!-- Other Question Items -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingTwo">
                                    <button class="accordion-button collapsed d-flex gap-3"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                    aria-controls="flush-collapseTwo">
                                    <span class="icon-question" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span> Membership - Terms & Conditions
                                </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingTwo"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body px-5 pb-4">
                                Lorem, ipsum dolor sit, amet consectetur adipisicing elit. Ea asperiores perferendis, quaerat sapiente numquam ut distinctio recusandae aut rerum officia incidunt quibusdam corporis debitis blanditiis quidem harum reiciendis, natus. Dolore.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed d-flex gap-3"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseThree" aria-expanded="false"
                            aria-controls="flush-collapseThree">
                            <span class="icon-question" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span> Website - Terms & Conditions
                        </button>
                    </h2>
                    <div id="flush-collapseThree" class="accordion-collapse collapse"
                    aria-labelledby="flush-headingThree"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body px-5 pb-4">
                        Lorem, ipsum dolor sit, amet consectetur adipisicing elit. Ea asperiores perferendis, quaerat sapiente numquam ut distinctio recusandae aut rerum officia incidunt quibusdam corporis debitis blanditiis quidem harum reiciendis, natus. Dolore.
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

@include('consumer.blocks.advertisement')
@include('consumer.blocks.social-feeds')



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script>
    $(document).ready(function() {
    // Get the message display area by its new ID
        const $contactUsResult = $('#contact-us-result'); 

        $('#multi-step-form').on('submit', function(event) {
        event.preventDefault(); // Stop the default form submission

        const $form = $(this);
        const url = $form.attr('action');
        const method = $form.attr('method');
        const formData = $form.serialize();
        const $submitButton = $form.find('button[type="submit"]');

        // --- Clear any existing error messages and styling BEFORE submission ---
        $form.find('.form-control').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('').removeClass('d-block');

        // Clear any previous general form messages, hide the div, and remove Bootstrap alert classes
        $contactUsResult.empty().hide().removeClass('alert alert-success alert-danger');

        // Disable button and change text while submitting
        $submitButton.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $contactUsResult.text(response.message).addClass('alert alert-success').show(); 
                $form[0].reset(); 
                $submitButton.prop('disabled', false).text('Submit Enquiry'); 

            },
            error: function(xhr, status, error) {
                console.error('Error submitting form:', error, xhr.responseText);
                $submitButton.prop('disabled', false).text('Submit Enquiry'); 
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function(fieldName, messages) {
                        const $inputField = $form.find(`[name="${fieldName}"]`);

                        if ($inputField.length > 0) {
                            $inputField.addClass('is-invalid'); 
                            const $errorDiv = $inputField.next('.invalid-feedback');
                            if ($errorDiv.length > 0) {
                                $errorDiv.text(messages.join(' ')); 
                                $errorDiv.addClass('d-block');     
                            }
                        }
                    });

                } else {
                    $contactUsResult.text(xhr.responseJSON.message).addClass('alert alert-danger') .show();
                }
            }
        });
    });
    });
</script>
@endsection
