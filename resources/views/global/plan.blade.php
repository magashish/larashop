<div class="home-plan-slider">
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