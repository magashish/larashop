@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">

                  <!-- <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-person-fill fs-1 me-3"></i>
                    <div>
                        Logged in as <strong>{{ Auth::guard('admin')->user()->email }}</strong> with 
                        <strong>{{ Auth::guard('admin')->user()->level }}</strong> level access
                    </div>
                </div> -->

                @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="container-fluid stats_dashboard text-center">

                    
                    <!-- ==================== Data Path Line Graph ==================== -->
                    <div class="row g-4 mt-4">
                        <div class="col-md-12">
                            <div class="p-3 h-100 bg-yellow rounded shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="stat_title mb-0">Data Path Over Time</h5>
                                    <select id="daySelector" class="form-select w-auto">
                                        <option value="7">Last 7 Days</option>
                                        <option value="14">Last 14 Days</option>
                                        <option value="30" selected>Last 30 Days</option>
                                        <option value="60">Last 60 Days</option>
                                        <option value="90">Last 90 Days</option>
                                    </select>
                                </div>
                                <div class="card bg-white border-0">
                                    <div class="card-body">
                                        <canvas id="dataPathChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Package Statistics ==================== -->
                    <div class="row g-4 mt-4">
                        <div class="col-md-12">
                            <div class="p-3 h-100 bg-yellow rounded shadow-sm">
                                <h5 class="stat_title mb-4">Package Statistics</h5>

                                <div class="row g-3">
                                    <!-- Total Packages -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <div class="stat">{{ $packageStats['total'] ?? 0 }}</div>
                                                <div class="stat_description">Total Packages Purchased</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bronze Tier -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <span class="badge">Bronze</span>
                                                </div>
                                                <div class="stat">{{ $packageStats['by_tier']['bronze'] ?? 0 }}</div>
                                                <div class="stat_description">Packages Sold</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Silver Tier -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <span class="badge">Silver</span>
                                                </div>
                                                <div class="stat">{{ $packageStats['by_tier']['silver'] ?? 0 }}</div>
                                                <div class="stat_description">Packages Sold</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gold Tier -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <span class="badge">Gold</span>
                                                </div>
                                                <div class="stat">{{ $packageStats['by_tier']['gold'] ?? 0 }}</div>
                                                <div class="stat_description">Packages Sold</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Page Engagement Statistics ==================== -->
                   <!--  <div class="row g-4 mt-4">
                        <div class="col-md-12">
                            <div class="p-3 h-100 bg-yellow rounded shadow-sm">
                                <h5 class="stat_title mb-4">Page Engagement Statistics</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <h6 class="mb-3">Mazda Business Page</h6>
                                                <div class="stat">{{ $pageEngagements['mazda'] ?? 0 }}</div>
                                                <div class="stat_description">Unique Users Engaged</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100 bg-white border-0">
                                            <div class="card-body text-center">
                                                <h6 class="mb-3">Grange Social Business Page</h6>
                                                <div class="stat">{{ $pageEngagements['grange_social'] ?? 0 }}</div>
                                                <div class="stat_description">Unique Users Engaged</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the chart
    let dataPathChart;
    const ctx = document.getElementById('dataPathChart').getContext('2d');

    // Function to load chart data
    function loadChartData(days = 30) {
        fetch(`/admin/stats/data-path-stats?days=${days}`)
            .then(response => response.json())
            .then(data => {
                // Prepare data for Chart.js
                const labels = data.map(item => item.date);
                const values = data.map(item => item.count);

                // If chart already exists, destroy it
                if (dataPathChart) {
                    dataPathChart.destroy();
                }

                // Create new chart
                dataPathChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Data Path Activity',
                            data: values,
                            borderColor: '#FFD700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: window.innerWidth < 768 ? 2 : 4,
                            pointHoverRadius: window.innerWidth < 768 ? 4 : 6,
                            pointBackgroundColor: '#FFD700',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: window.innerWidth < 768 ? 1 : 2,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: window.innerWidth < 768 ? 8 : 12,
                                titleFont: {
                                    size: window.innerWidth < 768 ? 11 : 14
                                },
                                bodyFont: {
                                    size: window.innerWidth < 768 ? 10 : 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 768 ? 9 : 11
                                    },
                                    maxRotation: window.innerWidth < 768 ? 45 : 0,
                                    minRotation: window.innerWidth < 768 ? 45 : 0
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
            });
    }

    // Load initial chart data
    loadChartData(30);

    // Day selector change event
    document.getElementById('daySelector').addEventListener('change', function() {
        loadChartData(this.value);
    });
});
</script>

<style>
/* Additional styling for new sections */
.stat {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat_description {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
}

.stat_title {
    font-weight: 600;
    color: #000;
}

.bg-yellow {
    background-color: #FFD700 !important;
}

.card {
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#dataPathChart {
    width: 100% !important;
    height: 350px !important;
}

@media (max-width: 768px) {
    .stat {
        font-size: 2rem;
    }
    
    #dataPathChart {
        height: 250px !important;
    }
    
    .stat_description {
        font-size: 0.75rem;
    }
    
    .stat_title {
        font-size: 1rem;
    }
}
</style>

@endsection