@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Licenses / List') }}
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
                        <a href="{{ route('licenses.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add a License
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                 <th>Title</th>
                                 <th>Quantity</th>
                                 <th>Price</th>
                                 <th>Type</th>
                                 <th>Active</th>
                                 <th>Public</th>
                                 <th class="text-end">Actions</th>
                             </tr>
                         </thead>
                         <tbody>
                            @foreach ($licenses as $license)
                            <tr>
                                <td>{{ $license->title }}</td>
                                <td>{{ $license->quantity }}</td>
                                <td>${{ $license->price }}</td>
                                <td>{{ ucfirst($license->license_type) }}</td>
                                <td>{{ $license->is_active ? 'Yes' : 'No' }}</td>
                                <td>{{ $license->is_public ? 'Yes' : 'No' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('licenses.edit', $license->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                                    <form action="{{ route('licenses.destroy', $license->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center">
                        {{ $licenses->links() }}
                    </div>

                </div>
            </div> 


        </div> 
    </div>
</div>
</div>
@endsection
