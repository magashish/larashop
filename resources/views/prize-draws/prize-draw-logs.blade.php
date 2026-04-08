@extends('layouts.app') {{-- Adjust this to your main layout file, e.g., 'layouts.admin' for admin panel --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Prize Winner Logs') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

         

                    <div class="table-responsive">


                        <table class="table custom-table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Message</th>
                                    <th>Prize</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($logs as $log)
                                <tr>
                                    <td>
                                        {{ $log->first_name }} {{ $log->last_name }}
                                        <br>
                                        <small>{{ $log->email }}</small>
                                    </td>
                                    <td>{{ $log->message }}</td>
                                    <td>
                                        @php
                                        $meta = json_decode($log->meta,true);
                                        @endphp
                                        {{ $meta['prize_name'] ?? '' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('F d, Y h:i A') }}
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No prize logs found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
