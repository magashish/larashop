@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"> {{ __('Unregistered Users') }} </div>
                <div class="card-body">

                    {{-- Success message --}}
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> 
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- Search form --}}
                    <!-- <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('unregisteredusers.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, or mobile" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                        </div>
                    </div> -->

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Follow-up Step</th>
                                    <th>Next Follow-up At</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
                                    <td>{{ $user->name ?? $user->email }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->mobile ?? '—' }}</td>
                                    <td>
                                        @if($user->followup_step == 4)
                                        Complete
                                        @else
                                        {{ $user->followup_step ?? 1 }}
                                        @endif
                                    </td>
                                    <td>{{ $user->next_followup_at?->format('d M Y H:i') ?? '—' }}</td>
                                    <td>{{ $user->created_at?->format('d M Y') }}</td>
                                    <td>
                                     
                                        <form action="{{ route('unregisteredusers.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No unregistered users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
