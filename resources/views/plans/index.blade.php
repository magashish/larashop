@extends('layouts.app') {{-- Adjust this to your main layout file --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Subscription Plans') }}</div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('plans.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Subscription Plan
                        </a>
                    </div>

                    {{-- 🔹 Tabs Navigation --}}
                    <ul class="nav nav-pills nav-fill mb-3" id="plansTab" role="tablist">
                        {{-- All Plans --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-plans" type="button" role="tab" aria-controls="all-plans" aria-selected="true">
                                <i class="bi bi-collection me-1"></i> All Plans
                            </button>
                        </li>

                        {{-- Package --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="package-tab" data-bs-toggle="tab" data-bs-target="#package-plans" type="button" role="tab" aria-controls="package-plans" aria-selected="false">
                                <i class="bi bi-box-seam me-1"></i> Package
                            </button>
                        </li>

                        {{-- Subscription --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription-plans" type="button" role="tab" aria-controls="subscription-plans" aria-selected="false">
                                <i class="bi bi-credit-card-2-front me-1"></i> Subscription
                            </button>
                        </li>
                    </ul>

                    {{-- 🔹 Tabs Content --}}
                    <div class="tab-content" id="plansTabContent">
                        <div class="tab-pane fade show active" id="all-plans" role="tabpanel" aria-labelledby="all-tab">
                           
                            @if($allPlans->isNotEmpty())
                                <div class="mb-3">
                                    @include('plans.sortable_table_partial', ['plans' => $allPlans])
                                </div>
                            @else
                                <div class="alert alert-info">No plans available.</div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="package-plans" role="tabpanel" aria-labelledby="package-tab">
                            @if($package->isNotEmpty())
                                <div class="mb-3">
                                    @include('plans.table_partial', ['plans' => $package])
                                </div>
                            @else
                                <div class="alert alert-info">No package plans available.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="subscription-plans" role="tabpanel" aria-labelledby="subscription-tab">
                            @if($subscription->isNotEmpty())
                                <div class="mb-3">
                                    @include('plans.table_partial', ['plans' => $subscription])
                                </div>
                            @else
                                <div class="alert alert-info">No subscription plans available.</div>
                            @endif
                        </div>
                    </div> {{-- end tab-content --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
