@extends('layouts.app') {{-- Adjust this to your main layout file, e.g., 'layouts.admin' for admin panel --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Prize Draw Entry / Create') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    
                    {{-- Form for creating a new prize draw entry --}}
                    <form action="{{ route('entries.store') }}" method="POST">
                        @csrf
                    
                        {{-- User ID Field (dropdown populated with users) --}}
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold">User</label>
                            <select class="form-control userselect2 @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Select a User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    


                        {{-- Description Field --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                   

                      

                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Prize Draw Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* You can remove this style block if you are using a consistent CSS framework like Bootstrap */
.invalid-feedback.d-block { display: block; }
</style>