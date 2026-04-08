@extends('layouts.xhale_mail')

@section('title', 'Package Expiry Reminder')
@section('header_title', 'Package Expiry Reminder')

@section('content')
    <p style="margin-top: 0;">Hi {{ $notifiable->name ?? 'there' }},</p>
    
    <p style="font-size: 16px;">
        ⚡ Heads up – your Xhale package is about to run out! Renew now to stay in the draw for our upcoming prizes and keep access to exclusive offers 👉 <a href="{{ url('/') }}">Renew Now</a>. Don’t miss out!
    </p>

    <p style="margin-bottom: 0;">Regards,</p>
    <p style="margin-top: 5px;"><span class="highlight-brand">Xhale</span> Team</p>
@endsection