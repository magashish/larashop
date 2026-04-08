@php
$allReviews = get_google_reviews();
$total = $allReviews->count();
$charLimit = 120;
if ($total > 8) {
    $middle = ceil($total / 2);
    $topReviews = $allReviews->slice(0, $middle);
    $bottomReviews = $allReviews->slice($middle);
} else {
    $topReviews = $allReviews;
    $bottomReviews = collect();
}
@endphp


<section class="client-testimonial-sec">
    <div class="container-fluid px-0">
        <div class="review-slider-top" dir="rtl">
            @foreach($topReviews as $review)
                @include('consumer.blocks.review-card', [
                    'review' => $review,
                    'limit' => $charLimit
                ])
            @endforeach
        </div>
        <div class="review-slider-bottom mt-4">
            @foreach($bottomReviews as $review)
                @include('consumer.blocks.review-card', [
                    'review' => $review,
                    'limit' => $charLimit
                ])
            @endforeach
        </div>
    </div>
</section>
