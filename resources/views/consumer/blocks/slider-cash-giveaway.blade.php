@php
$draws = activePrizeDraws();
@endphp
@if ($draws->isNotEmpty())
<div class="prize-slider"> {{-- Slick wrapper --}}

@foreach ($draws as $draw)
@php
$prizeText = '';
$prizeAmount = '';
if ($draw->prize_type === 'cash') {
    $prizeText = 'Xhale';
    $prizeAmount = '$' . number_format($draw->cash_amount, 2);
} else {
    $prizeText = $draw->prize_title;
    $prizeAmount = $draw->prize_value_amount
    ? '$' . number_format($draw->prize_value_amount, 2)
    : '';
}
@endphp
<section class="cash-giveway car-giveaway pt-md-0 pt-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-12 order-lg-1 order-2">
             <div class="cash-img">
                @if (isset($draw->prize_image))
                <img class="lazy" data-original="{{ asset('storage/' . $draw->prize_image) }}" alt="Current Image">
                @endif
            </div> 

        </div>
        <div class="col-lg-5 col-12 order-lg-2 order-3">
            <div class="cash-content">
                <div class="bg-title small-title">
                    <div class="yellow-bg">{{ $prizeText }}</div><br>
                    <div class="black-bg"> {{ $prizeAmount }}</div>
                </div>
                <p>{!! $draw->prize_description ?? '' !!}</p>
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div>
        </div>
            <div class="col-12 order-lg-3 order-1">
                <div class="clock"
                data-date="{{ $draw->draw_date }}"
                id="clock-{{ $draw->id }}">
            </div>
        </div>
    </div>
</div>
<div>
    <img src="../images/notesImg.png" class="money-img d-lg-none d-block">
</div>
</section>
@endforeach
</div>
@endif