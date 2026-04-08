@extends('layouts.xhale_mail')

@section('title', 'Welcome to Xhale Business')
@section('header_title', 'Welcome!')

@section('content')

<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">
    Hello, {{ $user->first_name }}! 👋
</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Welcome to Xhale! Your business account has been successfully created. You can now log in and start managing your offers and connecting with new customers.
</p>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Account Details
    </h2>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>Email:</strong> <span style="color: #10B981;">{{ $user->email }}</span>
        </li>
    </ul>
</div>

<p style="text-align: center; margin: 40px 0;">
    <a href="{{ url('/login') }}" style="
        background-color: #10B981;
        color: #ffffff;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
    ">Login to Your Account</a>
</p>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Thank you for joining our platform. We're excited to have you!
</p>
@endsection