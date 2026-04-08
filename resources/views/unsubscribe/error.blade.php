@extends('layouts.consumer')
@section('content')


<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0 unsubscribe-wrapper">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/Xhale_team_shoot_-_Hero_Banner.webp') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/Xhale_team_shoot_-_Hero_Banner.webp') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="inner-content text-center py-5">
            <h1 class="text-danger">❌ Error!</h1>
            <p class="lead mt-3">{{ $message ?? 'Something went wrong. Please try again later.' }}</p>
            <a href="{{ url('/') }}" class="btn-home signup">Back to Home</a>
        </div>
    </div>
</section>


@endsection