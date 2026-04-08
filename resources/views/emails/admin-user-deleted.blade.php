@extends('layouts.xhale_mail')

@section('title', 'User Account Deleted')
@section('header_title', 'User Account Permanently Deleted')

@section('content')

<h1 style="color: #ffffff; font-size: 28px; line-height: 36px; margin: 0 0 20px;">
    User Account Permanently Deleted
</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    This is an automated notification to inform you that a user account has been permanently deleted from the system.
</p>

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        User Details
    </h2>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Name:</strong> {{ $user->name }}
        </li>
        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>Email:</strong> {{ $user->email }}
        </li>
    </ul>
</div>

@endsection