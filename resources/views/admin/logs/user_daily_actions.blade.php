@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ $admin->name }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> 
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Admin Users Admin
                        </a>
                    </div>


                    <div class="mb-3">
                        <h2 class="fw-bold"> {{ $admin->name }}'s first and last action timestamps for last 30 days</h2>
                    </div>



                    <div class="table-responsive">
                        <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Day</th>
                                    <th>First</th>
                                    <th>Last</th>
                                    <th class="text-end">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($logData) > 0)
                                @foreach ($logData as $log)
                                <tr>
                                    <td>{{ $log['day'] }}</td>
                                    <td>{{ $log['first_action_time'] }}</td>
                                    <td>{{ $log['last_action_time'] }}</td>
                                    <td class="text-end">{{ $log['duration'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" align="center"><em>No activity logged!</em></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
@endsection