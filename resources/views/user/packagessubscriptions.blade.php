@extends('layouts.app') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                
                <div class="card-header fw-bold">
                    @if($user->first_name || $user->last_name)
                        {{ $user->first_name }} {{ $user->last_name }}
                    @else
                        {{ $user->email }}
                    @endif 
                    {{ __(' - Packages & Subscriptions') }}
                </div>

                <div class="card-body">

                    {{-- Success Message --}}
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- TABLE DISPLAY --}}
                    @if($allRecords->isEmpty())
                        <div class="alert alert-info">No package or subscription records found.</div>
                    @else
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Id</th>
                                        <th>Type</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Started</th>
                                        <th>Ends</th>
                                        <th>Stripe ID</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($allRecords as $record)
                                    <tr>
                                        <td>{{ $record->id }}</td>
                                        <td class="fw-bold text-uppercase">
                                            {{ $record->type ?? 'subscription' }}
                                        </td>

                                        <td>
                                            {{ $record->plan->name ?? ($record->name ?? 'N/A') }}
                                        </td>

                                        <td>
                                            @php
                                                $status = $record->status ?? $record->stripe_status ?? 'N/A';
                                                $badgeClass = $status == 'active' ? 'bg-success' : 'bg-danger';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                        </td>

                                        <td>{{ $record->starts_at ? $record->starts_at->format('d M Y') : '—' }}</td>

                                        <td>{{ $record->ends_at ? $record->ends_at->format('d M Y') : '—' }}</td>

                                        <td>{{ $record->stripe_id ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
