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
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
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
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
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
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
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
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Staff Officer (Branch)') || auth()->user()->hasRole('Director (Branch)'))
        <!-- Quick Stats Row for other branch roles -->
        <div class="row mt-4">
            <!-- Branch user stats -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->count() }}
                        </h3>
                        <p>My Applications</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->where('status', 'approved')->count() }}
                        </h3>
                        <p>Approved</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->whereIn('status', ['pending_subject_clerk', 'pending_staff_officer_branch', 'pending_director_branch'])->count() }}
                        </h3>
                        <p>Pending</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->where('status', 'rejected')->count() }}
                        </h3>
                        <p>Rejected</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <a href="{{ route('rejected-applications.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    @endif
@stop
