@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('View Offers for Business') }} {{ $organisation->title}}</div> 
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



                    {{-- New Offers Section (Pending Approval) --}}
                    @if ($offer_new->count() > 0) {{-- Check if count is greater than 0 (i.e., has items) --}}
                    <div class="mb-5">
                        <h5 class="mb-3">Pending Approval ({{ $offer_new->count() }})</h5>
                        @include('business.offers.table_partial', ['offers' => $offer_new])
                    </div>
                    @endif

                    {{-- Approved Offers Section --}}
                    {{-- Apply the same logic here if you want to hide it when count is 0 --}}
                    @if ($offer_approved->count() > 0)
                    <div class="mb-5">
                        <h5 class="mb-3">Approved ({{ $offer_approved->count() }})</h5>
                        @include('business.offers.table_partial', ['offers' => $offer_approved])
                    </div>
                    @endif

                    {{-- Rejected Offers Section --}}
                    {{-- And here --}}
                    @if ($offer_rejected->count() > 0)
                    <div class="mb-3">
                        <h5 class="mb-3">Rejected ({{ $offer_rejected->count() }})</h5>
                        @include('business.offers.table_partial', ['offers' => $offer_rejected])
                    </div>
                    @endif






                </div>
            </div>
        </div>
    </div>
</div>
@endsection
