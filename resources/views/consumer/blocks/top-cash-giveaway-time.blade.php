@php
    $latestDraw = getLatestDrawCountdown();
@endphp
@if ($latestDraw)
    <section class="top-header-cash-giveway car-giveaway">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 order-lg-3 order-1">
                    <span class="top-header-cash-giveway-title">WIN A {{ $latestDraw->title }}</span>
                    <div class="clock" data-date="{{ $latestDraw->draw_date }}" id="slide-clock-{{ $latestDraw->id }}"></div>
                </div>
            </div>
        </div>
    </section>
@endif