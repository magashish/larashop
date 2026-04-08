@extends('layouts.xhale_mail')

@section('title', 'Account Deleted')
@section('header_title', 'Account Permanently Deleted')

@section('content')

<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">Hello, {{ $user->name }}!</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    This email is to confirm that your account on our service has been permanently deleted at your request.
</p>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Important Information
    </h2>
    <p style="color: #ffffff; font-size: 16px; line-height: 24px; margin: 0 0 10px;">
        All of your data has been removed from our system.
    </p>
    <p style="color: #ffffff; font-size: 16px; line-height: 24px; margin: 0;">
        If you believe this was in error, please contact our support team immediately.
    </p>
</div>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    Thank you, and best regards,
</p>

@endsection