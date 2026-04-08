@extends('layouts.app') {{-- Or your actual layout file --}}
@section('content')


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Manage Businesses') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif



                    @can('create organisations')
                    <div class="card mb-4">
                        <div class="card-header fw-bold"> Add Multiple Businesses via CSV </div>
                        <div class="card-body d-flex justify-content-between">
                            <a href="{{ route('organisations.organisationsuploadcsv') }}" class="btn btn-dark text-white">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Upload Businesses CSV
                            </a>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <a href="{{ route('organisations.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Organisation
                        </a>
                    </div>
                    @endcan


                     <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input
                                        type="text"
                                        name="business"
                                        id="business"
                                        class="form-control"
                                        placeholder="Search Businesses Here"
                                        value="{{ request('business') }}"
                                        >
                                        <label for="business">Search Businesses Here</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    

                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-pills nav-fill mb-3" id="organisationsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-organisations" type="button" role="tab" aria-controls="pending-organisations" aria-selected="true">
                                <i class="bi bi-clock-history me-1"></i> Pending Approval
                                <span class="badge rounded-pill bg-warning text-dark ms-1">
                                    {{ count($organisations_new) }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved-organisations" type="button" role="tab" aria-controls="approved-organisations" aria-selected="false">
                                <i class="bi bi-check-circle me-1"></i> Approved
                                <span class="badge rounded-pill bg-success ms-1">
                                    {{ count($organisations_approved) }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected-organisations" type="button" role="tab" aria-controls="rejected-organisations" aria-selected="false">
                                <i class="bi bi-x-circle me-1"></i> Rejected
                                <span class="badge rounded-pill bg-danger ms-1">
                                    {{ count($organisations_rejected) }}
                                </span>
                            </button>
                        </li>
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="organisationsTabContent">
                        <div class="tab-pane fade show active" id="pending-organisations" role="tabpanel" aria-labelledby="pending-tab">
                            @include('organisation.table_partial', ['organisations' => $organisations_new,'class' => 'warning' ])
                        </div>
                        <div class="tab-pane fade" id="approved-organisations" role="tabpanel" aria-labelledby="approved-tab">
                            @include('organisation.table_partial', ['organisations' => $organisations_approved,'class' => 'success'])
                        </div>
                        <div class="tab-pane fade" id="rejected-organisations" role="tabpanel" aria-labelledby="rejected-tab">
                            @include('organisation.table_partial', ['organisations' => $organisations_rejected,'class' => 'danger'])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection