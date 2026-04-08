@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1>Transaction History</h1>

            {{-- One-Time Payments Table --}}
            <div class="card my-4">
                <div class="card-header">
                    <h2>One-Time Payments</h2>
                </div>
                <div class="card-body">
                    @if($oneTimeInvoices->isEmpty())
                        <p>You have no one-time payments.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($oneTimeInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                                        <td>{{ $invoice->total() }}</td>
                                        <td>{{ $invoice->id }}</td>
                                        <td>
                                            <a href="{{ route('invoice.download', $invoice->id) }}" class="btn btn-primary btn-sm">Download PDF</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Subscription Invoices Table --}}
            <div class="card">
                <div class="card-header">
                    <h2>Subscription Invoices</h2>
                </div>
                <div class="card-body">
                    @if($subscriptionInvoices->isEmpty())
                        <p>You have no recurring subscription invoices.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Subscription ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptionInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                                        <td>{{ $invoice->total() }}</td>
                                        <td>{{ $invoice->subscription }}</td>
                                        <td>
                                            <a href="{{ route('invoice.download', $invoice->id) }}" class="btn btn-primary btn-sm">Download PDF</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection