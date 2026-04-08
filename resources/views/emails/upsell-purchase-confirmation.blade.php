@extends('layouts.xhale_mail')

@section('title', 'Xhale Upsell Purchase Confirmation')
@section('header_title', 'Upsell Purchase Confirmation')

@section('content')
<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">Hello, {{ $user->name }}!</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Thank you for your recent purchase. Your {{ $subscription->plan->name }} {{ $subscription->type }} is now active!
</p>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Subscription Details
    </h2>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Plan:</strong> <span style="color: #10B981;">{{ $subscription->plan->name }}</span>
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Status:</strong> <span style="color: #10B981;">{{ $subscription->status }}</span>
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Start Date:</strong> {{ $subscription->starts_at->toFormattedDateString() }}
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>End Date:</strong> {{ $subscription->ends_at->toFormattedDateString() }}
        </li>
    </ul>
</div>



<p style="text-align: center; margin: 40px 0;">
    <a href="{{ url('/membership-dashboard') }}" style="
        background-color: #10B981;
        color: #ffffff;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
    ">View Your Account</a>
</p>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    We appreciate your business and hope you enjoy your upsell subscription.
</p>
@endsection