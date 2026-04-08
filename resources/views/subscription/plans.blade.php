@extends('layouts.businessapp')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">


            <div class="card">
                <div class="card-header">Select Subscriptions and Packages Plan:</div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                       <a href="{{ route('subscription.manage') }}" class="btn btn-outline-secondary">Manage My Subscriptions</a>
                   </div>
                   @if (session('success'))
                   <div class="alert alert-success">{{ session('success') }}</div>
                   @endif
                   @if (session('error'))
                   <div class="alert alert-danger">{{ session('error') }}</div>
                   @endif
                   @if (session('info'))
                   <div class="alert alert-info">{{ session('info') }}</div>
                   @endif

                   <div class="row">
                   
                @foreach($plans as $plan)
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            ${{ $plan->price }}/Mo
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $plan->name }}</h5>
                            <p class="card-text">{!! $plan->description !!}</p>
                            @php
                            $buttonText = 'Choose';
                            $buttonClass = 'btn-primary';
                            $isDisabled = false;
                            $actionRoute = route('subscription.show', $plan->id);
                            @endphp

                            @if ($currentSubscription)
                            @if ($currentSubscription->type === $plan->slug)
                            @php
                            $buttonText = 'Current Plan';
                            $buttonClass = 'btn-success';
                            $isDisabled = true; 
                            $actionRoute = '#'; 
                            @endphp
                            @else
                            @php
                            $buttonText = 'Swap to this Plan';
                            $buttonClass = 'btn-warning';
                            $actionRoute = route('subscription.swap', ['current' => $currentSubscription->type, 'new' => $plan->slug]); 
                            @endphp
                            @endif
                            @endif
                            <a href="{{ $actionRoute }}" class="btn {{ $buttonClass }} pull-right {{ $isDisabled ? 'disabled' : '' }}"
                            {{ $isDisabled ? 'aria-disabled="true"' : '' }}>
                            {{ $buttonText }}
                        </a>

                    </div>
                </div>
            </div>
            @endforeach
        </div> 
    </div> 
</div> 


</div> 
</div> 
</div> 

@endsection
