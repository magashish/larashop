@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="account-wrapper subscription-detail">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-8">
                    <div class="auth-box p-3 p-sm-4">
                        <div class="sec-head mb-4 text-center">
                            <h2 class="text-uppercase text-white">Package & Subscription Detail! 🎉</h2>
                        </div>
                        <div class="account-info-wrapper text-white">

                            {{-- Order Details Section --}}
                            <div class="info-item d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4 py-3">
                                <div class="item-label text-center text-sm-start mb-2 mb-sm-0">
                                    <h4 class="text-uppercase mb-0">Order Details</h4>
                                </div>
                                <div class="item-data text-center">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Plan:</strong>
                                            {{ !empty($plan->name) ? $plan->name : '&nbsp;' }}
                                        </li>
                                        <li><strong>Type:</strong>
                                            {{ !empty($plan->type) ? $plan->type : '&nbsp;' }}
                                        </li>
                                        <li><strong>Purchase Date:</strong>
                                            {{ !empty($userPackageSubscription->purchase_date)
                                                ? $userPackageSubscription->purchase_date->format('F d, Y')
                                                : '&nbsp;' }}
                                        </li>

                                        {{-- Gateway Badge --}}
                                        <li><strong>Payment Via:</strong>
                                            @if($isPayPal)
                                                <span class="badge bg-warning text-dark">PayPal</span>
                                            @else
                                                <span class="badge bg-primary">Stripe</span>
                                            @endif
                                        </li>

                                        {{-- ID — Stripe or PayPal --}}
                                        @if($isStripe)
                                            <li><strong>Stripe ID:</strong>
                                                {{ !empty($userPackageSubscription->stripe_id) ? $userPackageSubscription->stripe_id : '&nbsp;' }}
                                            </li>
                                        @endif
                                        @if($isPayPal)
                                            <li><strong>PayPal ID:</strong>
                                                {{ !empty($userPackageSubscription->paypal_id) ? $userPackageSubscription->paypal_id : '&nbsp;' }}
                                            </li>
                                        @endif

                                        <li><strong>Status:</strong>
                                            {{ !empty($userPackageSubscription->status) ? $userPackageSubscription->status : '&nbsp;' }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="item-action text-end d-none d-sm-block"></div>
                            </div>

                            <hr class="my-4">

                            {{-- Payment Details Section --}}
                            <div class="info-item d-flex flex-column flex-sm-row justify-content-between align-items-center py-3">
                                <div class="item-label text-center text-sm-start mb-2 mb-sm-0">
                                    <h4 class="text-uppercase mb-0">Payment Details</h4>
                                </div>

                                <div class="item-data text-center">

                                    {{-- ============================================
                                         STRIPE DETAILS
                                    ============================================= --}}
                                    @if($isStripe)

                                        @if($userPackageSubscription->type === 'package')
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Currency:</strong>
                                                    {{ !empty($purchaseData->currency) ? strtoupper($purchaseData->currency) : '&nbsp;' }}
                                                </li>
                                                <li><strong>Stripe Payment Intent ID:</strong>
                                                    {{ $purchaseData->id ?? '&nbsp;' }}
                                                </li>
                                                <li><strong>Stripe Charge ID:</strong>
                                                    {{ $purchaseData->latest_charge ?? '&nbsp;' }}
                                                </li>
                                                <li><strong>Description:</strong>
                                                    {{ $purchaseData->description ?? '&nbsp;' }}
                                                </li>
                                                <li><strong>Receipt:</strong>
                                                    @if(!empty($receiptUrl))
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="text-warning">View Receipt</a>
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </li>
                                            </ul>

                                        @elseif($userPackageSubscription->type === 'subscription')
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Stripe Subscription ID:</strong> {{ $purchaseData->id ?? '&nbsp;' }}</li>
                                                <li><strong>Status:</strong> {{ $purchaseData->status ?? '&nbsp;' }}</li>
                                                <li><strong>Current Period Start:</strong>
                                                    {{ !empty($purchaseData->current_period_start)
                                                        ? \Carbon\Carbon::createFromTimestamp($purchaseData->current_period_start)->format('F d, Y')
                                                        : '&nbsp;' }}
                                                </li>
                                                <li><strong>Current Period End:</strong>
                                                    {{ !empty($purchaseData->current_period_end)
                                                        ? \Carbon\Carbon::createFromTimestamp($purchaseData->current_period_end)->format('F d, Y')
                                                        : '&nbsp;' }}
                                                </li>
                                                <li><strong>Amount Paid:</strong>
                                                    {{ !empty($latestInvoice->amount_paid)
                                                        ? '$' . number_format($latestInvoice->amount_paid / 100, 2)
                                                        : '&nbsp;' }}
                                                </li>
                                                <li><strong>Stripe Charge ID:</strong> {{ $latestInvoice->charge ?? '&nbsp;' }}</li>
                                                <li><strong>Receipt:</strong>
                                                    @if(!empty($receiptUrl))
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="text-warning">View Receipt</a>
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </li>
                                            </ul>
                                        @endif

                                    {{-- ============================================
                                         PAYPAL DETAILS
                                    ============================================= --}}
                                    @elseif($isPayPal)

                                        @if($userPackageSubscription->type === 'package')
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>PayPal Capture ID:</strong>
                                                    {{ $paypalDetails['id'] ?? $userPackageSubscription->paypal_id }}
                                                </li>
                                                <li><strong>Amount Paid:</strong>
                                                    ${{ $paypalDetails['amount']['value'] ?? number_format($plan->price, 2) }}
                                                    {{ strtoupper($paypalDetails['amount']['currency_code'] ?? env('DEFAULT_CURRENCY', 'AUD')) }}
                                                </li>
                                                <li><strong>Status:</strong>
                                                    {{ $paypalDetails['status'] ?? 'COMPLETED' }}
                                                </li>
                                                @if(!empty($paypalDetails['create_time']))
                                                    <li><strong>Payment Date:</strong>
                                                        {{ \Carbon\Carbon::parse($paypalDetails['create_time'])->format('F d, Y h:i A') }}
                                                    </li>
                                                @endif
                                                <li><strong>Receipt:</strong>
                                                    @if(!empty($receiptUrl))
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="text-warning">View on PayPal</a>
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </li>
                                            </ul>

                                        @elseif($userPackageSubscription->type === 'subscription')
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>PayPal Subscription ID:</strong>
                                                    {{ $paypalDetails['id'] ?? $userPackageSubscription->paypal_id }}
                                                </li>
                                                <li><strong>Status:</strong>
                                                    {{ $paypalDetails['status'] ?? 'ACTIVE' }}
                                                </li>
                                                @if(!empty($paypalDetails['start_time']))
                                                    <li><strong>Started:</strong>
                                                        {{ \Carbon\Carbon::parse($paypalDetails['start_time'])->format('F d, Y') }}
                                                    </li>
                                                @endif
                                                @if(!empty($paypalDetails['billing_info']['next_billing_time']))
                                                    <li><strong>Next Billing:</strong>
                                                        {{ \Carbon\Carbon::parse($paypalDetails['billing_info']['next_billing_time'])->format('F d, Y') }}
                                                    </li>
                                                @endif
                                                @if(!empty($paypalDetails['billing_info']['last_payment']))
                                                    <li><strong>Last Payment:</strong>
                                                        ${{ $paypalDetails['billing_info']['last_payment']['amount']['value'] }}
                                                        {{ strtoupper($paypalDetails['billing_info']['last_payment']['amount']['currency_code']) }}
                                                        on {{ \Carbon\Carbon::parse($paypalDetails['billing_info']['last_payment']['time'])->format('F d, Y') }}
                                                    </li>
                                                @endif

                                                {{-- Latest Transaction --}}
                                                @if(!empty($latestInvoice))
                                                    <li><strong>Last Transaction ID:</strong>
                                                        {{ $latestInvoice['id'] ?? 'N/A' }}
                                                    </li>
                                                    @if(!empty($latestInvoice['amount_with_breakdown']['gross_amount']['value']))
                                                        <li><strong>Transaction Amount:</strong>
                                                            ${{ $latestInvoice['amount_with_breakdown']['gross_amount']['value'] }}
                                                        </li>
                                                    @endif
                                                    @if(!empty($latestInvoice['time']))
                                                        <li><strong>Transaction Date:</strong>
                                                            {{ \Carbon\Carbon::parse($latestInvoice['time'])->format('F d, Y') }}
                                                        </li>
                                                    @endif
                                                @endif

                                                <li><strong>Manage:</strong>
                                                    @if(!empty($receiptUrl))
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="text-warning">View on PayPal</a>
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </li>
                                            </ul>
                                        @endif

                                    @else
                                        <p>Unable to retrieve full payment details for this transaction.</p>
                                    @endif

                                </div>{{-- item-data --}}

                                <div class="item-action text-end d-none d-sm-block"></div>
                            </div>{{-- info-item --}}

                        </div>{{-- account-info-wrapper --}}

                        <div class="sec-btn text-center mt-4">
                            <a href="{{ route('subscription.manage') }}" class="btn btn-outline-warning text-uppercase">
                                <i class="bi bi-box-seam"></i> Manage My Subscriptions
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection