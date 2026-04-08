@extends('layouts.xhale_mail')

@section('title', 'Weekly Draw Reminder')
@section('header_title', 'Xhale Draw Tomorrow')

@section('content')
    @php
        $prizeText   = '';
        $prizeAmount = '';

        if ($draw->prize_type === 'cash') {
            $prizeText   = 'Xhale';
            $prizeAmount = '$' . number_format($draw->cash_amount, 2);
        } elseif ($draw->prize_type === 'non_cash') {
            $prizeText = $draw->prize_title;
             $prizeAmount = $latestDraw->prize_sub_title;

            // if (!empty($draw->prize_value_amount)) {
            //     $prizeAmount = '$' . number_format($draw->prize_value_amount, 2);
            // }
        }
    @endphp

    <p style="margin-top: 0;">Hi {{ $notifiable->name ?? 'there' }},</p>
    
    <p style="text-align: center; font-size: 24px; font-weight: bold; color: #FFD700;">
        🎉 Xhale Draw Tomorrow!
    </p>

    <p style="text-align: center; font-size: 16px;">
        Don’t forget, you’re in the running to win
        <strong>
            {{ $prizeText }}
            @if($prizeAmount)
                – {{ $prizeAmount }}
            @endif
        </strong>.
    </p>
    
    <p style="text-align: center; font-size: 16px;">
        Keep an eye out – winner drawn
        {{ \Carbon\Carbon::parse($draw->draw_date)->format('l, F jS') }}.
    </p>
    
    <p style="text-align: center; font-size: 24px; font-weight: bold; color: #FFD700; margin-top: 30px;">
        Good luck! 🍀
    </p>

    <p style="margin-bottom: 0; margin-top: 30px;">Regards,</p>
    <p style="margin-top: 5px;">
        <span class="highlight-brand">Xhale</span> Team
    </p>
@endsection
