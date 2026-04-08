<div class="hero-slider-section">
    <div class="hero-slider hero-sliderLogin-sec" id="hero-slider-login-sec">
        @foreach (is_featured_offers() as $offer)
        <div class="slider-item">
            <div class="innner-content">
                <div class="item-content">
                    <div class="sec-tag d-flex align-item-center gap-2">
                        @if ($offer->categories->isNotEmpty())
                        @php
                        $category = $offer->categories->first(); 
                        @endphp

                        @if ($category->icon)
                        <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}" class="img-fluid">
                        @endif

                        <p class="mb-0">{{ $category->name }}</p>
                        @endif
                    </div>
                    <h3 class="my-2">
                        {{ $offer->organisation->title ?? 'Organization Name' }}
                    </h3>
                    <p>{{ html_entity_decode($offer->title) }}</p>
                    <a href="{{ route('discounts') }}?busness={{ $offer->organisation->id ?? '' }}" class="btn btn-with-icon d-flex align-items-center justify-content-between">
                        EXPLORE NOW
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                        </span>
                    </a>
                </div>

                <div class="item-img">
                    @php
                    $firstAttachment = $offer->attachments->first();
                    @endphp
                    @if ($firstAttachment)
                    <img data-lazy="{{ Storage::url($firstAttachment->file_image) }}" class="img-thumbnail" alt="Offer Image" width="800" height="450" >
                    @else
                    <img data-lazy="{{ asset('images/ubx.png') }}" class="img-fluid" alt="Default Offer Image">
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
