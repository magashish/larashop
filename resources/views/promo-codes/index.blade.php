@extends('layouts.app') {{-- Adjust this to your main layout file, e.g., 'layouts.admin' for admin panel --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Manage Promo Codes') }}</div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('promo_codes.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Promo Code
                        </a>
                    </div>

                    @if ($distinctInfluencerNames->isEmpty())
                    <div class="alert alert-info" role="alert">
                        No promo codes found or no influencers assigned yet.
                    </div>
                    @else
                    @foreach ($distinctInfluencerNames as $influencerRecord)
                    @php
                    $influencerName = $influencerRecord->influencer;
                    $codesForThisInfluencer = $promoCodesByInfluencer->get($influencerName, collect());
                    @endphp

                    <div class="card mb-4 custom-table-card">
                        <div class="card-header fw-bold">{{ $influencerName ?: 'Unassigned Influencer' }}</div>

                        <div class="card-body p-0">
                            @if ($codesForThisInfluencer->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table custom-table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Promo Code</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Subscription Payment Status </th>
                                            <th>Promo Code Payment Status   </th>
                                            <th>Number of Orders    </th>
                                            <th>Total</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($codesForThisInfluencer as $promoCode)
                                        <tr>
                                            <td>{{ $promoCode->promo_code }}</td>
                                            <td>{{ \Carbon\Carbon::parse($promoCode->start_date)->format('Y-m-d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($promoCode->end_date)->format('Y-m-d') }}</td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td class="text-end">
                                                <a href="{{ route('promo_codes.edit', $promoCode->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('promo_codes.destroy', $promoCode->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn"
                                                    onclick="return confirm('Are you sure you want to delete this promo code?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="p-3 text-muted">No promo codes found for this influencer on this page.</p>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif

            </div>
        </div>
    </div>
</div>
</div>
@endsection
