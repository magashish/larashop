@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">

                    {{-- HEADER --}}
                    <div class="alert alert-info d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bar-chart-fill fs-1 me-3"></i>
                            <div>
                                <strong>Sales Report</strong> &mdash;
                                {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
                            </div>
                        </div>

                    </div>

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <form method="GET">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold mb-1">
                                            <i class="bi bi-calendar3 me-1"></i> Period
                                        </label>
                                        <select name="filter" class="form-select" onchange="
                                        document.getElementById('custom-range').style.display =
                                        this.value === 'custom' ? 'flex' : 'none';
                                        if(this.value !== 'custom') this.form.submit();
                                        ">
                                        {{-- <option value="today"      {{ request('filter') === 'today'      ? 'selected' : '' }}>Today</option> --}}
                                        <option value="this_month" {{ request('filter','this_month') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                        <option value="yearly"     {{ request('filter') === 'yearly'     ? 'selected' : '' }}>This Year</option>
                                        <option value="custom"     {{ request('filter') === 'custom'     ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>

                                <div id="custom-range"
                                class="col-md-9 row g-3 align-items-end m-0 p-0"
                                style="display: {{ request('filter') === 'custom' ? 'flex' : 'none' }}">

                                <div class="col-md-4">
                                    <label class="form-label fw-bold mb-1">
                                        <i class="bi bi-calendar-event me-1"></i> From
                                    </label>
                                    <input type="date"
                                    name="from"
                                    value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}"
                                    class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold mb-1">
                                        <i class="bi bi-calendar-event me-1"></i> To
                                    </label>
                                    <input type="date"
                                    name="to"
                                    value="{{ request('to', now()->format('Y-m-d')) }}"
                                    class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-1"></i> Apply Filter
                                    </button>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="container-fluid stats_dashboard text-center">
                <div class="row g-4">

                    {{-- REVENUE SECTION --}}
                    <div class="col-md-6">
                        <div class="p-3 h-100 bg-default rounded shadow-sm">
                            <h5 class="stat_title mb-4">Revenue</h5>
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat">${{ number_format($summary['total_revenue'], 2) }}</div>
                                            <div class="stat_description">Total Revenue</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat text-success">${{ number_format($summary['total_net'], 2) }}</div>
                                            <div class="stat_description">Net Amount</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat text-danger">${{ number_format($summary['total_stripe_fee'], 2) }}</div>
                                            <div class="stat_description">Stripe Fees</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat">{{ $summary['total_transactions'] }}</div>
                                            <div class="stat_description">Total Transactions</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- SALES TYPE SECTION --}}
                    <div class="col-md-6">
                        <div class="p-3 h-100 bg-default rounded shadow-sm">
                            <h5 class="stat_title mb-4">Sales Breakdown</h5>
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat text-primary">${{ number_format($summary['subscription_revenue'], 2) }}</div>
                                            <div class="stat_description">Subscription Revenue</div>
                                            <span class="badge bg-primary mt-1">{{ $summary['subscription_percentage'] }}% of total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat text-warning">${{ number_format($summary['one_time_revenue'], 2) }}</div>
                                            <div class="stat_description">One-Time Revenue</div>
                                            <span class="badge bg-warning text-dark mt-1">{{ $summary['one_time_percentage'] }}% of total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat">{{ $summary['subscription_count'] }}</div>
                                            <div class="stat_description">Subscription Txns</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="stat">{{ $summary['one_time_count'] }}</div>
                                            <div class="stat_description">One-Time Txns</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ==================== SUBSCRIPTIONS & PACKAGES DETAIL ==================== --}}
            <div class="row g-4 mt-4 stats_dashboard" style="display:none;">

                {{-- SUBSCRIPTION PLANS --}}
                <div class="col-md-6">
                    <div class="p-4 h-100 bg-default rounded shadow-sm border">
                        <h5 class="stat_title mb-4 fw-bold text-dark text-center">
                            <span class="badge bg-primary me-2">Subscriptions</span> By Plan
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Plan Name</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Net</th>
                                        <th class="text-end">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscriptions as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row->plan_name }}</td>
                                        <td class="text-end text-primary fw-semibold">${{ number_format($row->total_revenue, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($row->total_net, 2) }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ $row->total_transactions }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No subscriptions found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ONE-TIME PACKAGES --}}
                <div class="col-md-6">
                    <div class="p-4 h-100 bg-default rounded shadow-sm border">
                        <h5 class="stat_title mb-4 fw-bold text-dark text-center">
                            <span class="badge bg-warning text-dark me-2">Packages</span> By Product
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Net</th>
                                        <th class="text-end">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($oneTimeList as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row->product_name }}</td>
                                        <td class="text-end text-warning fw-semibold">${{ number_format($row->total_revenue, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($row->total_net, 2) }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-warning text-dark">{{ $row->total_transactions }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No one-time payments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ==================== MONTHLY BREAKDOWN ==================== --}}
            <div class="row g-4 mt-2 stats_dashboard">
                <div class="col-md-12">
                    <div class="p-4 h-100 bg-default rounded shadow-sm border">
                        <h5 class="stat_title mb-4 fw-bold text-dark text-center">Monthly Breakdown</h5>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Total Revenue</th>
                                        <th class="text-end">Stripe Fee</th>
                                        <th class="text-end">Net Amount</th>
                                        <th class="text-end">Subscriptions</th>
                                        <th class="text-end">One-Time</th>
                                        <th class="text-end">Transactions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthly as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row->period }}</td>
                                        <td class="text-end fw-bold">${{ number_format($row->total_revenue, 2) }}</td>
                                        <td class="text-end text-danger">-${{ number_format($row->total_fee, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($row->total_net, 2) }}</td>
                                        <td class="text-end">
                                            <span class="text-primary fw-semibold">${{ number_format($row->subscription_revenue, 2) }}</span>
                                            <span class="text-muted small ms-1">({{ $row->subscription_count }})</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-warning fw-semibold">${{ number_format($row->one_time_revenue, 2) }}</span>
                                            <span class="text-muted small ms-1">({{ $row->one_time_count }})</span>
                                        </td>
                                        <td class="text-end">{{ $row->total_transactions }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No transactions found for this period</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== SALES BY STATE ==================== --}}
            <div class="row g-4 mt-2 stats_dashboard" style="display:none;">
                <div class="col-md-12">
                    <div class="p-4 h-100 bg-default rounded shadow-sm border">
                        <h5 class="stat_title mb-4 fw-bold text-dark text-center">Sales by State</h5>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>State</th>
                                        <th>Country</th>
                                        <th class="text-end">Total Revenue</th>
                                        <th class="text-end">Transactions</th>
                                        <th>% of Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($byState as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row->billing_state ?? 'Unknown' }}</td>
                                        <td class="text-muted">{{ $row->billing_country ?? '—' }}</td>
                                        <td class="text-end fw-bold">${{ number_format($row->total_revenue, 2) }}</td>
                                        <td class="text-end">{{ $row->total_transactions }}</td>
                                        <td style="min-width: 160px">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-primary"
                                                    style="width: {{ $row->percentage }}%">
                                                </div>
                                            </div>
                                            <span class="small fw-semibold">{{ $row->percentage }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No state data found — make sure billing address is being saved
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== PER CUSTOMER ==================== --}}
        <div class="row g-4 mt-2 stats_dashboard" style="display:none;">
            <div class="col-md-12">
                <div class="p-4 h-100 bg-default rounded shadow-sm border">
                    <h5 class="stat_title mb-4 fw-bold text-dark text-center">Per Customer Breakdown</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Total Revenue</th>
                                    <th class="text-end">Stripe Fee</th>
                                    <th class="text-end">Net Amount</th>
                                    <th class="text-end">Subscriptions</th>
                                    <th class="text-end">One-Time</th>
                                    <th class="text-end">Transactions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perUser as $row)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $row->customer_name }}</div>
                                        <div class="text-muted small">{{ $row->customer_email }}</div>
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($row->total_revenue, 2) }}</td>
                                    <td class="text-end text-danger">-${{ number_format($row->total_fee, 2) }}</td>
                                    <td class="text-end text-success">${{ number_format($row->total_net, 2) }}</td>
                                    <td class="text-end text-primary">${{ number_format($row->subscription_revenue, 2) }}</td>
                                    <td class="text-end text-warning">${{ number_format($row->one_time_revenue, 2) }}</td>
                                    <td class="text-end">{{ $row->total_transactions }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No transactions found for this period</td>
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
</div>
</div>
</div>
@endsection