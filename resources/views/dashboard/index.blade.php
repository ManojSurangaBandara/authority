@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>
                <i class="fas fa-tachometer-alt"></i> Dashboard
                @auth
                    @if (auth()->user()->isBranchUser())
                        <small class="text-muted">- Branch/Directorate</small>
                    @elseif(auth()->user()->isMovementUser())
                        <small class="text-muted">- Directorate of Movement</small>
                    @endif
                @endauth
            </h1>
        </div>
        <div class="col-md-6 text-right">
            <div class="text-muted">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                @foreach (auth()->user()->roles as $role)
                    <span class="badge badge-primary ml-1">{{ $role->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @if (Auth::user() && Auth::user()->hasRole('Subject Clerk (DMOV)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('DMOV Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('jQuery available:', typeof $ !== 'undefined');

                console.log('Chart data available, initializing charts...');
                // Branch-wise Applications Chart (Horizontal Bar)
                const dmovBranchCtx = document.getElementById('dmovBranchChart').getContext('2d');
                const dmovBranchData = @json($chartData['branchWiseApplications'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const dmovBranchChart = new Chart(dmovBranchCtx, {
                    type: 'bar',
                    data: {
                        labels: dmovBranchData.labels,
                        datasets: [{
                            label: 'Applications',
                            data: dmovBranchData.data,
                            backgroundColor: '#007bff',
                            borderColor: '#007bff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Status Overview Chart (Doughnut)
                const dmovStatusCtx = document.getElementById('dmovStatusChart').getContext('2d');
                const dmovStatusData = @json($chartData['overallStatus'] ?? []);

                const dmovStatusChart = new Chart(dmovStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Forwarded to Movement',
                            'Pending DMOV Subject Clerk',
                            'Pending DMOV Staff Officer 2',
                            'Pending DMOV Staff Officer 1',
                            'Approved for Integration',
                            'Rejected'
                        ],
                        datasets: [{
                            data: [
                                dmovStatusData.forwarded_to_movement,
                                dmovStatusData.pending_dmov_subject_clerk,
                                dmovStatusData.pending_dmov_staff_officer_2,
                                dmovStatusData.pending_dmov_staff_officer_1,
                                dmovStatusData.approved_for_integration,
                                dmovStatusData.rejected
                            ],
                            backgroundColor: [
                                '#17a2b8',
                                '#ffc107',
                                '#fd7e14',
                                '#6f42c1',
                                '#28a745',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Monthly Trends Chart (Line)
                const dmovTrendsCtx = document.getElementById('dmovTrendsChart').getContext('2d');
                const dmovTrendsData = @json($chartData['monthlyTrends'] ?? null) || {
                    'labels': [],
                    'received': [],
                    'processed': []
                };

                const dmovTrendsChart = new Chart(dmovTrendsCtx, {
                    type: 'line',
                    data: {
                        labels: dmovTrendsData.labels,
                        datasets: [{
                            label: 'Received from Branches',
                            data: dmovTrendsData.received,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Processed by DMOV',
                            data: dmovTrendsData.processed,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Processing Time Chart (Bar)
                const dmovTimeCtx = document.getElementById('dmovTimeChart').getContext('2d');
                const dmovTimeData = @json($chartData['processingTime'] ?? []);

                const dmovTimeChart = new Chart(dmovTimeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Same Day', '1-2 Days', '3-5 Days', '5+ Days'],
                        datasets: [{
                            label: 'Applications',
                            data: [
                                dmovTimeData.same_day,
                                dmovTimeData.one_to_two,
                                dmovTimeData.three_to_five,
                                dmovTimeData.over_five
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pass Type Distribution Chart (Pie)
                const dmovPassTypeCtx = document.getElementById('dmovPassTypeChart').getContext('2d');
                const dmovPassTypeData = @json($chartData['passTypeDistribution'] ?? []);

                const dmovPassTypeChart = new Chart(dmovPassTypeCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Daily Travel', 'Weekend/Monthly Travel'],
                        datasets: [{
                            data: [
                                dmovPassTypeData.daily_travel,
                                dmovPassTypeData.weekend_monthly_travel
                            ],
                            backgroundColor: [
                                '#007bff',
                                '#6c757d'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Branch Performance Chart (Stacked Bar)
                const dmovPerformanceCtx = document.getElementById('dmovPerformanceChart').getContext('2d');
                const dmovPerformanceData = @json($chartData['establishmentPerformance'] ?? null) || {
                    'labels': [],
                    'approved': [],
                    'rejected': [],
                    'pending': []
                };

                const dmovPerformanceChart = new Chart(dmovPerformanceCtx, {
                    type: 'bar',
                    data: {
                        labels: dmovPerformanceData.labels,
                        datasets: [{
                            label: 'Approved',
                            data: dmovPerformanceData.approved,
                            backgroundColor: '#28a745'
                        }, {
                            label: 'Rejected',
                            data: dmovPerformanceData.rejected,
                            backgroundColor: '#dc3545'
                        }, {
                            label: 'Pending',
                            data: dmovPerformanceData.pending,
                            backgroundColor: '#ffc107'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@stop

@section('content')



    <!-- Charts Section for Branch Subject Clerk -->
    @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)') && isset($chartData))
        <div class="row mt-4">
            <!-- Application Status Overview -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Application Status Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Application Trends -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Monthly Application Trends
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Processing Time -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Processing Time
                        </h3>
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        <canvas id="processingChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bus Pass Types -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Bus Pass Types
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="passTypesChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weekly Activity -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-area"></i> Weekly Activity
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Staff Officer (Branch)') && !empty($chartData))
        <!-- Charts Section for Staff Officer (Branch) -->
        <div class="row mt-4">
            <!-- Approval Overview -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i> My Approval Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="approvalChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Approvals Trend -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Monthly Approval Activity (Last 6 Months)
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="monthlyApprovalsChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Approval Time -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i> My Response Time
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="approvalTimeChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recommendation Status -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Recommendation Outcomes
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="recommendationChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weekly Recommendations -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week"></i> This Week's Activity
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="weeklyRecommendationsChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Director (Branch)') && !empty($chartData))
        <!-- Charts Section for Director (Branch) -->
        <div class="row mt-4">
            <!-- Approval Overview -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> My Approval Overview
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="directorApprovalChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Approval Activity -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Monthly Approval Activity (Last 6 Months)
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="directorMonthlyChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Approval Time -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i> My Response Time
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="directorTimeChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pass Type Distribution -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Pass Type Distribution
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="directorPassTypeChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weekly Approvals -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week"></i> This Week's Activity
                        </h3>

                    </div>
                    <div class="card-body">
                        <canvas id="directorWeeklyChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user() && Auth::user()->hasRole('Subject Clerk (DMOV)'))
        {{-- DMOV Subject Clerk Dashboard --}}
        <div class="row">
            <!-- Branch-wise Applications -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Applications by Branch</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovBranchChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Overall Status Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Status Overview</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovStatusChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Monthly Trends -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Processing Trends</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovTrendsChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Processing Time -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Processing Time Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovTimeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pass Type Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pass Type Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovPassTypeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Establishment Performance -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Branch Performance Overview</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dmovPerformanceChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->isMovementUser())
        <!-- Movement user stats -->
        <div class="row mt-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ \App\Models\Bus::count() }}</h3>
                        <p>Total Buses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <a href="{{ route('buses.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ \App\Models\Driver::count() }}</h3>
                        <p>Active Drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <a href="{{ route('drivers.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ \App\Models\BusRoute::count() }}</h3>
                        <p>Bus Routes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <a href="{{ route('bus-routes.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ \App\Models\BusPassApplication::whereIn('status', ['pending_subject_clerk_mov', 'pending_staff_officer_2_mov', 'pending_staff_officer_1_mov', 'pending_col_mov', 'pending_director_mov'])->count() }}
                        </h3>
                        <p>Pending Approvals</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <a href="{{ route('bus-pass-approvals.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .small-box .icon {
            color: rgba(255, 255, 255, 0.8);
        }

        .card-body .btn-block {
            margin-bottom: 10px;
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .card-body canvas {
            max-height: 250px !important;
        }
    </style>
@stop

@section('js')
    @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)') && isset($chartData))
        <script>
            $(document).ready(function() {
                // Status Overview Chart (Donut)
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending Review', 'Staff Officer', 'Director', 'To Movement', 'Approved',
                            'Rejected'
                        ],
                        datasets: [{
                            data: [
                                {{ $chartData['statusOverview']['pending_subject_clerk'] }},
                                {{ $chartData['statusOverview']['pending_staff_officer_branch'] }},
                                {{ $chartData['statusOverview']['pending_director_branch'] }},
                                {{ $chartData['statusOverview']['forwarded_to_movement'] }},
                                {{ $chartData['statusOverview']['approved_for_integration'] }},
                                {{ $chartData['statusOverview']['rejected'] }}
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#17a2b8',
                                '#007bff',
                                '#6c757d',
                                '#28a745',
                                '#dc3545'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        }
                    }
                });

                // Monthly Trends Chart (Line)
                const trendsCtx = document.getElementById('trendsChart').getContext('2d');
                const trendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['monthlyTrends']['months']) !!},
                        datasets: [{
                            label: 'Created',
                            data: {!! json_encode($chartData['monthlyTrends']['created']) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Approved',
                            data: {!! json_encode($chartData['monthlyTrends']['approved']) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Processing Time Chart (Bar)
                const processingCtx = document.getElementById('processingChart').getContext('2d');
                const processingChart = new Chart(processingCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($chartData['processingTime'])) !!},
                        datasets: [{
                            label: 'Applications',
                            data: {!! json_encode(array_values($chartData['processingTime'])) !!},
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545'
                            ],
                            borderColor: [
                                '#28a745',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pass Types Chart (Horizontal Bar)
                const passTypesCtx = document.getElementById('passTypesChart').getContext('2d');
                const passTypesData = @json($chartData['passTypes']);
                const passTypeLabels = Object.keys(passTypesData).map(key => {
                    const labels = {
                        'daily_travel': 'Daily Travel',
                        'weekend_monthly_travel': 'Weekend/Monthly',
                        'living_in_only': 'Living In Only',
                        'weekend_only': 'Weekend Only',
                        'unmarried_daily_travel': 'Unmarried Daily'
                    };
                    return labels[key] || key;
                });

                const passTypesChart = new Chart(passTypesCtx, {
                    type: 'bar',
                    data: {
                        labels: passTypeLabels,
                        datasets: [{
                            label: 'Applications',
                            data: Object.values(passTypesData),
                            backgroundColor: '#17a2b8',
                            borderColor: '#17a2b8',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Weekly Activity Chart (Area)
                const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
                const weeklyChart = new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['weeklyActivity']['days']) !!},
                        datasets: [{
                            label: 'Created',
                            data: {!! json_encode($chartData['weeklyActivity']['created']) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.3)',
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Forwarded',
                            data: {!! json_encode($chartData['weeklyActivity']['forwarded']) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.3)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @elseif(auth()->user()->hasRole('Staff Officer (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                // Approval Overview Chart (Doughnut)
                const approvalCtx = document.getElementById('approvalChart').getContext('2d');
                const approvalData = @json($chartData['approvalOverview']);
                const approvalChart = new Chart(approvalCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending Review', 'Recommended', 'Not Recommended', 'Pending Director',
                            'Approved'
                        ],
                        datasets: [{
                            data: [
                                approvalData.pending_review,
                                approvalData.recommended,
                                approvalData.not_recommended,
                                approvalData.pending_director,
                                approvalData.approved
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#dc3545',
                                '#17a2b8',
                                '#007bff'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Monthly Approvals Chart (Line)
                const monthlyApprovalsCtx = document.getElementById('monthlyApprovalsChart').getContext('2d');
                const monthlyData = @json($chartData['monthlyApprovals']);

                const monthlyApprovalsChart = new Chart(monthlyApprovalsCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [{
                                label: 'Received',
                                data: monthlyData.received,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Recommended',
                                data: monthlyData.recommended,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Not Recommended',
                                data: monthlyData.not_recommended,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Approval Time Chart (Bar)
                const approvalTimeCtx = document.getElementById('approvalTimeChart').getContext('2d');
                const approvalTimeData = @json($chartData['approvalTime']);

                const approvalTimeChart = new Chart(approvalTimeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Same Day', '1-2 Days', '3-5 Days', '5+ Days'],
                        datasets: [{
                            label: 'Applications',
                            data: [
                                approvalTimeData.same_day,
                                approvalTimeData.one_to_two,
                                approvalTimeData.three_to_five,
                                approvalTimeData.over_five
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Recommendation Status Chart (Doughnut)
                const recommendationCtx = document.getElementById('recommendationChart').getContext('2d');
                const recommendationData = @json($chartData['recommendationStatus']);

                const recommendationChart = new Chart(recommendationCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending Director', 'Approved by Director', 'Rejected by Director'],
                        datasets: [{
                            data: [
                                recommendationData.pending_director,
                                recommendationData.approved_by_director,
                                recommendationData.rejected_by_director
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Weekly Recommendations Chart (Area)
                const weeklyRecommendationsCtx = document.getElementById('weeklyRecommendationsChart').getContext('2d');
                const weeklyData = @json($chartData['weeklyRecommendations']);

                const weeklyRecommendationsChart = new Chart(weeklyRecommendationsCtx, {
                    type: 'line',
                    data: {
                        labels: weeklyData.labels,
                        datasets: [{
                            label: 'Recommendations',
                            data: weeklyData.data,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.3)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @elseif(auth()->user()->hasRole('Director (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                // Director Approval Overview Chart (Doughnut)
                const directorApprovalCtx = document.getElementById('directorApprovalChart').getContext('2d');
                const directorApprovalData = @json($chartData['approvalOverview']);

                const directorApprovalChart = new Chart(directorApprovalCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending Review', 'Approved', 'Rejected', 'Forwarded to Movement',
                            'Total Processed'
                        ],
                        datasets: [{
                            data: [
                                directorApprovalData.pending_review,
                                directorApprovalData.approved,
                                directorApprovalData.rejected,
                                directorApprovalData.forwarded_to_movement,
                                directorApprovalData.total_processed
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#dc3545',
                                '#17a2b8',
                                '#6c757d'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Director Monthly Approvals Chart (Line)
                const directorMonthlyCtx = document.getElementById('directorMonthlyChart').getContext('2d');
                const directorMonthlyData = @json($chartData['monthlyApprovals']);

                const directorMonthlyChart = new Chart(directorMonthlyCtx, {
                    type: 'line',
                    data: {
                        labels: directorMonthlyData.labels,
                        datasets: [{
                                label: 'Received',
                                data: directorMonthlyData.received,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Approved',
                                data: directorMonthlyData.approved,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Rejected',
                                data: directorMonthlyData.rejected,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Director Approval Time Chart (Bar)
                const directorTimeCtx = document.getElementById('directorTimeChart').getContext('2d');
                const directorTimeData = @json($chartData['approvalTime']);

                const directorTimeChart = new Chart(directorTimeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Same Day', '1-2 Days', '3-5 Days', '5+ Days'],
                        datasets: [{
                            label: 'Applications',
                            data: [
                                directorTimeData.same_day,
                                directorTimeData.one_to_two,
                                directorTimeData.three_to_five,
                                directorTimeData.over_five
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Director Pass Type Distribution Chart (Horizontal Bar)
                const directorPassTypeCtx = document.getElementById('directorPassTypeChart').getContext('2d');
                const directorPassTypeData = @json($chartData['passTypeDistribution']);

                const directorPassTypeChart = new Chart(directorPassTypeCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Daily Travel', 'Weekend/Monthly'],
                        datasets: [{
                            label: 'Applications',
                            data: [
                                directorPassTypeData.daily_travel,
                                directorPassTypeData.weekend_monthly_travel
                            ],
                            backgroundColor: [
                                '#007bff',
                                '#28a745'
                            ]
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Director Weekly Approvals Chart (Area)
                const directorWeeklyCtx = document.getElementById('directorWeeklyChart').getContext('2d');
                const directorWeeklyData = @json($chartData['weeklyApprovals']);

                const directorWeeklyChart = new Chart(directorWeeklyCtx, {
                    type: 'line',
                    data: {
                        labels: directorWeeklyData.labels,
                        datasets: [{
                            label: 'Approvals',
                            data: directorWeeklyData.data,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.3)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@stop
