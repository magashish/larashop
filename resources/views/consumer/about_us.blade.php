@extends('layouts.consumer')
@section('content')


<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/Xhale_team_shoot_-_Hero_Banner.webp') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/Xhale_team_shoot_-_Hero_Banner.webp') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title" style="max-width: fit-content;">
            <div class="yellow-bg text-uppercase">XHALE</div>
            <div class="black-bg text-uppercase d-block"><h1>ABOUT US</h1></div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->



@include('consumer.blocks.about-us')
@include('consumer.blocks.social-feeds')

@endsection
