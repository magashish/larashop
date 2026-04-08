@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Role / Create') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Role Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="guard_name" class="form-label fw-semibold">For Which Side </label>
                            <select class="form-select @error('guard_name') is-invalid @enderror" id="guard_name" name="guard_name" required>
                                <option value="admin">Admin Side</option>
                                <option value="web">Wsb Side</option>
                            </select>
                            @error('guard_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>




                        {{-- <div class="mb-3">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2">Permissions</legend>
                                <div class="d-flex flex-column">
                                    @foreach ($permissions as $permission)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div> --}}

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add a Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
