@extends('layouts.businessapp') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('View Offers for Organisation') }} "{{ $organisation->title}}"</div> 
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

                    </div>

                    {{-- Pending Approval Section --}}
                    @if($offer_new->isNotEmpty())
                    <div class="mb-5">
                        <h5 class="mb-3 fw-bold">Pending Approval </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_new,'class' => 'warning'])
                    </div>
                    @endif

                    {{-- Expired Section --}}
                    @if($offer_expire->isNotEmpty())
                    <div class="mb-5">
                        <h5 class="mb-3 fw-bold">Expired </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_expire,'class' => 'danger'])
                    </div>
                    @endif

                    {{-- Live Section --}}
                    @if($live_offers->isNotEmpty())
                    <div class="mb-5">
                        <h5 class="mb-3 fw-bold">Live </h5>
                        @include('business.offers.table_partial', ['offers' => $live_offers,'class' => 'success'])
                    </div>
                    @endif

                    {{-- Rejected Section --}}
                    @if($offer_rejected->isNotEmpty())
                    <div class="mb-3"> {{-- Adjusted margin-bottom for the last section --}}
                        <h5 class="mb-3 fw-bold">Rejected </h5>
                        @include('business.offers.table_partial', ['offers' => $offer_rejected,'class' => 'danger'])
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
