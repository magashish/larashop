<section class="pricing-plan package-page-plan mt-0 mb-4 py-0">
    <div class="container-fluid px-0">
        <div class="row align-items-stretch mx-0">
            <div class="col-md-6 px-0">
                @foreach($subscription as $plan)
                @php
                $price = number_format($plan->price, 2, '.', '');
                [$whole, $decimal] = explode('.', $price);
                @endphp

                <div class="package-content-wrapper yellow-box">
                    <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                        <div class="plan-name">
                            {{ $plan->name }}
                        </div>
                        <div class="plan-content-col">
                            <div class="plan-price">
                                <sup>$</sup>{{ $whole }}<sup>.{{ $decimal }}</sup>
                                <p>{{ $plan->price_description }}</p>
                            </div>
                            <div class="plan-details">
                               {!! $plan->description !!}
                           </div>

                           @if(!empty($plan->read_more))
                           <div class="plan-read-more smooth-toggle" id="plan-readmore-{{ $plan->id }}">
                            {!! $plan->read_more !!}
                            <a class="package-terms-conditions" href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">See T’s & C’s Here >>></a>
                        </div>

                        <button class="read-more-circle" data-target="plan-readmore-{{ $plan->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-icon" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        @endif

                        <div class="pricing-button">
                           @php
                           $buttonText = 'READY TO WIN?';
                           $actionRoute = route('subscription.show', $plan->id);
                           @endphp
                           <a href="{{ $actionRoute }}">
                             {{ $buttonText }}
                         </a>
                     </div>
                 </div>
             </div>
         </div>
         @endforeach
         @foreach($package as $plan)
         @php
         $price = number_format($plan->price, 2, '.', '');
         [$whole, $decimal] = explode('.', $price);
         @endphp
         <div class="package-content-wrapper grey-box">
            <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                <div class="plan-name">
                    {{ $plan->name }}
                </div>
                <div class="plan-content-col">
                    <div class="plan-price">
                        <sup>$</sup>{{ $whole }}<sup>.{{ $decimal }}</sup>
                        <p>{{ $plan->price_description }}</p>
                    </div>
                    <div class="plan-details">
                       {!! $plan->description !!}
                   </div>

                   @if(!empty($plan->read_more))
                   <div class="plan-read-more smooth-toggle" id="plan-readmore-{{ $plan->id }}">
                    {!! $plan->read_more !!}
                    <a class="package-terms-conditions" style="color: white;" href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">See T’s & C’s Here >>></a>

                </div>

                <button class="read-more-circle" data-target="plan-readmore-{{ $plan->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="arrow-icon" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                @endif


                <div class="pricing-button">
                   @php
                   $buttonText = 'READY TO WIN?';
                   $actionRoute = route('subscription.show', $plan->id);
                   @endphp
                   <a href="{{ $actionRoute }}">
                     {{ $buttonText }}
                 </a>
             </div>
         </div>
     </div>
 </div>
 @endforeach

</div>
<div class="col-md-6 px-0">
    @php
    $randomPlan = $plans->whereNotNull('image')->random();
    @endphp
    @if ($randomPlan && $randomPlan->image)
    <div class="image-sec sticky-md-top">
        <img src="{{ Storage::url($randomPlan->image) }}" alt="{{ $randomPlan->name }}">
    </div>
    @endif
</div>
</div>
</div>
</section>

<style>
    .smooth-toggle {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.6s ease-in-out, opacity 0.4s ease;
        opacity: 0;
    }

/* When active (revealed) */
.smooth-toggle.active {
    max-height: 1000px; /* large enough to fit content */
    opacity: 1;
}

/* Arrow button styling */
.read-more-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: transparent;
    border: 2px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 15px auto;
    cursor: pointer;
    transition: all 0.3s ease;
}

.read-more-circle:hover {
    background-color: white;
}

.read-more-circle:hover .arrow-icon {
    stroke: #000;
}

.arrow-icon {
    width: 24px;
    height: 24px;
    transition: transform 0.4s ease;
}

.read-more-circle.active .arrow-icon {
    transform: rotate(180deg);
}

.pricing-table.package {
    width: 45%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    margin: 0 auto;
    padding: 80px 0;
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.read-more-circle').forEach(button => {
            button.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                const isActive = target.classList.toggle('active');
                this.classList.toggle('active', isActive);
            });
        });
    });
</script>


