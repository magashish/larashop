@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Events / Create') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Events Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold"> Name</label>
                            <input type="text" name="name" id="name"
                            placeholder="Enter Name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description"
                            placeholder="Enter Description"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

                            @error('description')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Start Date & Time --}}
                        <div class="mb-3">
                            <label for="start" class="form-label fw-semibold">Start Date & Time</label>
                            <input type="text" name="start" id="start"
                            class="form-control datetimepicker @error('start') is-invalid @enderror"
                            value="{{ old('start') }}" required>
                            @error('start')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- End Date & Time --}}
                        <div class="mb-3">
                            <label for="end" class="form-label fw-semibold">End Date & Time</label>
                            <input type="text" name="end" id="end"
                            class="form-control datetimepicker @error('end') is-invalid @enderror"
                            value="{{ old('end') }}">
                            @error('end')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- All Day Checkbox --}}
                       <!--  <div class="mb-3 form-check">
                            <input type="hidden" name="all_day" value="0">
                            <input type="checkbox" name="all_day" id="all_day"
                            class="form-check-input @error('all_day') is-invalid @enderror"
                            value="1" {{ old('all_day') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="all_day">All day</label>
                            @error('all_day')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div> -->

                        {{-- Location --}}
                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" id="address_search_input"
                            placeholder="Enter Location"
                            class="form-control @error('location') is-invalid @enderror"
                            value="{{ old('location') }}">
                            @error('location')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="event_category" class="form-label fw-semibold">Category</label>
                            <select class="form-select @error('event_category') is-invalid @enderror" id="event_category" name="event_category" required>
                                {{-- Assuming events_category() is a function that returns the categories --}}
                                @foreach (events_category() as $value => $label)
                                <option value="{{ $value }}" {{ old('event_category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('event_category')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="facebook_url" class="form-label fw-semibold">Facebook Live URL</label>
                            <input type="url" name="facebook_url" id="facebook_url"
                            placeholder="Enter Facebook Live URL"
                            class="form-control @error('facebook_url') is-invalid @enderror"
                            value="{{ old('facebook_url') }}">
                            @error('facebook_url')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                         <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Event Image</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                          </div>



                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Event
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
