 	@guest
 	@if (Route::has('login'))
 	<a class="login" href="{{ route('login') }}">{{ __('Log In') }}</a>
 	@endif
 	@if (Route::has('register'))
 	<a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">{{ __('Sign Up') }}</a>
 	@endif
 	@else 
 	@if (Auth::user()->level === 'member') 
 	<a class="login" href="{{ route('subscriber.dashboard.index') }}">Dashboard</a> 
 	@endif
 	@if (Auth::user()->level === 'business') 
 	<a class="signup" href="{{ route('business.myaccount.index') }}">My Account</a>
 	@endif
 	@endguest
