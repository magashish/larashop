@extends('layouts.consumer')
@section('content')


<section class="image-banner-sec pb-0">
	<div class="container">
		<div class="inner-content">
			<img src="{{ asset('images/event_desktop.webp') }}" class="d-lg-block d-none" alt="Banner Image">
			<img src="{{ asset('images/event_mobile.webp') }}" class="d-lg-none d-block" alt="Banner Image">
		</div>
		<div class="bg-title">
			<div class="yellow-bg text-uppercase">WHAT'S ON</div>
			<div class="black-bg text-uppercase"><h1>AT XHALE</h1></div>
		</div>
	</div>
</section>

<section class="packages-quote-sec pt-lg-0 pb-5 d-lg-block d-none">
	<div class="container ">
		<div class="row">
			<div class="content-box">
				<div class="image-box">
					<img src="images/Screenshot_2025-06-26_125700-removebg-preview.png" alt="">
				</div>
				<p class="medium">Your wellness destination to access deals, support local and prioritise your wellbeing and be in for the chance to win cash and prizes!</p>
			</div>
		</div>
	</div>
</section>



<section class="giveaway-sec pt-0">
	<div class="container">
		<div class="filter-bar">
			<div class="row align-items-center">
				<div class="col-xxl-2 col-lg-1 d-lg-block d-none">
					<div class="sec-img">
						<img src="images/XHALE-SECOND-LOGO.png" class="img-fluid">
					</div>
				</div>
				<div class="col-xxl-6 col-lg-7">
					<div class="filter-tab">
						<div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
							<button class="nav-link active" id="nav-one-tab" data-bs-toggle="tab" data-bs-target="#nav-one" type="button" role="tab" aria-controls="nav-one" aria-selected="true">
								<p class="mb-0">CASH GIVEAWAYS</p>
							</button>
							<button class="nav-link" id="nav-two-tab" data-bs-toggle="tab" data-bs-target="#nav-two" type="button" role="tab" aria-controls="nav-two" aria-selected="false">
								<p class="mb-0">PRIZE GIVEAWAYS</p>
							</button>
							<button class="nav-link" id="nav-three-tab" data-bs-toggle="tab" data-bs-target="#nav-three" type="button" role="tab" aria-controls="nav-three" aria-selected="false">
								<p class="mb-0">EVENTS</p>
							</button>
						</div>
					</div>
				</div>
				<div class="col-lg-4 d-lg-block d-none">
					<div class="filter-search">
						<form class="d-flex align-items-center" role="search">
							<input class="form-control" name="s" type="search" value="{{ $searchQuery }}" placeholder="Search for events here.." aria-label="Search">
							<button class="ms-3" type="submit"><img src="images/search-icon.svg" class="img-fluid"></button>
						</form>
					</div>
				</div>                        
			</div>
		</div>

		<div class="tab-content-wrapper mt-0">
			<div class="tab-content" id="nav-tabContent">
				<div class="tab-pane fade show active" id="nav-one" role="tabpanel" aria-labelledby="nav-one-tab" tabindex="0">
					<div id="cash-events-container">
						@if ($cash_events->isEmpty()) 
						<p>No events available for cash giveaways.</p>
						@else
						@include('consumer.partials.giveaway-events-list', ['events' => $cash_events,'title'=> 'CASH GIVEAWAYS','event_category' => 1])
						@endif
					</div>

				</div>

				<div class="tab-pane fade" id="nav-two" role="tabpanel" aria-labelledby="nav-two-tab" tabindex="0">

					<div id="prize-events-container">
						@if ($prize_events->isEmpty()) 
						<p>No events available for prize giveaways.</p>
						@else
						@include('consumer.partials.giveaway-events-list', ['events' => $prize_events,'title'=> 'PRIZE GIVEAWAYS','event_category' => 2])
						@endif
					</div>

				</div>

				<div class="tab-pane fade" id="nav-three" role="tabpanel" aria-labelledby="nav-three-tab" tabindex="0">
					<div id="charity-events-container">
						@if ($charity_events->isEmpty()) 
						<p>No events available.</p>
						@else
						@include('consumer.partials.events-list', ['events' => $charity_events,'title'=> 'EVENTS','event_category' => 3])
						@endif

					</div>

				</div>
			</div>
		</div>
	</div>  
</section>




@include('consumer.blocks.social-feeds')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script>
	$(document).ready(function() {
		const perPage = {{ $perPage }}; 
		const searchQuery = {!! json_encode($searchQuery) !!};

		$('.load-more').on('click', function() {
			const $clickedButton = $(this);
			let currentOffset = parseInt($clickedButton.data('offset') || 0);
			const category = $clickedButton.data('category');
			const appenddiv = $clickedButton.data('appenddiv');
			$.ajax({
				url: '{{ route('events') }}', 
				method: 'GET',
				headers: {
					'X-Requested-With': 'XMLHttpRequest', 
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					offset: currentOffset,
					category_id: category,
					s: searchQuery
				},
				success: function(response) {

					const $tempDiv = $('<div>').html(response.html);
					if (response.monthYearsArray && response.monthYearsArray.length > 0) {
						response.monthYearsArray.forEach(function(monthYearClass) {
							if ($('#' + appenddiv).has('.' + monthYearClass).length) {
								$tempDiv.find('.' + monthYearClass).remove();
							}
						});
					}
					$('#' + appenddiv).append($tempDiv.html());
					currentOffset += perPage;
					$clickedButton.data('offset', currentOffset);
					if (!response.hasMore) {
						$clickedButton.hide();
					}
				},
				error: function(xhr, status, error) {
					console.error('Error loading events:', error);
				}
			});
		});
	});
</script>
@endsection




