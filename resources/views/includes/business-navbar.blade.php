 <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
            <img src="{{ asset('/images/logo.svg') }}">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @endif

                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else

             

                @if (Auth::user()->isBusinessAccount() )
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('business.users.index') }}">{{ __('Users') }}</a>
                </li>
                @endif 
                @if (Auth::user()->isBusinessAccount() || Auth::user()->isSubBusinessAccount())
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('business.offers.index') }}">{{ __('Offers') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('business.organisations.index') }}">{{ __('Organisations') }}</a>
                </li>
                @endif

                @if (Auth::user()->isMemberAccount())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('subscriber.dashboard.index') }}">{{ __('Dashboard') }}</a>
                </li>
                @endif 

                
                
                {{-- @if (Auth::user()->isBusinessAccount() )
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('business.prize_draws.index') }}">{{ __(' Prize Draws') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('business.users.index') }}">{{ __('Users') }}</a>
                </li>
                @endif  --}}

                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>

            
            @endguest
        </ul>
    </div>
</div>
</nav>