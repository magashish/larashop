@php
$cleanText = html_entity_decode(strip_tags($review->review_text));
$textLength = mb_strlen($cleanText);
$isLong = $textLength > $limit;
$imgUrl = str_starts_with($review->author_img, 'http')
? $review->author_img
: asset('storage/' . $review->author_img);
$uniqueId = 'reviewModal-' . $review->id;

    // Create truncated text with proper ellipsis
if ($isLong) {
    $shortText = mb_substr($cleanText, 0, $limit);
    $lastSpace = mb_strrpos($shortText, ' ');
    if ($lastSpace !== false && $lastSpace > ($limit - 20)) {
        $shortText = mb_substr($shortText, 0, $lastSpace);
    }
    $shortText = rtrim($shortText, '.,!?;:') . '...';
} else {
    $shortText = $cleanText;
}
@endphp
<div class="review-items">
    <div class="author-img">
        <img src="{{ $imgUrl }}" alt="{{ $review->author_name }}" loading="lazy">
        <img src="{{ asset('images/' . $review->type . '.svg') }}" class="over-icon"  alt="{{ $review->type }}">
    </div>
    <div class="author-name">{{ $review->author_name }}</div>
    

    <div class="rating-stars mt-2 mb-2" style="--rating: {{ $review->rating }};">
        <span class="stars-bg">★★★★★</span>
        <span class="stars-fill">★★★★★</span>
    </div>

    <p class="review-text">
        {{-- {!! $review->review_text !!} --}}
        {!! \Illuminate\Support\Str::words(strip_tags($review->review_text), 50, ' ...') !!}
    </p>
    <p>
        <strong>{{ $review->type }}</strong> rating score:
        <strong>{{ number_format($review->rating, 1) }}</strong> of 5
    </p>
</div>