@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="legal-doc-wrapper accordion-sec">
            <div class="container">
                <div class="auth-box p-3 p-sm-4">
                    <div class="sec-head mb-4 text-center">
                        <h2 class="text-uppercase text-white">Your Packages & Subscriptions</h2>
                    </div>

                    {{-- Alert Messages --}}
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    @if ($subscriptions->isEmpty())
                    <div class="text-center my-5 text-white">
                        <p class="mb-3">You do not have any active subscriptions.</p>
                        <a href="{{ route('membership.dashboard') }}" class="btn btn-warning text-uppercase">Browse Subscriptions Plan</a>
                    </div>
                    @else
                    <div class="account-info-wrapper text-white">
                        @foreach ($subscriptions as $subscription)
                            @php
                            $plan = \App\Models\Plan::where('id', $subscription->plan_id)->first();
                            @endphp

                            <div class="info-item mb-3 d-flex flex-column flex-sm-row justify-content-between align-items-center">
                                <div class="item-label text-center text-sm-start mb-2 mb-sm-0">
                                    <p class="text-uppercase mb-0">Plan: {{ $plan->name ?? 'N/A' }}</p>
                                </div>
                                <div class="item-data text-center mb-2 mb-sm-0">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Status:</strong> <span class="badge {{ $subscription->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ Str::title($subscription->status) }}</span></li>
                                        @if($subscription->starts_at)
                                        <li><strong>Started:</strong> {{ $subscription->starts_at->format('M d, Y') }}</li>
                                        @endif

                                        @if($subscription->ends_at)
                                        <li><strong>Ends:</strong> {{ $subscription->ends_at->format('M d, Y') }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="item-action text-center text-sm-end">
                                    <div class="d-flex flex-column flex-sm-row justify-content-end">
                                        {{-- The new "View Details" button --}}
                                        <a href="{{ route('subscription.packagesubscriptiondetail', $subscription->id) }}" class="btn btn-sm btn-outline-info me-sm-2 mb-2 mb-sm-0 text-uppercase">View Details</a>


                                        <a href="{{ route('packagesubscriptionentries', $subscription->id) }}" class="btn btn-sm btn-outline-info me-sm-2 mb-2 mb-sm-0 text-uppercase"> View Entries</a>


                                    <form action="{{ route('subscription.destroy', $subscription->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE') 
                                            <button type="submit" class="btn btn-danger mb-2" onclick="return confirm('WARNING: Are you sure you want to revoke access to your {{ $plan->name ?? 'package' }}? This action cannot be undone and you will lose immediate access and entries.');">Delete</button>
                                            </form> 



                                            @if ($subscription->type == 'subscription' && $subscription->status == 'active')

                                            <form action="{{ route('subscription.cancel') }}" method="POST" class="d-inline">
                                                @csrf
                                                @if(!empty($subscription->paypal_id))
                                                <input type="hidden" name="subscription_name" value="{{ $subscription->paypal_id }}">
                                                @else
                                                <input type="hidden" name="subscription_name" value="{{ $subscription->stripe_id }}">
                                                @endif

                                                <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to cancel your {{ $plan->name ?? $subscription->name }} subscription?');">
                                                Cancel Subscription
                                            </button>
                                        </form>

                                        @endif


                                    </div>
                                </div>
                            </div>
                            @if (!$loop->last)
                            <hr class="my-3">
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- This section is for the Upsell Modal, assuming you want to keep it --}}
{{-- @if($package_plan = upsell_modal_function())
    @include('subscriber.block.upsellModal')
@endif --}}

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