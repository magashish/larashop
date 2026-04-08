@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Offers / List') }}</div> 
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

                    <div class="mb-3 text-end">
                        <a href="{{ route('business.offers.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Create an Offer for your Business
                        </a>
                    </div>



                    {{-- New Organisations Section --}}
                    <div class="mb-5"> {{-- Added margin-bottom for separation --}}
                        <h5 class="mb-3 fw-bold">Pending Approval </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_new ,'class' => 'warning' ])
                    </div>

                    <div class="mb-5"> {{-- Added margin-bottom for separation --}}
                        <h5 class="mb-3 fw-bold">Expired </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_expire ,'class' => 'expired' ])
                    </div>

                    {{-- Approved Organisations Section --}}
                    <div class="mb-5"> {{-- Added margin-bottom for separation --}}
                        <h5 class="mb-3 fw-bold">Live </h5>
                        @include('business.offers.table_partial', ['offers' => $live_offers ,'class' => 'success' ])
                    </div>


                    {{-- Rejected Organisations Section --}}
                    <div class="mb-3"> {{-- Adjusted margin-bottom for the last section --}}
                        <h5 class="mb-3 fw-bold">Rejected </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_rejected ,'class' => 'danger' ])
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
