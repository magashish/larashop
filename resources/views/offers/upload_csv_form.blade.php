@extends('layouts.app') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Upload Offers CSV') }}</div> 
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('offers.uploadcsvaction') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csv_file" class="form-label fw-semibold">Select CSV file to upload: </label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv,.txt">
                            <div class="form-text">Please upload a CSV  file.</div>
                            @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-right"></i> Next: Field Mapping
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
