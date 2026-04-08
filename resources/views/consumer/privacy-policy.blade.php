@extends('layouts.consumer')
@section('content')

<section class="image-banner-sec pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/package-desktop-banner.jpg') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/pacakge-mob-banner.jpg') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title" style="max-width: fit-content;">
            <div class="yellow-bg text-uppercase">XHALE</div>
            <div class="black-bg text-uppercase d-block"><h1>PRIVACY POLICY</h1></div>
        </div>
    </div>
</section>



@include('consumer.blocks.privacy-policy')


@endsection