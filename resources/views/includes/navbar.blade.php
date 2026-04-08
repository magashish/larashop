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


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                </li>

                @if(Auth::user()->level === 'admin')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.statsdashboard') }}">
                        {{ __('Stats') }}
                    </a>
                </li>
                @endif


                @can('view offers')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('offers.index') }}">{{ __('Offers') }}
                        @php
                        $offersCount = get_offers_count();
                        @endphp
                        @if ($offersCount > 0)
                        <span class="badge">{{ $offersCount }}</span>
                        @endif
                    </a>
                </li>
                @endcan

                @can('view organisations')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('organisations.index') }}">{{ __('Businesses') }}
                        @php
                        $organisationsCount = get_organisations_count();
                        @endphp
                        @if ($organisationsCount > 0)
                        <span class="badge">{{ $organisationsCount }}</span>
                        @endif
                    </a>
                </li>
                @endcan


                @can('view promo_codes')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('promo_codes.index') }}">{{ __('Promo Codes') }}</a>
                </li>
                @endcan

                @can('view prize_draws')
                <li class="nav-item dropdown">
                    <a id="navbarDropdownprizedraws" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                       Draws
                   </a>
                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownprizedraws">
                    <a class="dropdown-item" href="{{ route('prize_draws.index') }}">{{ __('Prize Draws') }}</a>
                    <a class="dropdown-item" href="{{ route('entries.index') }}">{{ __('Prize Draws Entries') }}</a>
                    <a class="dropdown-item" href="{{ route('admin.prizedrawlog') }}">{{ __('Prize Draw Logs') }}</a>
                </div>
            </li> 
            @endcan




               {{--  <li class="nav-item">
                    <a class="nav-link" href="{{ route('prize_draws.index') }}">{{ __('Prize Draws') }}</a>
                </li>
                --}}
                @can('view faqs')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('faqs.index') }}">{{ __('FAQs') }}</a>
                </li>
                @endcan
                <li class="nav-item dropdown">
                    {{-- The dropdown will only show if the user has any of these permissions --}}
                    @canany(['view categories', 'view users', 'view admins'])
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Platform Setup
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('plans.index') }}">{{ __('Subscription Plans') }}</a>
                        <a class="dropdown-item" href="{{ route('pages.index') }}">{{ __('Pages') }}</a>
                        <a class="dropdown-item" href="{{ route('google-reviews.index') }}">{{ __('Google reviews') }}</a>
                        @can('view categories')
                        <a class="dropdown-item" href="{{ route('categories.index') }}">{{ __('Categories') }}</a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('events.index') }}">{{ __('Events') }}</a>
                        @can('view users')
                        <a class="dropdown-item" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                        <a class="dropdown-item" href="{{ route('unregisteredusers.index') }}">{{ __('Unregistere Users') }}</a>
                        @endcan
                        @can('view admins')
                        <a class="dropdown-item" href="{{ route('admins.index') }}">{{ __('Admins') }}</a>
                        @endcan
                    </div>
                    @endcanany
                </li>
                
                 {{-- <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Config 
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                        <a class="dropdown-item" href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a>
                    </div>
                </li>  --}}

                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
            @endguest
        </ul>
    </div>
</div>
</nav>