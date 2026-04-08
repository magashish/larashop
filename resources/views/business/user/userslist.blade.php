@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"> {{ __('Manage Users') }} </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> 
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('business.users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add a User
                        </a>
                    </div>

                   

                    <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input
                                        type="text"
                                        name="email"
                                        class="form-control"
                                        placeholder="Search Email address"
                                        value="{{ request('email') }}"
                                        >
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>


                    




                    <div class="table-responsive">
                        <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Name </th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Created At</th>
                                    <th>User status</th>
                                    <th>User Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                 <td>
                                    @if($user->first_name || $user->last_name)
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    @else
                                    {{ $user->email }}
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $user->status }}</td>
                                <td>{{ $user->level }}</td>
                                <td>
                                    <a href="{{ route('business.users.edit', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('business.users.destroy', $user->id) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Are you sure?');">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form> 
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-dark pagination-sm">

                        {{ $users->links('pagination::bootstrap-4') }}
                    </ul>
                </nav>
            </div>
            @endif

        </div>  
    </div> 
</div>
</div>
</div>
@endsection
