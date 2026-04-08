@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Admin Users') }}
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
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create User
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                       {{--  <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Are you sure?');">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form> --}}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>  
        </div> 
    </div>
</div>
</div>
@endsection
