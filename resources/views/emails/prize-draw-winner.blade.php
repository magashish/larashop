@extends('layouts.xhale_mail')

@section('title', 'Xhale Prize Draw Winner!')
@section('header_title', 'You Are A Winner!')

@section('content')
@php
    $prizeText   = '';
    $prizeAmount = '';

    if ($prizeDraw->prize_type === 'cash') {
        $prizeText   = 'Cash Prize';
        $prizeAmount = '$' . number_format($prizeDraw->cash_amount, 2);
    } elseif ($prizeDraw->prize_type === 'non_cash') {
        $prizeText = $prizeDraw->prize_title;
        $prizeAmount = $latestDraw->prize_sub_title;

        // if (!empty($prizeDraw->prize_value_amount)) {
        //     $prizeAmount = '$' . number_format($prizeDraw->prize_value_amount, 2);
        // }
    }
@endphp

<h1 style="color: #10B981; font-size: 28px; line-height: 36px; margin: 0 0 20px;">
    🎉 Congratulations, {{ $user->first_name }}! 🎉
</h1>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    We are absolutely thrilled to announce that you have been chosen as the
    <strong>official winner</strong> of our recent prize draw!
</p>

{{-- Prize Image --}}
@if (!empty($prizeDraw->prize_image))
    <div style="text-align: center; margin: 0 0 30px;">
        <img
            src="{{ asset('storage/' . $prizeDraw->prize_image) }}"
            alt="Prize Image"
            style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #10B981;"
        >
    </div>
@endif

<div style="background-color: #333333; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #ffffff; font-size: 20px; line-height: 28px; margin: 0 0 15px; border-bottom: 1px solid #444444; padding-bottom: 10px;">
        Your Winning Details
    </h2>

    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="color: #ffffff; font-size: 16px; line-height: 24px; margin-bottom: 10px;">
            <strong>Prize:</strong>
            <span style="color: #10B981;">
                {{ $prizeText }}
                @if($prizeAmount)
                    – {{ $prizeAmount }}
                @endif
            </span>
        </li>

        <li style="color: #ffffff; font-size: 16px; line-height: 24px;">
            <strong>Date Drawn:</strong>
            {{ $prizeDraw->draw_datetime
                ? $prizeDraw->draw_datetime->toFormattedDateString()
                : 'N/A'
            }}
        </li>
    </ul>
</div>

<p style="color: #bbbbbb; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
    We look forward to seeing you claim your reward!
    If you have any immediate questions, please contact our support team.
</p>
@endsection
