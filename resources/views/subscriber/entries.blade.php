@extends('layouts.subscriber')

@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Your Prize Draw Entries</h2>
                    </div>
                    <div class="card-body">
                        @if($userEntries->isEmpty())
                            <div class="alert alert-info" role="alert">
                                You have not entered any prize draws yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Entry ID</th>
                                            <th scope="col">Package & Subscription</th>
                                            <th scope="col">Entry Type</th>
                                            <th scope="col">Plan Name</th>
                                            <th scope="col">Entered At</th>
                                            <th scope="col">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($userEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->id }}</td>
                                                <td>{{ $entry->package_subscriptions_id }}</td>
                                                <td style="text-transform: capitalize;">{{ $entry->type }}</td>
                                                <td>
                                                    @if($entry->packageSubscription && $entry->packageSubscription->plan)
                                                        {{ $entry->packageSubscription->plan->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $entry->created_at->format('M d, Y H:i A') }}</td>
                                                <td>{{ $entry->description }}</td>
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
</div>
@endsection