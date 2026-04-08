@extends('layouts.app') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Import Offers via CSV') }}</div> 
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if ($errors->any()) 
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif


                    <form action="{{ route('offers.uploadcsvaction') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                     <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold">Select CSV file to upload: </label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv">
                        @error('csv_file') {{-- Add validation error display here --}}
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Next: Field Mapping
                    </button>
                </form>


            </div>
        </div>
    </div>
</div>
</div>
@endsection
