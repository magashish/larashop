@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Permissions / List') }}
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
                        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Permission
                        </a>
                    </div>
                    <div class="table-responsive">
                         <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Permission Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('permissions.edit', $permission->id) }}" 
                                           class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                           <i class="bi bi-pencil"></i> Edit
                                       </a>

                                       <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" 
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
                                <td colspan="2" class="text-center text-muted">No permissions found.</td>
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
