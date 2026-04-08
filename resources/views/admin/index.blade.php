@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Manage Admins Users') }}
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
                        <a href="{{ route('admins.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Admin
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Email / Username</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Latest Action</th>
                                    <th class="text-end" colspan="2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($processedUsers as $user)
                                <tr>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->status }}</td>
                                    <td>{{ $user->last_action_formatted }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admins.edit', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="{{ route('admin.logs.show', ['admins',$user->id]) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                            <i class="bi bi-clipboard-data"></i> Log
                                        </a>
                                        <a href="{{ route('admin.user_daily_actions', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                            <i class="bi bi-clock-history"></i> First-Last
                                        </a>
                                       {{--  <form action="{{ route('admins.destroy', $user->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Are you sure?');">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
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
