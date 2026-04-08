@extends('layouts.xhale_mail')

@section('title', 'Xhale Password Reset')
@section('header_title', 'Password Reset')

@section('content')
    <p style="margin-top: 0;">Hello {{ $notifiable->name ?? 'there' }},</p>
    <p>You are receiving this email because we received a password reset request for your <span class="highlight-brand">Xhale</span> account.</p>

    <p style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ $url }}" class="button">
            Reset Password
        </a>
    </p>

    <p style="text-align: center; font-size: 14px; color: #aaaaaa;">
        This password reset link will expire in <strong style="color: #FFD700;">{{ $expireMinutes }} minutes</strong>.
    </p>

    <p>If you did not request a password reset, no further action is required.</p>

    <p style="margin-bottom: 0;">Regards,</p>
    <p style="margin-top: 5px;"><span class="highlight-brand">Xhale</span> Support Team</p>

    <p style="font-size: 12px; color: #aaaaaa; margin-top: 25px;">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $url }}" style="color: #3366CC; text-decoration: underline; word-break: break-all;">{{ $url }}</a>
    </p>
@endsection