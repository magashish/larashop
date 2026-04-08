@extends('layouts.app')
@section('content')


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Manage Offers') }}</div>

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


                 @can('create offers')
                    <div class="card mb-4">
                        <div class="card-header fw-bold"> Add Multiple Offers via CSV </div>
                        <div class="card-body">
                            <a href="{{ route('offers.uploadcsv') }}" class="btn btn-dark text-white">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Upload CSV
                            </a>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <a href="{{ route('offers.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add New Offer
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


                    <div>
                        <ul class="nav nav-pills nav-fill mb-3" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                    <i class="bi bi-clock-history me-1"></i> Pending Approval
                                    <span class="badge rounded-pill bg-warning text-dark ms-1">
                                        {{ count($offer_new) }}
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button" role="tab" aria-controls="expired" aria-selected="false">
                                    <i class="bi bi-calendar-x me-1"></i> Expired
                                    <span class="badge rounded-pill bg-danger ms-1">
                                        {{ count($offer_expire) }}
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button" role="tab" aria-controls="live" aria-selected="false">
                                    <i class="bi bi-check-circle me-1"></i> Live
                                    <span class="badge rounded-pill bg-success ms-1">
                                        {{ count($live_offers) }}
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                    <i class="bi bi-x-circle me-1"></i> Rejected
                                    <span class="badge rounded-pill bg-secondary ms-1">
                                        {{ count($offer_rejected) }}
                                    </span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                @include('offers.table_partial', ['offers' => $offer_new ,'class' => 'warning' ])
                            </div>
                            <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
                                @include('offers.table_partial', ['offers' => $offer_expire ,'class' => 'expired' ])
                            </div>
                            <div class="tab-pane fade" id="live" role="tabpanel" aria-labelledby="live-tab">
                                @include('offers.table_partial', ['offers' => $live_offers ,'class' => 'success' ])
                            </div>
                            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                                @include('offers.table_partial', ['offers' => $offer_rejected ,'class' => 'danger' ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection