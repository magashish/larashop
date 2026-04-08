<ul class="list-unstyled d-flex align-items-center justify-content-lg-start justify-content-center flex-wrap gap-1">
    <li class="{{ Request::is('/') ? 'active' : '' }}">
        <a href="{{ url('/') }}">Home</a>
    </li>
    <li class="{{ Request::routeIs('packages') ? 'active' : '' }}">
        <a href="{{ route('packages')}}">Giveaways</a>
    </li>
    <li class="{{ Request::routeIs('discounts') ? 'active' : '' }}">
        <a href="{{ route('discounts')}}">Discounts</a>
    </li>
    <li class="{{ Request::routeIs('businessportal') ? 'active' : '' }}">
        <a href="{{ route('businessportal')}}">Business Portal</a>
    </li>
    <li class="{{ Request::routeIs('events') ? 'active' : '' }}">
        <a href="{{ route('events')}}">Events</a>
    </li>
    <li class="{{ Request::routeIs('supportandlegal') ? 'active' : '' }}">
        <a href="{{ route('supportandlegal')}}">Support & Legal</a>
    </li>
</ul>