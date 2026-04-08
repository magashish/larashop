@php
$useCustomRegister = $customRegister ?? false;
@endphp
<div class="package-plans">
    <div class="container">
        <div class="row g-sm-4 g-3">
            @foreach($plans as $plan)
            @php
            $price = number_format($plan->price, 2, '.', '');
            [$whole, $decimal] = explode('.', $price);

            $planName = strtolower($plan->name);
            switch ($planName) {
                case 'bronze':
                $buttonText = "Start with {$plan->name} - \${$whole}";
                break;
                case 'silver':
                $buttonText = "Choose {$plan->name} - \${$whole}";
                break;
                case 'gold':
                $buttonText = "Buy {$plan->name} - \${$whole}";
                break;
                case 'subscription':
                $buttonText = "Go {$plan->name} - \${$whole}";
                break;
                default:
                $buttonText = "Start with {$plan->name} - \${$whole}";
            }
            @endphp
            @if(!empty($plan->read_more))
            <div class="modal fade" id="planModal-{{ $plan->id }}" tabindex="-1" aria-labelledby="planModalLabel-{{ $plan->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content {{ strtolower($plan->name) }} position-relative">
                        <div class="access-box {{ strtolower($plan->name) }}">
                            <div class="inner-wrapper">
                                <button type="button" class="btn-close position-absolute" style="top: 15px; right: 15px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div>
                                    <div class="access-title">
                                        <p class="mb-0">{{ $plan->name }}</p>
                                    </div>
                                    <div class="package-desc mt-3">
                                        <div class="plan-details py-2 px-3">
                                            {!! $plan->read_more !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="access-btn">
                                    {{-- <a href="{{ auth()->check()
                                        ? route('subscription.show', $plan->id)
                                        : route('register.package', $plan->slug) }}">
                                        {!! $buttonText !!}
                                    </a> --}}


                    @if(auth()->check())
                    <a href="{{ route('subscription.show', $plan->id) }}">
                        {!! $buttonText !!}
                    </a>
                    @else
                    <a class="read-more-circle"
                    data-bs-toggle="modal"
                    data-bs-target="#subscriptionplanModal-{{ $plan->id }}">
                       {!! $buttonText !!}
                   </a>
                @endif

                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>






@php
$useCustomRegister = $customRegister ?? false;
@endphp
<div class="package-plans">
    <div class="container">
        <div class="row g-sm-4 g-3">
            @foreach($plans as $plan)
            @php
            $price = number_format($plan->price, 2, '.', '');
            [$whole, $decimal] = explode('.', $price);

            $planName = strtolower($plan->name);
            switch ($planName) {
                case 'bronze':
                $buttonText = "Start with {$plan->name} - \${$whole}";
                break;
                case 'silver':
                $buttonText = "Choose {$plan->name} - \${$whole}";
                break;
                case 'gold':
                $buttonText = "Buy {$plan->name} - \${$whole}";
                break;
                case 'subscription':
                $buttonText = "Go {$plan->name} - \${$whole}";
                break;
                default:
                $buttonText = "Start with {$plan->name} - \${$whole}";
            }
            @endphp
            @if(!empty($plan->read_more))
            <div class="modal fade" id="subscriptionplanModal-{{ $plan->id }}" tabindex="-1" aria-labelledby="subscriptionplanModalLabel-{{ $plan->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content {{ strtolower($plan->name) }} position-relative">
                        <div class="access-box {{ strtolower($plan->name) }}">
                            <div class="inner-wrapper">
                                <button type="button" class="btn-close position-absolute" style="top: 15px; right: 15px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div>
                                    <div class="access-title">
                                        <p class="mb-0">{{ $plan->name }}</p>
                                    </div>
                                    <div class="package-desc mt-3">
                                        <div class="plan-details py-2 px-3 payment-modal">
                                            {{-- {!! $plan->read_more !!} --}}
                                           @include('consumer.blocks.pop-up-subscription-register-form', ['formId' => $plan->id,'buttonText'=>$buttonText])
                                       </div>
                                   </div>
                               </div>
                              {{--  <div class="access-btn">
                                <a href="{{ auth()->check()
                                    ? route('subscription.show', $plan->id)
                                    : route('register.package', $plan->slug) }}">
                                    {!! $buttonText !!}
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
</div>