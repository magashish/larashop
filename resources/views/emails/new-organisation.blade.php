@extends('layouts.xhale_mail')

@section('title', 'New Organisation Account')
@section('header_title', 'New Organisation')

@section('content')

<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">
    Hello, Administrator!
</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    A new organisation has just been created and is awaiting your review and approval.
</p>

<!-- Organisation Details Card -->
<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Organisation Details
    </h2>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Organisation Name:</strong> <span style="color: #10B981;">{{ $organisation->title }}</span>
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>Account User:</strong> <span style="color: #10B981;">{{ $organisation->user->name }}</span>
        </li>
    </ul>
</div>

<p style="text-align: center; margin: 40px 0;">
    <a href="{{ route('organisations.edit', $organisation->id) }}" style="
        background-color: #10B981;
        color: #ffffff;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
    ">View Organisation Details</a>
</p>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Please review the details and take the necessary action.
</p>

@endsection
