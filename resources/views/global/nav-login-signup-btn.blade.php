 <div class="header-right d-xl-flex d-none">
 	@guest
 	@if (Route::has('login'))
 	<a class="login" href="{{ route('login') }}">{{ __('Log In') }}</a>
 	@endif
 	@if (Route::has('register'))
 	{{-- <a class="signup" href="{{ route('register') }}">{{ __('Sign Up') }}</a>  --}}
    <a href="#" class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">{{ __('Sign Up') }}</a>
 	@endif
 	@else 
 	@if (Auth::user()->level === 'member') 
 	<div class="user-wrapper me-1">
        <div class="notifiy-dropdown dropdown">
            <a href="#" class="text-dark position-relative" id="notifyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                @if(Auth::user()->unreadNotifications->count() > 0)
                <span class="notification-count position-absolute border border-light rounded-circle">
                    {{ Auth::user()->unreadNotifications->count() }}
                </span>
                @endif
            </a>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                {{-- Conditionally show the header section --}}
                @if(Auth::user()->unreadNotifications->count() > 0)
                <div class="sec-head d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-uppercase text-white">NOTIFICATIONS</h6>
                    <a href="{{ route('notifications.markAllAsRead') }}" class="text mb-0">Mark All As Read</a>
                </div>
                @endif
                
                <div class="notification-wrapper">
                    @forelse(Auth::user()->unreadNotifications as $notification)
                    <div class="notify-box">
                        <a href="{{ route('notifications.read', $notification->id) }}">
                            <div class="inner-content d-flex align-items-center gap-2">
                                <img src="{{ asset('images/bell-regular.svg') }}" class="img-fluid">

                                <div>
                                    <h6 class="text-white text-uppercase mb-0"> {{ $notification->data['title'] }}</h6>
                                    <p class="text-white mb-0">
                                        {{ $notification->data['message'] }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="notify-box">
                        <p class="text-white mb-0 text-center">No new notifications</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>


 		{{-- <a href="#" class="text-dark position-relative">
 			<i class="bi bi-bell"></i>
 			<span class="notification-count position-absolute border border-light rounded-circle">10</span>
 		</a>
       --}}


       <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
           @if(Auth::user()->profile_image)
           <img  src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="user-img me-2 previewImage">
           @else
           <img src="{{ asset('images/user.svg') }}" alt="User" class="user-img me-2 previewImage">
           @endif
       </a>
       <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
           <li><a class="dropdown-item" href="{{ route('subscriber.dashboard.index') }}">Dashboard</a> </li>
           <li><a class="dropdown-item" href="{{ route('subscription.manage') }}">Your Packages & Subscriptions</a> </li>
           <li><a class="dropdown-item" href="{{ route('logout') }}" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form></li>
            {{-- <li><a class="dropdown-item" href="{{ route('business.myaccount.index') }}">My Account</a></li> --}}
        </ul>
    </div>
</div>
@endif
@if (Auth::user()->level === 'business') 
<div class="">
 <a href="{{ route('business.myaccount.index') }}" class="d-flex align-items-center text-decoration-none" >
    <img src="{{ asset('images/logo.svg') }}" alt="User" class="user-img me-2 previewImage">
</a>
</div>
@endif
@endguest
{{-- <div class="header-search">
 <span><a href="{{ route('events')}}"><img src="{{ asset('images/search-icon.svg') }}"></a></span>
</div>  --}}

<div class="coming-soon-event">
    <i class="bi bi-list" data-bs-toggle="offcanvas" data-bs-target="#ComingSoonMenu" aria-controls="ComingSoonMenu"></i>

    <!-- Side Menu Content -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="ComingSoonMenu" aria-labelledby="ComingSoonMenuLabel">
      <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
        <div class="img-wrapper text-center">
            <img src="/images/footer-logo.svg">
            <p class="mb-0">Don't forget to breathe...</p>
        </div>
        <div class="sec-btns">
            <a href="/events" class="btn d-block">Events</a>
            <a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Mechcandise</a>
            <a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Charity</a>
        </div>
        <div class="auth-btns d-flex align-items-center justify-content-center gap-3 position-relative pb-5">

            @guest
            @if (Route::has('login'))
            <a class="login" href="{{ route('login') }}">{{ __('Log In') }}</a>
            @endif
            @if (Route::has('register'))
            <a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">{{ __('Sign Up') }}</a>
            @endif
            @else 
            <a class="login" href="{{ route('logout') }}" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @endif
        </div>
    </div>
</div>
</div>

</div>


<!-- Mobile User Wrapper -->
<div class="user-mob-wrapper user-wrapper d-xl-none d-flex">

    <div class="profile-wrapper dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            @auth
            @if(Auth::user()->profile_image)
            <img src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="user-img me-2 previewImage">
            @else
            <img src="{{ asset('images/user.svg') }}" alt="User" class="user-img me-2 previewImage">
            @endif
            @else
            <img src="{{ asset('images/user.svg') }}" alt="User" class="user-img me-2 previewImage">
            @endauth
        {{-- <img src="{{ asset('images/user.svg') }}" alt="User" class="user-img me-1 d-none">
        <img src="{{ asset('images/logout-user.svg') }}" alt="User" class="loggedin-user-img user-img me-1"> --}}
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">

        <div class="text-center">
            <div class="dropdown-logo">
                <img src="{{ asset('/images/footer-logo.svg') }}">
            </div>
        </div>


        @if (Auth::check() && (Auth::user()->level === 'member' || Auth::user()->level === 'business'))
        <div class="profile-info d-flex align-items-center">
            @if(Auth::user()->profile_image)
            <img  src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="user-img me-2 previewImage">
            @else
            <img src="{{ asset('images/user.svg') }}" alt="User" class="user-img me-2 previewImage">
            @endif
            <div>
                <h6 class="text-white text-uppercase mb-0">{{ Auth::user()->name }}</h6>
                @if (Auth::user()->level === 'member') 
                <a href="{{ route('subscriber.dashboard.index') }}" class="mb-0">View Profile</a>
                @endif 
            </div>
        </div>
    {{--  @if (Auth::user()->level === 'member') 
    <div class="subscription-notice position-relative mt-3">
        <h6 class="mb-1">You are currently on the Weekly subscription package.</h6>
        <p class="date-text mb-0">Next Payment On: 07/06/25 <span>For</span> $5.00</p>
        <a href="#" class="btn">Update</a>
    </div>
    @endif --}}


    @else
    <div class="profile-info info-logout-user d-flex align-items-center">
        <img src="{{ asset('images/logout-user.svg') }}" alt="User" class="loggedin-user-img user-img me-1">
        <div>
            {{-- <a href="{{ route('register')}}">Sign Up</a>  --}}
            <a   data-bs-toggle="modal" data-bs-target="#SignUpModal">Sign Up</a> / <a href="{{ route('login')}}">Login</a>
        </div>
    </div>
    @endif





    <div class="mob-menu">
        <ul class="list-unstyled">
            <li>
                <a class="{{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}"><span><img src="{{ asset('images/home-icon.svg') }}"></span> Home</a>
            </li>
            <li>
                <a class="{{ Request::routeIs('packages') ? 'active' : '' }}" href="{{ route('packages')}}"><span><img src="{{ asset('images/tags-solid-icon.svg') }}"></span> Giveaways</a>
            </li>
            <li>
                <a class="{{ Request::routeIs('discounts') ? 'active' : '' }}" href="{{ route('discounts')}}"><span><img src="{{ asset('images/piggy-bank-solid.svg') }}"></span> Discounts</a>
            </li>
            <li>
                <a class="{{ Request::routeIs('businessportal') ? 'active' : '' }}" href="{{ route('businessportal')}}"><span><img src="{{ asset('images/handshake-solid.svg') }}"></span> Business Portal</a>
            </li>
            <!-- <li>
                <a  class="{{ Request::routeIs('events') ? 'active' : '' }}" href="{{ route('events')}}"><span><img src="{{ asset('images/calendar-days-solid.svg') }}"></span> Events</a>
            </li> -->
            <li>
                <a  class="{{ Request::routeIs('events') ? 'active' : '' }}" href="#"><span><img src="{{ asset('images/calendar-days-solid.svg') }}"></span> Coming Soon</a>
                <ul class="coming-soon-submenu p-2 text-center mt-3">
                    <li><a href="/events" class="btn d-block">Events</a></li>
                    <li><a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Mechcandise</a></li>
                    <li><a href="#" class="btn d-block" data-bs-toggle="modal" data-bs-target="#comingSoonModal">Charity</a></li>
                </ul>
            </li>
            <li>
                <a class="{{ Request::routeIs('supportandlegal') ? 'active' : '' }}" href="{{ route('supportandlegal')}}"><span><img src="{{ asset('images/scale-balanced-solid.svg') }}"></span> Support & Legal</a>
            </li>

            @if (Auth::check() && (Auth::user()->level === 'member' || Auth::user()->level === 'business'))
            <li>
                <a href="{{ route('logout') }}" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span><img src="{{ asset('images/right-from-bracket-solid.svg') }}"></span>  Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
            @else
            <li>
                <a class="{{ Request::routeIs('login') ? 'active' : '' }}" href="{{ route('login')}}"><span><img src="{{ asset('images/right-from-bracket-solid.svg') }}"></span> Login</a>
            </li>
            @endif



        </ul>
    </div>
</div>
</div>
</div>

<!-- Modal -->
<div class="modal welcomeModal comingSoonModal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      <div class="modal-body p-0">
        <div class="row mx-0">
            <div class="col-sm-6 px-0">
                <div class="welcome-wrapper h-100 text-center">
                    <div class="welcome-img mx-3">
                        <div class="mob-logo">
                            <img src="images/logo.svg" class="img-fluid d-sm-none d-block">
                        </div>
                        <div class="sec-head text-uppercase text-center">Coming Soon</div>
                        <p class="mb-0 d-sm-block d-none">Keep and eye out for these new and exciting Xhale features.</p>                                
                    </div>
                </div>
            </div>
            <div class="col-sm-6 px-0">
                <div class="welcome-content text-center h-100">
                    <div class="modal-logo text-center mb-5 d-sm-block d-none">
                        <img src="images/logo-light.svg" class="img-fluid mx-auto">
                        <p class="mt-3 mb-0 text-white">Don't forget to breath...</p>
                    </div>
                    <div class="action-btns">
                        <p class="mb-4 text-white d-sm-none d-block">Keep and eye out for these new and exciting Xhale features.</p>
                        <a href="#" class="d-block signup">Events</a>
                        <a href="#" class="d-block login business-signup-btn my-3">Mechcandise</a>
                        <a href="#" class="d-block login">Charity</a>
                        <span class="skip-text mt-3 mb-0 text-uppercase">
                            @guest
                            @if (Route::has('login'))
                            <a href="{{ route('login') }}">{{ __('Log In') }}</a>
                            @endif
                            @if (Route::has('register'))
                            / 
                            <a   data-bs-toggle="modal" data-bs-target="#SignUpModal">{{ __('Sign Up') }}</a>
                            @endif
                            @else 
                            <a  href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            @endif

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>





