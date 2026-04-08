@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card shadow">
				<div class="card-header fw-bold">
					{{ __('Manage Categories') }}
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
						<a href="{{ route('categories.create') }}" class="btn btn-primary">
							<i class="bi bi-plus-circle"></i> Create Category
						</a>
					</div>
					<div class="table-responsive">
						<table class="table  align-middle custom-table">
							<thead class="table-light">
								<tr>
									<th>Name</th>
									<th>Description</th>
									<th>Icon</th>
									<th colspan="2">Number of Offers</th>
									<th class="text-end">Actions</th>
								</tr>
							</thead>
							<tbody id="catsortable">
								@foreach($categories as $category)
								<tr data-id="{{ $category->id }}">
									<td><i class="bi bi-arrows-move me-2 handle" style="cursor: move;"></i> {{ $category->name }}</td>
									<td>
										@if (!empty($category->description))
										<i class="bi bi-check text-success"></i>
										@else
										<i class="bi bi-x text-danger"></i> 
										@endif
									</td>
									<td>
										@if ($category->icon)
										<img src="{{ Storage::url($category->icon) }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
										@else
										N/A
										@endif
									</td>

									<td>{{ $category->offers()->count();}}</td>
									<td><a href="{{ route('categories.show', $category->id) }}" class="btn  btn-outline-dark custom-action-btn me-2">
											<i class="bi bi-list-columns-reverse"></i>  View</a></td>
									<td class="text-end">
										<a href="{{ route('categories.edit', $category->id) }}" class="btn  btn-outline-dark custom-action-btn me-2">
											<i class="bi bi-pencil"></i> Edit</a>
											<form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
												@csrf @method('DELETE')
												<button type="submit" class="btn  btn-outline-dark custom-action-btn"  onclick="return confirm('Delete this category?')">
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




