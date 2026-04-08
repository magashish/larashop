@extends('layouts.xhale_mail')

@section('title', 'Welcome to Xhale!')
@section('header_title', 'Welcome to Xhale!')

@section('content')
    <p style="margin-top: 0;">Hello {{ $user->name ?? 'there' }},</p>

    <p>
        Welcome to <span class="highlight-brand">Xhale</span>! We're thrilled to have you join our community.
    </p>

    <p>
        Your account has been successfully created. You can now log in and explore all the features we have to offer.
    </p>

    {{-- ✅ SHOW PASSWORD ONLY IF IT EXISTS --}}
    @if(!empty($password))
        <div style="background:#111; color:#fff; padding:15px; border-radius:6px; margin:20px 0;">
            <p style="margin:0 0 8px 0;"><strong>Your login details:</strong></p>
            <p style="margin:0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin:0;"><strong>Temporary Password:</strong> {{ $password }}</p>
            <p style="margin-top:10px; font-size:13px; color:#ffb3b3;">
                ⚠️ For security reasons, please log in and change your password immediately.
            </p>
        </div>
    @endif

    <p style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ $dashboardUrl }}" class="button">
            Go to Your Dashboard
        </a>
    </p>

    <p>
        If you have any questions or need assistance getting started, please don't hesitate to reach out to our support team.
    </p>

    <p style="margin-bottom: 0;">Best regards,</p>
    <p style="margin-top: 5px;">
        <span class="highlight-brand">Xhale</span> Team
    </p>

    <p style="font-size: 12px; color: #aaaaaa; margin-top: 25px;">
        If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $dashboardUrl }}"
           style="color: #3366CC; text-decoration: underline; word-break: break-all;">
            {{ $dashboardUrl }}
        </a>
    </p>
@endsection
