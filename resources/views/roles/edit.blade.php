@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Role / Edit') }}</div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Role Admin
                        </a>
                    </div>

                    <form method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" id="name" name="name" value="{{ $role->name }}"
                            class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guard_name" class="form-label fw-semibold">For Which Side </label>
                            <select class="form-select @error('guard_name') is-invalid @enderror" id="guard_name" name="guard_name" required>
                                <option value="admin" {{ old('guard_name', $role->guard_name ?? '') == 'admin' ? 'selected' : '' }}>Admin Side</option>
                                <option value="web" {{ old('guard_name', $role->guard_name ?? '') == 'web' ? 'selected' : '' }}>Web Side</option>
                            </select>
                            @error('guard_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- <div class="mb-3">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2">Permissions</legend>
                                <div class="d-flex flex-column gap-2">
                                    @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                        {{ $hasPermissions->contains($permission->name) ? 'checked' : '' }}>
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
                                <i class="bi bi-save"></i> Edit Role
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

