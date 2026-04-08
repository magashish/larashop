@foreach($subscription as $plan)
<section class="pricing-plan package-content-wrapper yellow-box mb-4 py-0">
    <div class="container-fluid px-0">
        <div class="row align-items-stretch mx-0">
            <div class=" col-lg-6 col-12">
                <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-content-col">
                        <div class="plan-price">
                           <sup>$</sup>{{ $plan->price }}<sup>.00</sup>
                           <p>ONE OFF PAYMENT</p>
                       </div>
                       <div class="plan-details">
                         {!! $plan->description !!}
                     </div>
                     <div class="pricing-button">
                           @php
                           $buttonText = 'READY TO WIN?';
                           $actionRoute = route('subscription.show', $plan->id);
                           @endphp
                           <a href="{{ $actionRoute }}">
                            {{ $buttonText }}
                        </a>
                    </div>
                    <div class="plicing-info"> {!! $plan->sub_description !!}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 px-0">
            <div class="image-sec">
               @if ($plan->image)
               <img src="{{ Storage::url($plan->image) }}" alt="{{ $plan->name }}">
               @endif
           </div>
       </div>
   </div>
</div>
</section>
@endforeach


@foreach($package as $plan)
<section class="pricing-plan package-content-wrapper grey-box mb-4 mt-0  py-0">
    <div class="container-fluid px-0">
        <div class="row align-items-stretch mx-0">
            <div class=" col-lg-6 col-12">
                <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-content-col">
                        <div class="plan-price">
                           <sup>$</sup>{{ $plan->price }}<sup>.00</sup>
                           <p>ONE OFF PAYMENT</p>
                       </div>
                       <div class="plan-details">
                           {!! $plan->description !!}
                       </div>
                       <div class="pricing-button">
                           @php
                           $buttonText = 'READY TO WIN?';
                           $actionRoute = route('subscription.show', $plan->id);
                           @endphp
                           <a href="{{ $actionRoute }}">
                            {{ $buttonText }}
                        </a>
                    </div>
                     <div class="plicing-info"> {!! $plan->sub_description !!}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 px-0">
            <div class="image-sec">
               @if ($plan->image)
               <img src="{{ Storage::url($plan->image) }}" alt="{{ $plan->name }}">
               @endif
           </div>
       </div>
   </div>
</div>
</section>
@endforeach