@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card shadow">
				<div class="card-header fw-bold">
					{{ __('Manage Events') }}
				</div>
				<div class="card-body">
					@if (session('status'))
					<div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
						<i class="bi bi-check-circle-fill me-2"></i> 
						{{ session('status') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
					@endif
					<div class="mb-3 text-end">
						<a href="{{ route('events.create') }}" class="btn btn-primary">
							<i class="bi bi-plus-circle"></i> Create event
						</a>
					</div>
					<div class="table-responsive">
						<table class="table  align-middle custom-table">
							<thead class="table-light">
								<tr>
									<th>Name</th>
									<th>Start Date</th>
									<th>End Date</th>  
									<th>Description</th>
									<th>Image</th>
									<th class="text-end">Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($events as $event)
								<tr data-id="{{ $event->id }}">
									<td> {{ $event->name }}</td>
									<td> {{ Carbon\Carbon::parse($event->start)->format('F d Y H:i:s') }}</td>
									<td>
										@if($event->end)
										{{ Carbon\Carbon::parse($event->end)->format('F d Y H:i:s') }}
										@else
										N/A
										@endif
									</td>
									<td>
										@if (!empty($event->description))
										<i class="bi bi-check text-success"></i>
										@else
										<i class="bi bi-x text-danger"></i> 
										@endif
									</td>
									<td>
										@if ($event->image)
										<img src="{{ Storage::url($event->image) }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
										@else
										N/A
										@endif
									</td>
									<td class="text-end">
										<a href="{{ route('events.edit', $event->id) }}" class="btn  btn-outline-dark custom-action-btn me-2">
											<i class="bi bi-pencil"></i> Edit</a>
											<form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline-block;">
												@csrf @method('DELETE')
												<button type="submit" class="btn  btn-outline-dark custom-action-btn"  onclick="return confirm('Delete this event?')">
													<i class="bi bi-trash"></i> Delete
												</button>

											</form>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div> 
				</div> 
			</div>
		</div>
	</div>
	@endsection




