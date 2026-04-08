<div class="col-12 d-lg-none d-block">
    <div class="footer-mob">
        <ul class="list-unstyled d-flex align-items-center">
            <li><a href="{{ route('discounts')}}" class="menu-item {{ Request::routeIs('discounts') ? 'active' : '' }}"><img src="{{ asset('images/cart-icon.svg') }}"></a></li>
            <li><a href="{{ route('events')}}" class="menu-item {{ Request::routeIs('events') ? 'active' : '' }}"><img src="{{ asset('images/calendar-days-solid.svg') }}"></a></li>
            <li>
                <a href="{{ url('/') }}" class="menu-logo {{ Request::is('/') ? 'active' : '' }}"><img src="{{ asset('images/exhale--logo.png') }}"></a>
                @auth
                @if (Auth::user()->level === 'member')
                <a href="{{ route('subscriber.dashboard.index') }}" class="menu-item live-stream-icon"><img src="{{ asset('images/live-streaming.svg') }}"></a>
                @endif
                @if (Auth::user()->level === 'business')
                <a href="{{ route('subscriber.dashboard.index') }}" class="menu-item live-stream-icon"><img src="{{ asset('images/live-streaming.svg') }}"></a>
                @endif
                @endauth
            </li>
            <li>
                <a href="{{ route('business.myaccount.index')}}" class="menu-item user-icon"><img src="{{ asset('images/user-regular.svg') }}"></a>
                <a href="{{ route('discounts')}} {{ Request::routeIs('discounts') ? 'active' : '' }}" class="menu-item search-icon"><img src="{{ asset('images/search-icon.svg') }}"></a>
            </li>
            <li>
                <span class="menu-item footer-menu-icon"><img src="{{ asset('images/hamburer-icon.png') }}"></span>
                <!-- @auth
                @if (Auth::user()->level === 'member')
                <a href="{{ route('subscriber.dashboard.index') }}" class="menu-item live-stream-icon"><img src="{{ asset('images/live-streaming.svg') }}"></a>
                @endif
                @if (Auth::user()->level === 'business')
                <a href="{{ route('subscriber.dashboard.index') }}" class="menu-item live-stream-icon"><img src="{{ asset('images/live-streaming.svg') }}"></a>
                @endif
                @endauth    -->   
            </li>
        </ul>
    </div>
</div>
