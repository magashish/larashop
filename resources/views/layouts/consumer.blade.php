<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('global.head-google-tag-manager')

    <meta charset="utf-8">
    <meta name="fo-verify" content="55411b5e-09d7-4550-8c61-4ea5884199fd" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/exhale--logo.png') }}" sizes="48x48">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 

    <link defer href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link defer href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css" rel="stylesheet">




    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css">
    <link rel="stylesheet" type="text/css" href="https://www.xhale.com.au/build/assets/app-DbaZCfaT.css">
    <link rel="stylesheet" type="text/css" href="https://www.xhale.com.au/build/assets/style-ESbPOoOf.css">
    
    {{-- @vite(['resources/sass/app.scss',  'resources/css/slick.css',  'resources/css/external.css',  'resources/css/style.css', 'resources/js/app.js']) --}}

    <!-- Vite CSS -->
    @vite([
        'resources/sass/app.scss',
        'resources/css/slick.css',
        'resources/css/external.css',
        'resources/css/style.css'
        ])

    @stack('styles')
    </head>
    @php
    $pageClass = str_replace('/', '-', request()->path() ?: 'home');
    @endphp
    <body class="{{ Auth::check() ? 'logged-in dashboard-header' : 'logged-out' }} page-{{ $pageClass }} {{ $bodyClass ?? '' }}" data-is-guest="{{ Auth::guest() ? 'true' : 'false' }}">

        @include('global.body-google-tag-manager')

        <div id="loader-wrapper">
            <div id="loader">
                <img src="{{ asset('/images/logo.svg') }}">
            </div>
        </div> 

        @guest
        <div class="announcement-bar">
            <div class="container">
                @if(!empty($showLandingBar))
                <div class="announcement-bar-cash-giveaway">
                    @include('consumer.blocks.sticky-top-cash-giveaway-time')
                    @include('consumer.blocks.top-cash-giveaway-time')
                </div>
                @else
                <div class="announcement-bar-subscribe">
                <a href="{{ route('register') }}" class="announcement-link">
                    <p>Get More. Save More. Live Better. <strong>Subscribe Today!</strong></p>
                </a>
                <div class="top-social-links">
                    @include('includes.top-social-links')
                </div>
            </div>
                @endif
            </div>
        </div>

        @endguest

        <div id="main_wrap">
            <div id="navbar_wrap">
                @include('includes.consumer-navbar')


            </div>
            <div id="site_wrap">
                <main>
                    @yield('content')
                </main>
            </div>

            <div id="footer">

                <footer class="footer @if(Auth::check()) logged-in  @else logged-out @endif">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-1 col-md-4 footer-logo-col">
                                <div class="footer-logo">
                                    <a  href="{{ url('/') }}">
                                        <img src="/images/footer-logo.svg">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-6">
                                <div class="footer-menu my-lg-0 my-4">
                                   @include('includes.consumer-footer-navbar')
                                   @include('includes.social-links')
                               </div>
                           </div>
                           <div class="col-xl-3 col-lg-4 col-md-8 newsletter-col">
                            <div class="newsletter-wrapper">
                                <h6>Subscribe to Newsletter</h6>
                                <form id="newsletter-form">
                                    <div class="position-relative">
                                        <input type="email" name="email" id="newsletter-email" class="form-control" placeholder="Your email address here" required>
                                        <button type="submit" class="btn btn-primary">SUBSCRIBE</button>
                                    </div>
                                    <div id="newsletter-message" class="mt-2"></div>
                                </form>
                            </div>
                        </div>
                        @include('includes.mobile-footer-navbar')
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <style>

        .highlighted .offer-col-inner {
            box-shadow: 0 0 0 5px #FFD700, 0 4px 10px rgba(0, 0, 0, 0.4);
            border-radius: 20px;
        }

    </style>






@include('global.user-terms-accepted-form') 

<!-- Modal -->
@if(!request()->routeIs('register'))
<div class="modal welcomeModal SignUpModal fade" id="SignUpModal" tabindex="-1" aria-labelledby="SignUpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      <div class="modal-body p-0">
        <div class="row">
         @include('global.register-form') 
     </div>
 </div>
</div>
</div>
</div>
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@if (url()->current() !== url('/subscription-package'))
@include('global.stripe')
@endif

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('assets/tinymce48/tinymce.min.js') }}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery_lazyload/1.9.7/jquery.lazyload.min.js'></script>
--}}

<!-- FlipClock -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.js"></script>
{{-- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"></script> --}}
{{-- <script src="{{ asset('assets/tinymce48/tinymce.min.js') }}"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
<script>
   $(document).ready(function() {
    // Wrap all lozad images dynamically with loader
    $('.lozad').each(function() {
        if (!$(this).parent().hasClass('lozad-wrapper')) {
            $(this).wrap('<div class="lozad-wrapper w-100"></div>');
            $(this).parent().append('<div class="lozad-loader"></div>');
        }
    });

    // Initialize Lozad
    const observer = lozad('.lozad', {
        threshold: 0.1,
        loaded: function(el) {
            $(el).attr('data-loaded', 'true'); 
            $(el).fadeIn(500); 
            $(el).siblings('.lozad-loader').fadeOut(900, function() {
                $(this).remove();
            });
        }
    });

    observer.observe();
});

</script>


@stack('scripts') 
<script>
    window.appConfig = {
        routes: {
            discounts: "{{ route('discounts') }}",
            register: "{{ route('register') }}",
            login: "{{ route('login') }}",
            loginCheckRedirection: "{{ route('logincheckredirection') }}",
            ajaxSubscribe: "{{ route('ajax.subscribe') }}"
        },
        csrfToken: "{{ csrf_token() }}"
    };
</script>

{{-- @vite(['resources/js/consumer.js']) --}}

<!-- Vite JS -->
@vite([
    'resources/js/app.js',
    'resources/js/consumer.js',
    'resources/js/flipclock.js'
    ])

    @stack('scripts')


</body>
</html>
