@extends('layouts.app')
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
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add a User
                        </a>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header fw-bold"> {{ __('Export Users') }} </div>
                        <div class="card-body">
                            <p>Click the button below to generate a CSV of all users.</p>
                             <a href="{{ route('users.export_csv', request()->query()) }}" class="btn btn-dark text-white"> 
                                <i class="bi bi-file-earmark-spreadsheet"></i> Generate CSV
                            </a>
                        </div>
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


                    <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                        {{-- All Users --}}
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index') }}" class="nav-link {{ !request()->hasAny(['sort_by', 'level' ,'live']) ? 'active' : '' }}">
                                <i class="bi bi-list-ul me-1"></i> All Users
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['live' => 'live']) }}" class="nav-link {{ request('live') == 'live' ? 'active' : '' }}">
                                <i class="bi bi-person-circle me-1"></i> Users with Active Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['live' => 'expire']) }}" class="nav-link {{ request('live') == 'expire' ? 'active' : '' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Users with Expired Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['live' => 'inactive']) }}" class="nav-link {{ request('live') == 'inactive' ? 'active' : '' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Users Without Active Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['live' => 'smssubscribe']) }}" class="nav-link {{ request('live') == 'smssubscribe' ? 'active' : '' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Sms subscribed
                            </a>
                        </li>

                        {{-- Active/Deactivated Sorting --}}
                        {{-- <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['sort_by' => 'active']) }}" class="nav-link {{ request('sort_by') == 'active' ? 'active' : '' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Active Users
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['sort_by' => 'deactivated']) }}" class="nav-link {{ request('sort_by') == 'deactivated' ? 'active' : '' }}">
                                <i class="bi bi-person-x-fill me-1"></i> Deactivated Users
                            </a>
                        </li>
 --}}
                        {{-- User Level Filtering --}}
                        {{-- <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['level' => 'member']) }}" class="nav-link {{ request('level') == 'member' ? 'active' : '' }}">
                                <i class="bi bi-person-circle me-1"></i> Subscribers Users
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('users.index', ['level' => 'business']) }}" class="nav-link {{ request('level') == 'business' ? 'active' : '' }}">
                                <i class="bi bi-building me-1"></i> Business Users
                            </a>
                        </li> --}}
                    </ul>




                    <div class="table-responsive">
                        <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    {{-- <th>ID </th> --}}
                                    <th>Name </th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    {{-- <th>Created At</th> --}}
                                    <th>User status</th>
                                    {{-- <th>User Level</th> --}}
                                    <th>Exclude From Prize Draw</th>
                                    <th>Sms Unsubscribed</th>
                                    <th>Two Factor Code</th>
                                    {{-- <th>Stripe ID</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    {{-- <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td> --}}
                                    <td>{{ 
                                        $user->name 
                                        ?: trim($user->first_name . ' ' . $user->last_name) 
                                        ?: $user->email 
                                    }}
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile }}</td>
                                {{-- <td>{{ $user->created_at?->format('Y-m-d') }}</td> --}}
                                <td>{{ $user->status }}</td>
                                {{-- <td>{{ $user->level }}</td> --}}
                                <td>
                                    @if ($user->exclude_from_prize_draw)
                                    <span class="badge bg-danger">Yes</span>
                                    @else
                                    <span class="badge bg-success">No</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($user->sms_unsubscribed)
                                    <span class="badge bg-danger">Yes</span>
                                    @else
                                    <span class="badge bg-success">No</span>
                                    @endif
                                </td>

                                <td>{{ $user->two_factor_code }}</td>
                                {{-- <td>{{ $user->stripe_id }}</td> --}}
                                <td>

                                    <a href="{{ route('userpackagessubscriptions', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Packages & Subscriptions
                                    </a>

                                    <a href="{{ route('userActivity', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Activity
                                    </a>

                                    <a href="{{ route('users.entries', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Entries ( {{ $user->main_entries_count }})
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.logs.show', ['users',$user->id]) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-clipboard-data"></i> Log
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
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

            {{-- 💡 1. PAGINATION LINKS ADDED HERE with custom styling --}}
                    @if ($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-dark pagination-sm">
                                    {{-- 
                                        Note: The `appends` logic was already handled in the controller
                                        using $users->paginate(...)->appends($request->query()). 
                                        We just call links() on the collection.
                                    --}}
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
