@extends('layouts.xhale_mail')

@section('title', 'Don’t miss your chance!')
@section('header_title', 'You’re missing out!')

@section('content')
    <p style="margin-top: 0;">Hi {{ $user->title ?? 'there' }},</p>
    
    <p style="font-size: 16px;">
        🔥 Don’t sit this one out – tomorrow’s Xhale draw is for <strong>{{ $draw->title }}</strong>!
    </p>

    <p style="font-size: 16px;">
        Reactivate your package or subscription today to get back in the game 👉 <a href="{{ url('/') }}" class="button">
            Reactivate Now
        </a>.
    </p>

    <p style="margin-bottom: 0;">Regards,</p>
    <p style="margin-top: 5px;"><span class="highlight-brand">Xhale</span> Team</p>
@endsection