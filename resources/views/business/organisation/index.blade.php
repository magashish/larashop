@extends('layouts.businessapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Organisations / List') }}</div>
                <div class="card-body">
                    {{-- Stats Dashboard Section --}}
                    <div class="row mb-4"> {{-- Added mb-4 for bottom margin --}}
                     
                     <!-- @include('business.organisation.stats-dashboard') -->

                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif



                 @if (Auth::user()->isBusinessAccount())
                    <div class="mb-3 text-end">
                        <a href="{{ route('business.organisations.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i> Create  Business
                        </a>
                    </div>

                     @endif

                    {{-- New Organisations Section --}}
                    <div class="mb-5">
                        <h5 class="mb-3">Pending Approval</h5>
                        @include('business.organisation.table_partial', ['organisations' => $organisations_new,'class' => 'warning' ])
                    </div>

                    {{-- Approved Organisations Section --}}
                    <div class="mb-5">
                        <h5 class="mb-3">Approved </h5>
                        @include('business.organisation.table_partial', ['organisations' => $organisations_approved,'class' => 'success'])
                    </div>

                    {{-- Rejected Organisations Section --}}
                    <div class="mb-3">
                        <h5 class="mb-3">Rejected</h5>
                        @include('business.organisation.table_partial', ['organisations' => $organisations_rejected,'class' => 'danger'])
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection