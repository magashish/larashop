@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="row g-md-5">
            <div class="col-lg-6">
                <div class="user-info-box info-view-box">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="user-image-wrapper">
                                @if(Auth::user()->profile_image)
                                <img id="previewImage" src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <img id="previewImage" src="{{ asset('images/hero-img.jpg') }}" alt="Preview" class="img-fluid w-100">
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-8">


                            @if(count($subscriptions) > 0)
                            <div class="profile-info ps-sm-0 ps-3 pe-xl-5 pe-3 pt-sm-5 pt-4 pb-4">
                                <h2 class="text-uppercase mb-4 ps-md-3">Hi, <span>{{ Auth::user()->name }}</span></h2>
                                @php
                                $firstSubscription = $subscriptions->first();
                                @endphp
                                @if($firstSubscription)
                                <p class="mb-3">You are currently on the <b>{{ $firstSubscription->plan->name ?? $firstSubscription->name }}</b>. For more chances to win in our cash or prize giveaways upgrade your package today!!!</p>
                                <a href="{{ route('subscription.packagesubscriptiondetail', $firstSubscription->id) }}" >View Subscription</a>
                                @endif
                            </div>

                            {{-- Check if there are more than one subscriptions --}}
                            @if(count($subscriptions) > 1)
                            <a href="{{ route('subscription.manage') }}" class="btn text-uppercase">View All Package & Subscriptions</a>
                            @endif

                            @else
                            {{-- Optional: Show a message if there are no subscriptions --}}
                            <div class="profile-info ps-sm-0 ps-3 pe-xl-5 pe-3 pt-sm-5 pt-4 pb-4">
                                <h2 class="text-uppercase mb-4 ps-md-3">Hi, <span>{{ Auth::user()->name }}</span></h2>
                                <p class="mb-3">You do not have any active subscriptions. Start today to get a chance to win in our cash or prize giveaways!</p>
                            </div>
                            @endif



                           {{--  @foreach ($subscriptions as $subscription)
                            @php
                            $plan = \App\Models\Plan::where('id', $subscription->plan_id)->first();
                            @endphp
                            <div class="profile-info ps-sm-0 ps-3 pe-xl-5 pe-3 pt-sm-5 pt-4 pb-4">
                                <h2 class="text-uppercase mb-4 ps-md-3">Hi, <span>{{ Auth::user()->name }}</span></h2>
                                <p class="mb-3">You are currently on the <b>{{ $plan->name ?? $subscription->name }}</b>. For more chances to win in our cash or prize giveaways upgrade your package today!!!</p>
                                <a href="{{ route('subscription.packagesubscriptiondetail', $subscription->id) }}" >View Subscription</a>
                            </div>
                            @endforeach --}}
                        </div>
                    </div>
                </div>                                
            </div>
            <div class="col-lg-6">
                <div class="entries-box h-100 mt-md-0 mt-5">
                    <div class="box-inner h-100">
                        <div class="d-flex flex-column h-100">
                            <div class="box-title text-center mb-3">
                                <h6 class="mb-0 text-uppercase">TOTAL ENTRIES</h6>
                            </div>
                            {!! generate_user_progress_circle() !!}
                            {{-- @php
                            $entriesData = current_user_entries_percentage();
                            $percentage = $entriesData['percentage'];
                            $dashoffset = 377 * (1 - min($percentage, 100) / 100);
                            @endphp
                            <div class="box-content d-flex align-items-center justify-content-center h-100">
                                <div class="progress-circle">
                                    <svg width="150" height="150">
                                        <circle class="progress-bg" cx="75" cy="75" r="60" />
                                        <circle class="progress-value" 
                                        cx="75" 
                                        cy="75" 
                                        r="60"
                                        stroke-dasharray="377"
                                        stroke-dashoffset="{{ $dashoffset }}" />
                                    </svg>
                                    <div class="progress-text">
                                        {{ round($percentage) }}%<br>
                                        <small>{{ $entriesData['current_entries'] }}/{{ $entriesData['top_entries'] }}</small>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="package-plan-wrapper mt-5">
            <div class="row g-xxl-5">
                @foreach($allplans as $plan)
                @php
                $price = number_format($plan->price, 2, '.', '');
                [$whole, $decimal] = explode('.', $price);
                @endphp
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-content-col">
                            <div class="plan-price">
                                <sup>$</sup>{{ $whole }}<sup>.{{ $decimal }}</sup>
                                <p>{{ $plan->price_description }}</p>
                            </div>
                            <div class="plan-details">
                               {!! $plan->description !!}
                           </div>
                           <div class="pricing-button">
                            @php
                            $buttonText = 'READY TO WIN?';
                            $buttonClass = '';
                            $isDisabled = false;
                            $actionRoute = route('subscription.show', $plan->id);
                            @endphp
                            @if ($plan->type == 'subscription')
                            @php
                            $buttonText = 'REAL PRIZES, REAL WINNERS';
                            @endphp
                            @endif
                            <a href="{{ $actionRoute }}" class="btn {{ $buttonClass }} pull-right {{ $isDisabled ? 'disabled' : '' }}"
                            {{ $isDisabled ? 'aria-disabled="true"' : '' }}>
                            {{ $buttonText }}
                        </a>
                        
                        </div>
                        <div class="plicing-info"> {!! $plan->sub_description !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>

{{-- This section is for the Upsell Modal, assuming you want to keep it --}}
{{-- @if($package_plan = upsell_modal_function())
    @include('subscriber.block.upsellModal')
@endif
 --}}

@php
    $upsell = upsell_modal_function();
    $package_plan  = $upsell['package_plan'] ?? null;
    $package_count = $upsell['package_count'] ?? null;
@endphp

@if($package_plan && $package_count === 0)
    {{-- First Time User --}}
    @include('subscriber.block.upsellModal')

@elseif($package_plan && $package_count > 0)
    {{-- Returning User --}}
    @include('subscriber.block.upsellModalSecond')
@endif




@endsection