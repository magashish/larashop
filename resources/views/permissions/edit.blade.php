@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Permissions / Edit') }}</div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <a href="{{ route('permissions.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Permission Admin
                        </a>
                    </div>

                    <form method="POST" action="{{ route('permissions.update', $permission) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ $permission->name }}" required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">Group Name</label>
                            <input type="text" class="form-control @error('group') is-invalid @enderror"
                                id="group" name="group" value="{{ $permission->group }}" required>

                            @error('group')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Edit Permission
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
