@extends('layouts.xhale_mail')

@section('title', 'Xhale Prize Draw Entries')
@section('header_title', 'New Prize Draw Entries')

@section('content')

<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">Hello, {{ $user->name }}!</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Congratulations! You've just received new entries into our prize draw.
</p>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Entry Details
    </h2>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>New Entries:</strong> <span style="color: #10B981;">{{ $numberOfEntries }}</span>
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Reason:</strong> Bonus from {{ $subscription->plan->name }} {{ $subscription->type }}
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>Entry Date:</strong> {{ now()->toFormattedDateString() }}
        </li>
    </ul>
</div>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
    <p style="color: #10B981; font-size: 18px; line-height: 26px; margin: 0;">
        <strong>Good luck in the draw!</strong>
    </p>
</div>


<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    We appreciate your business.
</p>

@endsection