@extends('layouts.xhale_mail')

@section('title', 'Complete Your Registration')
@section('header_title', 'Almost there!')

@section('content')
    <p>Hi {{ $user->name ?? 'there' }},</p>

    <p style="font-size: 16px;">
        {{ $messageText }}
    </p>

    <p>
        <a href="{{ $registerUrl }}" class="button">
            Complete Registration
        </a>
    </p>

    <p>Regards,<br><strong>Xhale Team</strong></p>
@endsection
