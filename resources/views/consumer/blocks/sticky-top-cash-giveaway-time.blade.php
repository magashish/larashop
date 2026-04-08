@php
$latestDraw = getSecondDrawCountdown();
@endphp
@if ($latestDraw)
<section id="cashStickyBar" class="sticky-top-header-cash-giveway top-header-cash-giveway car-giveaway">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 order-lg-3 order-1">
                <div class="sticky-top-cash-giveway">
                 <p>{{ $latestDraw->title }} ${{ $latestDraw->cash_amount }} {{ $latestDraw->draw_date->format('l, F jS Y \a\t g:i A') }}</p> 

                 {{-- <p>{{ $latestDraw->title }}  {{ $latestDraw->draw_date->setTimezone('Australia/Brisbane')->format('D, j M Y • g:i A') }}</p> --}}

              {{--  <span class="top-header-cash-giveway-title">{{ $latestDraw->title }}</span> 
                <div class="clock" data-date="{{ $latestDraw->draw_date }}" id="slide-clock-{{ $latestDraw->id }}"></div>  --}}
            </div>
            </div>
        </div>
    </div>
</section>
<script>
const stickyBar = document.getElementById('cashStickyBar');
const triggerPoint = 100;

window.addEventListener('scroll', () => {
    if (!stickyBar) return;

    if (window.scrollY > triggerPoint) {
        stickyBar.classList.add('is-visible');
    } else {
        stickyBar.classList.remove('is-visible');
    }
}, { passive: true });


   {{--  document.addEventListener('scroll', function () {
    const stickyBar = document.getElementById('cashStickyBar');
    const mainMenu = document.querySelector('.main-menu');

    if (!stickyBar || !mainMenu) return;

    const menuBottom = mainMenu.getBoundingClientRect().bottom;

    if (menuBottom <= 0) {
        stickyBar.classList.add('is-visible');
    } else {
        stickyBar.classList.remove('is-visible');
    }
}); --}}

</script>
@endif


