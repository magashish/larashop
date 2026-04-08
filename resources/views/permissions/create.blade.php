@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Permissions / Create') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('permissions.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Permission Admin
                        </a>
                    </div>

                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Permission Name</label>
                            <input type="text" name="name" id="name"
                            placeholder="Enter Permission Name"
                            class="form-control @error('name') is-invalid @enderror">

                            @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label fw-semibold">Group Name</label>
                            <input type="text" name="group" id="group"
                            placeholder="Enter Group Name"
                            class="form-control @error('group') is-invalid @enderror">

                            @error('group')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Permission
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
