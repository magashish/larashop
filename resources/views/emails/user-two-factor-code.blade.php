@extends('layouts.xhale_mail')

@section('title', 'Xhale Two-Factor Authentication')
@section('header_title', 'Your Two-Factor Code')

@section('content')
    <p style="margin-top: 0;">Hi {{ $notifiable->name ?? 'there' }},</p>
    <p>For your security, please use the following code to complete your login:</p>

    <div style="
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        letter-spacing: 3px;
        margin: 25px 0;
        background-color: #1a1a1a; /* Slightly lighter black for code background */
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #333333; /* Subtle border for definition */
    ">
        <span style="color: #FFD700;">{{ $notifiable->two_factor_code }}</span>
    </div>

    <p style="text-align: center;">This code will expire in 10 minutes.</p>

    <p>If you have not tried to login, please ignore this message.</p>

    <p style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ $verifyUrl }}" class="button">
            Verify Here
        </a>
    </p>

    <p style="margin-bottom: 0;">Regards,</p>
    <p style="margin-top: 5px;"><span class="highlight-brand">Xhale</span> Team</p>

    <p style="font-size: 12px; color: #aaaaaa; margin-top: 25px;">
        If you're having trouble clicking the "Verify Here" button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $verifyUrl }}" style="color: #3366CC; text-decoration: underline; word-break: break-all;">{{ $verifyUrl }}</a>
    </p>
@endsection