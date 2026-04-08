@extends('layouts.app') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('CSV Field Mapping') }}</div> 
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif






                    <form action="{{ route('offers.import_csv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="organisation_id" class="form-label fw-semibold">Select Organisation: </label>
                            <select class="form-select @error('organisation_id') is-invalid @enderror" id="organisation_id" name="organisation_id" required>
                                @foreach ($organisations as $id => $name)
                                <option value="{{ $id }}" {{ old('organisation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('organisation_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_ids" class="form-label fw-semibold">Select Category:</label>
                            <select class="form-control select2 @error('category_ids') is-invalid @enderror" id="category_ids" name="category_ids[]" multiple="multiple">
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_ids')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image_files" class="form-label fw-semibold">Fallback Image (if no image or ratio too extreme):</label>
                            <input type="file" name="image_files" id="image_files" class="form-control @error('image_files') is-invalid @enderror @error('image_files') is-invalid @enderror" >
                            <small class="text-muted">Optional. If an offer lacks an image or has an extreme aspect ratio, we'll use this fallback.</small>
                            @error('image_files')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('image_files.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <h5 class="card-title mb-3">Map CSV Columns to Offer Fields</h5>
                            <p class="mb-4">Select which column from your CSV file corresponds to each system field below.</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table  align-middle custom-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>System Field</th>
                                        <th>Maps to CSV Column</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($systemFields as $dbColumn => $label)
                                    <tr>
                                        <td class="fw-bold">{{ $label }}</td>
                                        <td>
                                            <select name="mapping[{{ $dbColumn }}]" class="form-select csv-column-select" data-db-column="{{ $dbColumn }}">
                                                <option value="">-- Select CSV Column --</option>
                                                @foreach ($actualCsvHeaders as $csvHeader)
                                                <option value="{{ $csvHeader }}"
                                                {{ old('mapping.' . $dbColumn, str_replace([' ', '_', '-'], '', strtolower($csvHeader)) === str_replace('_', '', strtolower($dbColumn)) ? $csvHeader : '') == $csvHeader ? 'selected' : '' }}>
                                                
                                                {{ $csvHeader }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('offers.uploadcsv') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Upload
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
</div>
@endsection
