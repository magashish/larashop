@foreach ($events as $year => $months)
@foreach ($months as $monthYear => $eventsInMonth)
<div class="sec-tag mb-3 rounded-pill d-inline-block month-header {{$event_category}}-{{ Str::slug($monthYear, '-') }}">
    <p class="mb-0 text-uppercase {{ $monthYear }}">CASH GIVEAWAYS >> <span>{{ $monthYear }}</span></p>
</div>
@foreach ($eventsInMonth as $event)
<div class="tab-content-outer">
    <div class="row g-2">
        <div class="col-xl-2 col-md-6 order-2">
            <div class="inner-bg sec-img d-flex align-items-end">
                @if ($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" class="img-fluid" alt="{{ $event->name }} Image">
                @else
                <img src="{{ asset('images/australian-dollar.png') }}" class="img-fluid" alt="No Image Available">
                @endif
            </div>
        </div>
        <div class="col-xl-7 order-xl-2 order-1">
            <div class="inner-bg content-sec p-4">
                <h2 class="d-inline-block">{{ $event->name }}</h2>
                <div>{!! $event->description !!}</div>
                <p>{{ $event->created_at->format('M d, Y H:i A') }}</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 order-xl-3 order-3">
            <div class="inner-bg sign-up-box position-relative">
                <div class="bg-title small-title">
                    <div class="yellow-bg">DON'T MISS OUT</div>
                    <div class="black-bg">SIGN UP TODAY</div>
                </div>
                <a href="#" class="btn btn-with-icon d-flex align-items-center justify-content-between" tabindex="0">SIGN UP NOW <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"></path></svg></span></a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endforeach
@endforeach



