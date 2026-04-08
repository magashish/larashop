@extends('layouts.businessapp')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Plan Change') }}</div>

                <div class="card-body">
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <p>You are about to change your subscription plan:</p>

                    <ul class="list-group mb-4">
                        <li class="list-group-item">
                            <strong>Current Plan:</strong> {{ $currentSubscription->name }}
                            ({{ $currentSubscription->stripe_price }})
                        </li>
                        <li class="list-group-item">
                            <strong>New Plan:</strong> {{ $newPlan->name }}
                            ({{ $newPlan->stripe_plan }}) - ${{ number_format($newPlan->price, 2) }}/Mo
                        </li>
                        <li class="list-group-item text-muted">
                            <small>
                                Your subscription will be prorated. This means any unused time on your current plan will be credited towards the new plan, or you may be charged immediately for the difference.
                            </small>
                        </li>
                        @if ($prorationExplanation)
                        <li class="list-group-item">
                            <strong>Proration Estimate:</strong>
                            <span class="text-info">{{ $prorationExplanation }}</span>
                        </li>
                        @endif
                    </ul>

                    <form action="{{ route('subscription.swap.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="current_subscription_name" value="{{ $currentSubscription->type }}">
                        <input type="hidden" name="new_plan_slug" value="{{ $newPlan->slug }}">

                        <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Please confirm you want to change your plan to {{ $newPlan->name }}. This action will take effect immediately.');">
                        Confirm Plan Change
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
