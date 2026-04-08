<nav class="sidebar">
    <div class="sidebar-bg">
        <div class="logo-wrapper pt-4 mb-5">
            <a  href="{{ url('/') }}">
                <img src="{{ asset('images/XHALE-SECOND-LOGO.png') }}" class="img-fluid">
            </a>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item {{ request()->routeIs('subscriber.dashboard.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('subscriber.dashboard.index') }}">
                        <img src="{{ asset('images/house.png') }}" class="img-fluid" alt="Dashboard">
                        <span class="menu-title">Home</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('account.dashboard')}}">
                        <img src="{{ asset('images/user-regular.svg') }}" class="img-fluid">
                        <span class="menu-title">Accounts</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('billing.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('billing.dashboard')}}">
                        <img src="{{ asset('images/credit-card.svg') }}" class="img-fluid">
                        <span class="menu-title">Billing</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('membership.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('membership.dashboard')}}">
                        <img src="{{ asset('images/shield.png') }}" class="img-fluid">
                        <span class="menu-title">Membership</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('legal.documentation') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('legal.documentation')}}">
                        <img src="{{ asset('images/scale-balanced-solid.svg') }}" class="img-fluid">
                        <span class="menu-title">Legal Documentation</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('images/sign-out.png') }}" class="img-fluid" alt="Logout">
                    <span class="menu-title">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>
    </div>
</div>
</nav>

