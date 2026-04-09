<header>
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-12 head">
                    <div class="header-inner">
                        <div class="logo">
                            <a class="navbar-brand" href="{{ url('/') }}">
                                {{ config('app.name', 'Laravel') }}
                                <img src="{{ asset('images/logo.svg') }}">
                            </a>
                        </div>
                        @auth
                        @if (Auth::user()->level === 'member' || Auth::user()->level === 'business')
                        <div class="auth-log-in"> <p class="mb-0">Hi,<span>{{ Auth::user()->name }}</span></p></div>
                        @endif
                        @endauth
                        <div class="navbar">
                            <ul class="main-menu"> 
                                <li class="{{ Request::is('/') ? 'active' : '' }}">
                                    <a href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="{{ Request::routeIs('about_us') ? 'active' : '' }}">
                                    <a href="{{ route('about_us')}}">About Us</a>
                                </li>

                                <li class="{{ Request::routeIs('packages') ? 'active' : '' }}">
                                    <a href="{{ route('packages')}}">Giveaways</a>
                                </li>

                                <li class="{{ Request::routeIs('shop') ? 'active' : '' }}">
                                    <a href="{{ route('shop')}}">Discounts</a>
                                </li>

                                <li class="{{ Request::routeIs('shop.merchandise.*') ? 'active' : '' }}">
                                    <a href="{{ route('shop.merchandise.index') }}">Shop</a>
                                </li>

                                <li class="{{ Request::routeIs('businessportal') ? 'active' : '' }}">
                                    <a href="{{ route('businessportal')}}">Business Portal</a>
                                </li>

                                @guest
                                <li class="d-block d-xl-none {{ Request::routeIs('login') ? 'active' : '' }}">
                                    <a class="login" href="{{ route('login') }}">{{ __('Log In') }}</a>
                                </li>
                                <li class="d-block d-xl-none {{ Request::routeIs('register') ? 'active' : '' }}">
                                    <a  class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">
                                        {{ __('Sign Up') }}
                                    </a>
                                </li>
                                @else
                               <li class="d-block d-xl-none"> <a class="login" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>?</li>
                                @endguest

                            </ul>
                        </div>
                        @include('global.nav-login-signup-btn')
                        <div class="menu_icon">
                            <span></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>

