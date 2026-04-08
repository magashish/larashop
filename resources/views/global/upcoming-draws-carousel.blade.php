<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

@php
    $draws = upcoming_draws_carousel();
    $total = $draws->count();
@endphp
@if($total > 0)
<section class="draws-section">
    <div class="draws-header">
        <div class="draws-title-group">
            <span class="draws-label-top">UPCOMING</span>
            <span class="draws-label-main">PRIZE DRAWS</span>
        </div>
        <div class="draws-controls">
            @if($total > 2)
            <button class="draws-nav-btn" id="drawsPrev">&#8592;</button>
            <button class="draws-nav-btn" id="drawsNext">&#8594;</button>
            @endif
            <span class="draws-count-badge">
                {{ $total }} Draw{{ $total !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    <div class="draws-slider" id="drawsSlider">
        @foreach($draws as $draw)
        <div class="draw-card-wrap">
            <div class="draw-card" data-date="{{ \Carbon\Carbon::parse($draw->draw_date)->toIso8601String() }}">
                <div class="draw-card-type">{{ strtoupper($draw->draw_type) }}</div>
                <div class="draw-card-title">{{ $draw->title }}</div>
                <div class="draw-card-desc">{{ $draw->prize_description }}</div>
                <div class="draw-countdown"></div>
                <div class="draw-card-footer">
                    <span class="draw-prize-badge">
                        @if($draw->prize_type === 'cash')
                            ${{ number_format($draw->cash_amount, 0) }} Cash
                        @else
                            {{ $draw->prize_title }}
                        @endif
                    </span>
                    <span class="draw-card-date">
                        {{ \Carbon\Carbon::parse($draw->draw_date)->format('j M Y \a\t h:i a') }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</section>

<style>
.draws-section {
    background: #ffffff;
    padding: 2.5rem 1.5rem 3.5rem;
    font-family: Arial, Helvetica, sans-serif;
}
.draws-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 12px;
}
.draws-title-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.draws-label-top {
    display: inline-block;
    background: #f5c518;
    color: #111111;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 2px;
    padding: 3px 10px;
    border-radius: 3px;
    width: fit-content;
}
.draws-label-main {
    display: inline-block;
    background: #111111;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1.5px;
    padding: 5px 16px;
    border-radius: 3px;
    width: fit-content;
}
.draws-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}
.draws-nav-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1.5px solid #dddddd;
    background: #ffffff;
    color: #111111;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, border-color 0.2s;
    line-height: 1;
}
.draws-nav-btn:hover {
    background: #f5c518;
    border-color: #f5c518;
}
.draws-count-badge {
    background: #f5c518;
    color: #111111;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 18px;
    border-radius: 20px;
}

/* Slider */
.draws-slider { margin: 0 -10px; }
.draw-card-wrap { padding: 0 10px; outline: none; }

.draw-card {
    background: #ffffff;
    border: 1.5px solid #e8e8e8;
    border-radius: 12px;
    padding: 1.4rem 1.25rem;
    display: flex !important;
    flex-direction: column;
    gap: 8px;
    min-height: 210px;
    box-sizing: border-box;
    outline: none;
}
.draw-card-type {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #aaaaaa;
}
.draw-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #111111;
    line-height: 1.3;
}
.draw-card-desc {
    font-size: 13px;
    color: #888888;
    line-height: 1.55;
    flex: 1;
}
.draw-countdown {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin: 4px 0;
}
.draw-countdown .cblock {
    background: #111111;
    border-radius: 6px;
    padding: 6px 12px;
    text-align: center;
    min-width: 52px;
}
.draw-countdown .cnum {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}
.draw-countdown .clbl {
    display: block;
    font-size: 8px;
    color: #aaaaaa;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-top: 2px;
}
.draw-expired {
    font-size: 12px;
    font-weight: 700;
    color: #e24b4a;
}
.draw-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 2px solid #f5c518;
    margin-top: 4px;
    flex-wrap: wrap;
    gap: 8px;
}
.draw-prize-badge {
    background: #f5c518;
    color: #111111;
    font-size: 13px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 4px;
}
.draw-card-date { font-size: 12px; color: #aaaaaa; }

/* Slick dots */
.draws-slider .slick-dots { bottom: -32px; }
.draws-slider .slick-dots li button:before {
    font-size: 8px;
    color: #dddddd;
    opacity: 1;
}
.draws-slider .slick-dots li.slick-active button:before {
    color: #f5c518;
    opacity: 1;
}

@media (max-width: 1024px) {
    .draw-card-title { font-size: 16px; }
}
@media (max-width: 640px) {
    .draws-header { flex-direction: column; align-items: flex-start; }
    .draw-card-title { font-size: 15px; }
    .draws-section { padding: 1.5rem 1rem 3rem; }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script>
$(document).ready(function () {

    var $slider = $('#drawsSlider');
    var total   = {{ $total }};

    // How many to show based on total
    var showDesktop = total >= 2 ? 2 : total;
    var showTablet  = total >= 2 ? 2 : total;
    var showMobile  = 1;

    $slider.slick({
        slidesToShow   : showDesktop,
        slidesToScroll : 1,
        initialSlide   : 0,           // always start from first
        autoplay       : true,
        autoplaySpeed  : 3500,
        speed          : 500,
        dots           : total > showDesktop,
        arrows         : false,
        infinite       : total > showDesktop,
        pauseOnHover   : true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow   : showTablet,
                    dots           : total > showTablet,
                    infinite       : total > showTablet,
                }
            },
            {
                breakpoint: 640,
                settings: {
                    slidesToShow   : showMobile,
                    dots           : total > 1,
                    infinite       : total > 1,
                }
            }
        ]
    });

    $('#drawsPrev').on('click', function () { $slider.slick('slickPrev'); });
    $('#drawsNext').on('click', function () { $slider.slick('slickNext'); });

    // Countdown
    function timeLeft(dateStr) {
        var diff = new Date(dateStr) - new Date();
        if (diff <= 0) return null;
        return {
            d: Math.floor(diff / 86400000),
            h: Math.floor(diff % 86400000 / 3600000),
            m: Math.floor(diff % 3600000  / 60000),
            s: Math.floor(diff % 60000    / 1000)
        };
    }

    function block(val, lbl) {
        return '<div class="cblock"><span class="cnum">' + val + '</span><span class="clbl">' + lbl + '</span></div>';
    }

    function buildCountdown($card) {
        var $cd = $card.find('.draw-countdown');
        var t   = timeLeft($card.data('date'));
        if (!t) { $cd.html('<span class="draw-expired">Draw Completed</span>'); return; }
        $cd.html(block(t.d,'Days') + block(t.h,'Hrs') + block(t.m,'Min') + block(t.s,'Sec'));
    }

    function tickAll() {
        $('#drawsSlider .draw-card').each(function () {
            var $c = $(this);
            if ($c.find('.draw-expired').length) return;
            var t = timeLeft($c.data('date'));
            if (!t) { buildCountdown($c); return; }
            $c.find('.cnum').eq(0).text(t.d);
            $c.find('.cnum').eq(1).text(t.h);
            $c.find('.cnum').eq(2).text(t.m);
            $c.find('.cnum').eq(3).text(t.s);
        });
    }

    $('#drawsSlider .draw-card').each(function () { buildCountdown($(this)); });
    setInterval(tickAll, 1000);
});
</script>

@endif