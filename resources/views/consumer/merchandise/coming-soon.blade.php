@extends('layouts.consumer')

@push('styles')
<style>
.coming-soon-wrap {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 4rem 1.5rem;
}
.coming-soon-inner {
    max-width: 560px;
    text-align: center;
}
.coming-soon-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 1.5rem;
}
.coming-soon-heading {
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.1;
    color: #111;
    margin-bottom: 1.5rem;
}
.coming-soon-divider {
    width: 48px;
    height: 3px;
    background: #111;
    margin: 0 auto 1.75rem;
}
.coming-soon-body {
    font-size: 15px;
    color: #666;
    line-height: 1.75;
    margin-bottom: 2.5rem;
}
.coming-soon-back {
    display: inline-block;
    background: #111;
    color: #fff;
    text-decoration: none;
    padding: 14px 40px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: background .2s;
}
.coming-soon-back:hover { background: #333; color: #fff; }
</style>
@endpush

@section('content')
<div class="coming-soon-wrap">
    <div class="coming-soon-inner">
        <p class="coming-soon-eyebrow">Shop</p>
        <h1 class="coming-soon-heading">Coming<br>Soon</h1>
        <div class="coming-soon-divider"></div>
        <p class="coming-soon-body">
            We're working on something great.<br>
            Check back soon for our latest collection.
        </p>
        <a href="{{ route('consumer') }}" class="coming-soon-back">Back to Home</a>
    </div>
</div>
@endsection
