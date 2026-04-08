@foreach ($events as $year => $months)
@foreach ($months as $monthYear => $eventsInMonth)

<div class="sec-tag mt-4 mb-3 rounded-pill d-inline-block month-header cash-{{ Str::slug($monthYear . '-' . $year, '-') }}">
    <p class="mb-0 text-uppercase">
        {{ $title }} >> <span>{{ $monthYear }}</span>
    </p>
</div>

@php
$isLoggedIn = Auth::check();
$linkUrl = $isLoggedIn ? route('packages') : route('register'); 
$linkText = $isLoggedIn ? 'GET MORE ENTRIES' : 'SIGN UP NOW';
@endphp

<div class="sec-box-outer">
    @foreach ($eventsInMonth as $event)

    <div class="tab-content-outer">
        <div class="row g-2">

            {{-- IMAGE --}}
            <div class="col-xl-2 col-md-6 order-2 d-lg-block d-none">
                <div class="inner-bg sec-img d-flex align-items-end">
                    <img
                    src="{{ $event->prize_image ? asset('storage/'.$event->prize_image) : asset('images/australian-dollar.png') }}"
                    class="img-fluid"
                    alt="{{ $event->title }}">
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="col-xl-7 order-xl-2 order-1">
                <div class="inner-bg content-sec p-lg-4 p-3">

                    <h2 class="d-lg-inline-block d-none">{{ $event->title }}</h2>

                    <h2 class="d-lg-none d-block text-uppercase mob-price-title text-center mb-1">
                        @if($event->prize_type === 'cash')
                        ${{ number_format($event->cash_amount, 2) }}
                        @else
                        {{ $event->prize_title }}
                        @endif
                    </h2>

                    <h2 class="d-lg-none d-block text-uppercase mob-title text-center">
                        {{ strtoupper($monthYear) }}
                    </h2>

                    {{-- SHORT DESCRIPTION --}}
                    @php
                    $plainText = strip_tags($event->prize_description);
                    $shortText = Str::limit($plainText, 180);
                    @endphp

                    <div class="event-description">
                        <p>{{ $shortText }}

                        @if(strlen($plainText) > 180)
                        <a href="javascript:void(0)"
                        class="read-more-link text-danger fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#cashEventModal-{{ $event->id }}">
                        Read more
                    </a>
                    @endif
                    </p>
                </div>

                <p class="text-sm text-gray-700 mt-2">
                    <strong>Draw Date Time:</strong> {{ Carbon\Carbon::parse($event->draw_date)->format('M d, Y h:i A') }}
                </p>

                {{-- MOBILE IMAGE --}}
                <div class="featured-mob-img d-lg-none d-block">
                    <img
                    src="{{ $event->prize_image ? asset('storage/'.$event->prize_image) : asset('images/australian-dollar.png') }}"
                    class="img-fluid mx-auto"
                    alt="{{ $event->title }}">
                </div>

            </div>
        </div>

        {{-- SIGNUP DESKTOP --}}
        <div class="col-xl-3 col-md-6 order-xl-3 order-3 d-lg-block d-none">
            <div class="inner-bg sign-up-box">
                <a href="{{ $linkUrl }}" class="btn btn-with-icon d-flex align-items-center justify-content-between">
                    {{ $linkText }}
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                        </svg>
                    </span>
                </a>
            </div>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="cashEventModal-{{ $event->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">{{ $event->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {!! $event->prize_description !!}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endforeach
</div>

{{-- SIGNUP MOBILE --}}
<div class="inner-bg sign-up-box d-lg-none d-block">
    <a href="{{ $linkUrl }}" class="btn btn-with-icon d-flex align-items-center justify-content-between">
        {{ $linkText }}
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
            </svg>
        </span>
    </a>
</div>

@endforeach
@endforeach
