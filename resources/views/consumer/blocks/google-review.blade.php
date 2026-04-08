@php
    $reviews = get_google_reviews();
    $limit = 150;
@endphp

<div class="review-slider">
    @foreach($reviews as $review)
    @php
        $text = $review->review_text;
        $short = \Illuminate\Support\Str::limit($text, $limit, '');
        $hasMore = strlen($text) > $limit;
        $imgUrl = str_starts_with($review->author_img, 'http') 
                  ? $review->author_img 
                  : asset('storage/' . $review->author_img);
    @endphp

    <div class="review-items">
        <div class="author-img">
            <img src="{{ $imgUrl }}" alt="{{ $review->author_name }}">
            <img src="{{ asset('images/' . $review->type . '.svg') }}" class="over-icon"  alt="{{ $review->type }}">
        </div>

        <div class="author-name">{{ $review->author_name }}</div>

       

        <div class="rating-icon rating-stars mt-2 mb-2" style="--rating: {{ $review->rating }};">
            <span class="stars-bg">★★★★★</span>
            <span class="stars-fill">★★★★★</span>
        </div>

        {{-- Desktop Version --}}
        <div class="review-content review-content-desktop d-none d-lg-block">
            <p class="review-text">
                <span class="short-text">{!! $text !!}</span>
            </p>
        </div>

        {{-- Mobile Version --}}
        <div class="review-content review-content-mobile d-block d-lg-none">
            <p class="review-text">
                <span class="short-text">{!! $short !!}</span>
                @if($hasMore)
                <span class="ellipsis">... </span>
                <span class="full-text" style="display:none">{!! substr($text, $limit) !!}</span>
                @endif
            </p>
            @if($hasMore)
            <button type="button" class="read-more-btn" aria-expanded="false">Read more</button>
            @endif
        </div>

        <p>
            <strong>{{ $review->type }}</strong> rating score:
           <strong>{{ number_format($review->rating, 1) }}</strong> of 5
        </p>
    </div>
    @endforeach
</div>

