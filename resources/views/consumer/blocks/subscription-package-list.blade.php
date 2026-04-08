<div class="row align-items-end reward-mob-slider">
    @foreach($plans as $plan)
    @php
    $price = number_format($plan->price, 2, '.', '');
    [$whole, $decimal] = explode('.', $price);
    @endphp
    <div class="col-xl-3 col-md-6 col-12">
        <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }} w-100 py-0 mb-xl-0 mb-4">
            <div class="plan-name w-100">
                {{ $plan->name }}

                <div class="plan-price d-md-none d-flex">
                    <p class="price-text">${{ $whole }}.{{ $decimal }}</p>
                    <p>{{ $plan->price_description }}</p>
                </div>
            </div>
            <div class="plan-content-col">
                <div class="plan-price d-md-block d-none">
                    <sup>$</sup>{{ $whole }}<sup>.{{ $decimal }}</sup>
                    <p>{{ $plan->price_description }}</p>
                </div>
                <div class="plan-details">
                    {!! $plan->description !!}
                </div>

                @if(!empty($plan->read_more))
                <div class="plan-read-more smooth-toggle" id="plan-readmore-{{ $plan->id }}">
                    {!! $plan->read_more !!}
                    <a class="{{ $plan->type }} package-terms-conditions" href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">See T’s & C’s Here >>></a>
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
                    @endphp
                    @auth
                    <a href="{{ route('subscription.show', $plan->id) }}">
                        {{ $buttonText }}
                    </a>
                    @endauth
                    @guest
                    <a href="{{ route('register') }}">
                        {{ $buttonText }}
                    </a>
                    @endguest
                </div>
                <div class="plicing-info"> {!! $plan->sub_description !!}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>


<style>
    .smooth-toggle {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.6s ease-in-out, opacity 0.4s ease;
        opacity: 0;
    }
    .smooth-toggle.active {
        max-height: 1000px; 
        opacity: 1;
    }
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
    a.package-terms-conditions {
        color: #ffffff;
    }
    a.subscription.package-terms-conditions {
        color: #000000;
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


