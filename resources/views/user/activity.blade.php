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
                    {{ __('Activity Logs') }}
                </div>
                <div class="card-body">

                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="tab-content mt-3">
                        @if ($logs->isEmpty())
                        <div class="alert alert-info">No activity logs found.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Message</th>
                                        <th>Meta</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $index => $log)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $log->event)) }}</td>
                                        <td>{{ $log->message }}</td>
                                        <td>
                                            @if($log->meta)
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#meta-{{ $index }}" aria-expanded="false" aria-controls="meta-{{ $index }}">
                                                View Meta
                                            </button>
                                            <div class="collapse mt-2" id="meta-{{ $index }}">
                                                <pre class="text-xs bg-light p-2 rounded">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-dark pagination-sm">
                                    {{ $logs->appends(request()->input())->links('pagination::bootstrap-4') }}
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
