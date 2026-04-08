@php
 $latestDraw = getLatestDrawCountdown();
@endphp
@if ($latestDraw)

@php
$prizeText = '';
$prizeAmount = '';
$prizeDescription = $latestDraw->prize_description;
if ($latestDraw->prize_type === 'cash') {
    $prizeText = "Xhale";
    $prizeAmount = '$' . number_format($latestDraw->cash_amount, 2);
} elseif ($latestDraw->prize_type === 'non_cash') {
    $prizeText = $latestDraw->prize_title;
    $prizeAmount = $latestDraw->prize_sub_title;
    // if (!empty($latestDraw->prize_value_amount)) {
    //     $prizeAmount .= '$' . number_format($latestDraw->prize_value_amount, 2);
    // }
}
@endphp



<section class="cash-giveway car-giveaway">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-12 order-lg-1 order-2">
               <div class="cash-img">
                @if (isset($latestDraw->prize_image))
                <img class="lazy lozad" src="{{ asset('storage/' . $latestDraw->prize_image) }}" alt="Current Image">
                @endif
            </div> 

        </div>
        <div class="col-lg-5 col-12 order-lg-2 order-3">
            <div class="cash-content">
                <div class="bg-title small-title">
                    <div class="yellow-bg">{{ $prizeText }}</div><br>
                    <div class="black-bg"> {{ $prizeAmount }}</div>
                </div>
                <p>{!! $prizeDescription ?? '' !!}</p>
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div>
        </div>
        <div class="col-12 order-lg-3 order-1">
            <div class="clock" data-date="{{ $latestDraw->draw_date }}" id="slide-clock-{{ $latestDraw->id }}"></div>
        </div>
    </div>
</div>
<div>
    <img src="../images/notesImg.png" class="money-img d-lg-none d-block">
</div>
</section>
@endif

