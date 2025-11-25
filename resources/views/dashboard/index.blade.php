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

    @if (Auth::user() && Auth::user()->hasRole('Subject Clerk (DMOV)'))
        <script>
            $(document).ready(function() {
                console.log('DMOV Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('jQuery available:', typeof $ !== 'undefined');

                // Check if the canvas element exists before trying to initialize
                const dmovPendingCanvas = document.getElementById('dmovPendingByUserLevelChart');
                if (!dmovPendingCanvas) {
                    console.error('Canvas element dmovPendingByUserLevelChart not found!');
                    return;
                }

                console.log('Chart data available, initializing pending approvals chart...');

                // Pending Approvals Chart - Same as Staff Officer 2 (DMOV)
                const dmovPendingByUserLevelCtx = dmovPendingCanvas.getContext('2d');
                const dmovPendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const dmovPendingByUserLevelChart = new Chart(dmovPendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: dmovPendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: dmovPendingByUserLevelData.data,
                            backgroundColor: ['#ffc107', '#fd7e14', '#dc3545'],
                            borderColor: ['#ffc107', '#fd7e14', '#dc3545'],
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif



    @if (Auth::user() && Auth::user()->hasRole('Director (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('Branch Director Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('jQuery available:', typeof $ !== 'undefined');

                // Pending Approvals Chart - Only Chart
                const directorPendingByUserLevelCtx = document.getElementById('directorPendingByUserLevelChart')
                    .getContext('2d');
                const directorPendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const directorPendingByUserLevelChart = new Chart(directorPendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: directorPendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: directorPendingByUserLevelData.data,
                            backgroundColor: '#007bff',
                            borderColor: '#0056b3',
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Staff Officer (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('Staff Officer Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('jQuery available:', typeof $ !== 'undefined');

                // Pending Approvals Chart - All Levels (Vertical Bar)
                const branchStaffOfficerPendingCtx = document.getElementById('branchStaffOfficerPendingChart')
                    .getContext('2d');
                const approvalData = @json($chartData['approvalOverview']);
                const branchStaffOfficerPendingChart = new Chart(branchStaffOfficerPendingCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Branch Clerk', 'Branch Staff Officer', 'DMOV Clerk', 'DMOV Staff Officer 2',
                            'DMOV Colonel'
                        ],
                        datasets: [{
                            label: 'Pending Applications (My Branch)',
                            data: [
                                approvalData.pending_branch_clerk,
                                approvalData.pending_branch_staff_officer,
                                approvalData.pending_dmov_clerk,
                                approvalData.pending_dmov_staff_officer_2,
                                approvalData.pending_dmov_col
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
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });


            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Bus Pass Subject Clerk (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('Branch Subject Clerk Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('jQuery available:', typeof $ !== 'undefined');

                // Pending Approvals Chart - All Levels (Vertical Bar)
                const branchSubjectClerkPendingCtx = document.getElementById('branchClerkPendingChart').getContext(
                '2d');
                const branchApprovalData = @json($chartData['approvalOverview']);
                const branchSubjectClerkPendingChart = new Chart(branchSubjectClerkPendingCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Branch Clerk', 'Branch Staff Officer', 'DMOV Clerk', 'DMOV Staff Officer 2',
                            'DMOV Colonel'
                        ],
                        datasets: [{
                            label: 'Pending Applications (My Branch)',
                            data: [
                                branchApprovalData.pending_branch_clerk,
                                branchApprovalData.pending_branch_staff_officer,
                                branchApprovalData.pending_dmov_clerk,
                                branchApprovalData.pending_dmov_staff_officer_2,
                                branchApprovalData.pending_dmov_col
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
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Staff Officer 2 (DMOV)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('DMOV Staff Officer 2 Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');

                // Pending Applications by User Level Chart (Vertical Bar) - Only Chart
                const so2PendingByUserLevelCtx = document.getElementById('so2PendingByUserLevelChart').getContext('2d');
                const so2PendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const so2PendingByUserLevelChart = new Chart(so2PendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: so2PendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: so2PendingByUserLevelData.data,
                            backgroundColor: ['#ffc107', '#fd7e14', '#dc3545'],
                            borderColor: ['#ffc107', '#fd7e14', '#dc3545'],
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif



    @if (Auth::user() && Auth::user()->hasRole('Staff Officer 1 (DMOV)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('DMOV Staff Officer 1 Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');



                // Pending Applications by User Level Chart (Vertical Bar)
                const so1PendingByUserLevelCtx = document.getElementById('so1PendingByUserLevelChart').getContext('2d');
                const so1PendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const so1PendingByUserLevelChart = new Chart(so1PendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: so1PendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: so1PendingByUserLevelData.data,
                            backgroundColor: ['#ffc107', '#fd7e14', '#dc3545'],
                            borderColor: ['#ffc107', '#fd7e14', '#dc3545'],
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Col Mov (DMOV)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('Col Mov Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');

                // Pending Applications by User Level Chart (Vertical Bar) - Only Chart
                const colPendingByUserLevelCtx = document.getElementById('colPendingByUserLevelChart').getContext('2d');
                const colPendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const colPendingByUserLevelChart = new Chart(colPendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: colPendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: colPendingByUserLevelData.data,
                            backgroundColor: '#007bff',
                            borderColor: '#0056b3',
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Director (DMOV)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('DMOV Director Dashboard JavaScript Loading...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');



                // Pending Applications by User Level Chart (Vertical Bar)
                const dirPendingByUserLevelCtx = document.getElementById('dirPendingByUserLevelChart').getContext('2d');
                const dirPendingByUserLevelData = @json($chartData['pendingByUserLevel'] ?? null) || {
                    'labels': [],
                    'data': []
                };

                const dirPendingByUserLevelChart = new Chart(dirPendingByUserLevelCtx, {
                    type: 'bar',
                    data: {
                        labels: dirPendingByUserLevelData.labels,
                        datasets: [{
                            label: 'Pending Applications',
                            data: dirPendingByUserLevelData.data,
                            backgroundColor: ['#ffc107', '#fd7e14', '#dc3545'],
                            borderColor: ['#ffc107', '#fd7e14', '#dc3545'],
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
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Bus Pass Subject Clerk (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                // Pending Approvals Chart - All Levels (Vertical Bar)
                const branchClerkPendingCtx = document.getElementById('branchClerkPendingChart').getContext('2d');
                const branchApprovalData = @json($chartData['approvalOverview']);
                const branchClerkPendingChart = new Chart(branchClerkPendingCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Branch Clerk', 'Branch Staff Officer', 'DMOV Clerk', 'DMOV Staff Officer 2',
                            'DMOV Colonel'
                        ],
                        datasets: [{
                            label: 'Pending Applications (My Branch)',
                            data: [
                                branchApprovalData.pending_branch_clerk,
                                branchApprovalData.pending_branch_staff_officer,
                                branchApprovalData.pending_dmov_clerk,
                                branchApprovalData.pending_dmov_staff_officer_2,
                                branchApprovalData.pending_dmov_col
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
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
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
            <!-- Pending Approvals - Only Chart -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals (My Branch)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="branchClerkPendingChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Staff Officer (Branch)') && !empty($chartData))
        <!-- Charts Section for Staff Officer (Branch) -->
        <div class="row mt-4">
            <!-- Pending Approvals - Only Chart -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i> Pending Approvals (My Branch)
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="branchStaffOfficerPendingChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Director (Branch)') && !empty($chartData))
        <!-- Charts Section for Director (Branch) -->
        <div class="row mt-4">
            <!-- Pending Approvals - Only Chart -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="directorPendingByUserLevelChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user() && Auth::user()->hasRole('Subject Clerk (DMOV)'))
        {{-- DMOV Subject Clerk Dashboard --}}
        @if (!empty($chartData))
            <div class="row">
                <!-- Pending Approvals - Only Chart -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="dmovPendingByUserLevelChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h4>Dashboard Loading...</h4>
                        <p>Chart data is not available. Please contact administrator if this persists.</p>
                        <p>Debug: chartData = {{ json_encode($chartData ?? 'null') }}</p>
                    </div>
                </div>
            </div>
        @endif
    @elseif(Auth::user() && Auth::user()->hasRole('Staff Officer 2 (DMOV)') && !empty($chartData))
        {{-- DMOV Staff Officer 2 Dashboard --}}
        <div class="row">
            <!-- Pending Applications by User Level -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="so2PendingByUserLevelChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user() && Auth::user()->hasRole('Staff Officer 1 (DMOV)') && !empty($chartData))
        {{-- DMOV Staff Officer 1 Dashboard --}}
        <div class="row">
            <!-- Pending Applications by User Level -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="so1PendingByUserLevelChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user() && Auth::user()->hasRole('Col Mov (DMOV)') && !empty($chartData))
        {{-- Col Mov Dashboard --}}
        <div class="row">
            <!-- Pending Applications by User Level -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="colPendingByUserLevelChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user() && Auth::user()->hasRole('Director (DMOV)') && !empty($chartData))
        {{-- DMOV Director Dashboard --}}
        <div class="row">
            <!-- Pending Applications by User Level -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Pending Approvals</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dirPendingByUserLevelChart" style="height: 300px;"></canvas>
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
                        <h3>{{ \App\Models\BusPassApplication::whereIn('status', ['pending_subject_clerk_mov', 'pending_staff_officer_2_mov', 'pending_col_mov'])->count() }}
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

{{-- @endsection --}}

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
