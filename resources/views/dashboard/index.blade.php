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
    <!-- DataTables Core JS -->
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- DataTables Buttons JS -->
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>

    <!-- Required for Excel export -->
    <script src="{{ asset('js/jszip.min.js') }}"></script>

    <!-- Required for PDF export -->
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>

    <script src="{{ asset('js/chart.umd.min.js') }}"></script>

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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });

                // Store chart instance globally
                window.dmovPendingByUserLevelChart = dmovPendingByUserLevelChart;
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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status, null,
                                        '{{ auth()->user()->establishment_id }}');
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });

                // Store chart instance globally
                window.directorPendingByUserLevelChart = directorPendingByUserLevelChart;
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
                        labels: ['Branch Clerk', 'Branch Staff Officer'],
                        datasets: [{
                            label: 'Pending Applications (My Branch)',
                            data: [
                                approvalData.pending_branch_clerk,
                                approvalData.pending_branch_staff_officer
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745'
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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });

                // Store chart instance globally
                window.branchStaffOfficerPendingChart = branchStaffOfficerPendingChart;


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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
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
                        },
                        onClick: function(evt, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];

                                // Map chart labels to correct status codes
                                const statusMapping = {
                                    'Branch Clerk': 'pending_subject_clerk',
                                    'Branch Staff Officer': 'pending_staff_officer_branch',
                                    'DMOV Subject Clerk': 'forwarded_to_movement',
                                    'DMOV Staff Officer 2': 'pending_staff_officer_2_mov',
                                    'DMOV Staff Officer 1': 'pending_staff_officer_1_mov',
                                    'Col Mov (DMOV)': 'pending_col_mov',
                                    'Director (DMOV)': 'pending_director_mov'
                                };

                                const status = statusMapping[level];
                                if (status) {
                                    loadDashboardApplications(status);
                                }
                            }
                        },
                        onHover: function(evt, elements) {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            });
        </script>
    @endif

    @if (Auth::user() && Auth::user()->hasRole('Bus Pass Subject Clerk (Branch)') && !empty($chartData))
        <script>
            $(document).ready(function() {
                console.log('Branch Clerk Dashboard JavaScript Loading...');
                console.log('Chart data:', @json($chartData['approvalOverview']));

                // Check if canvas exists
                const canvas = document.getElementById('branchClerkPendingChart');
                if (!canvas) {
                    console.error('Canvas element branchClerkPendingChart not found!');
                    return;
                }

                // Pending Approvals Chart - All Levels (Vertical Bar)
                const branchClerkPendingCtx = canvas.getContext('2d');
                const branchApprovalData = @json($chartData['approvalOverview']);
                console.log('Branch approval data:', branchApprovalData);

                const branchClerkPendingChart = new Chart(branchClerkPendingCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Branch Clerk', 'Branch Staff Officer'],
                        datasets: [{
                            label: 'Pending Applications (My Branch)',
                            data: [
                                branchApprovalData.pending_branch_clerk,
                                branchApprovalData.pending_branch_staff_officer
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745'
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
                        },
                        onClick: function(evt, elements) {
                            console.log('Chart clicked, elements:', elements);
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const labels = this.data.labels;
                                const level = labels[index];
                                console.log('Clicked level:', level);

                                // Branch Clerk can see applications pending at their level and the next level
                                if (level === 'Branch Clerk') {
                                    loadDashboardApplications('pending_subject_clerk', null,
                                        '{{ auth()->user()->establishment_id }}');
                                } else if (level === 'Branch Staff Officer') {
                                    // Show applications pending staff officer review
                                    loadDashboardApplications('pending_staff_officer_branch', null,
                                        '{{ auth()->user()->establishment_id }}');
                                }
                            }
                        }
                    }
                });

                // Store chart instance globally
                window.branchClerkPendingChart = branchClerkPendingChart;
                console.log('Branch Clerk chart initialized:', branchClerkPendingChart);

                // Add cursor change on hover
                canvas.addEventListener('mousemove', function(evt) {
                    const rect = canvas.getBoundingClientRect();
                    const x = evt.clientX - rect.left;
                    const y = evt.clientY - rect.top;
                    const elements = branchClerkPendingChart.getElementsAtEventForMode(evt, 'nearest', {
                        intersect: true
                    }, false);
                    canvas.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    console.log('Mouse move, elements found:', elements.length);
                });
                canvas.addEventListener('mouseleave', function() {
                    canvas.style.cursor = 'default';
                });
            });
        </script>
    @endif

    <!-- DataTable and Chart Click Functionality -->
    <script>
        // Global function for loading dashboard applications
        function loadDashboardApplications(status, level = null, establishmentId = null) {
            $.ajax({
                url: '{{ route('dashboard.applications') }}',
                method: 'GET',
                data: {
                    status: status,
                    level: level,
                    establishment_id: establishmentId
                },
                success: function(response) {
                    if (response.success) {
                        // Set table title
                        let title = `${response.title} (${response.applications.length})`;
                        $('#applicationsTableTitle').text(title);

                        // Clear and populate table
                        if (window.dashboardApplicationsTable) {
                            window.dashboardApplicationsTable.clear();
                            window.dashboardApplicationsTable.rows.add(response.applications);
                            window.dashboardApplicationsTable.draw();
                        }

                        // Show the table section
                        $('#applicationsTableSection').show();

                        // Scroll to table
                        $('html, body').animate({
                            scrollTop: $('#applicationsTableSection').offset().top - 100
                        }, 500);
                    } else {
                        alert('Error loading applications: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading applications:', error);
                    alert('Error loading applications');
                }
            });
        }

        $(document).ready(function() {
            let dashboardApplicationsTable;

            // Initialize DataTable
            function initializeDashboardDataTable() {
                if (dashboardApplicationsTable) {
                    dashboardApplicationsTable.destroy();
                }

                dashboardApplicationsTable = $('#dashboardApplicationsTable').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    order: [],
                    info: true,
                    autoWidth: false,
                    responsive: false,
                    pageLength: 25,
                    language: {
                        search: "",
                        searchPlaceholder: "Search applications...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No applications found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    columnDefs: [{
                        orderable: true,
                        targets: [0, 1, 2, 3, 4, 5, 6, 7]
                    }, {
                        orderable: false,
                        targets: [8]
                    }],
                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'person_regiment_no'
                        },
                        {
                            data: 'person_name'
                        },
                        {
                            data: 'person_rank'
                        },
                        {
                            data: 'establishment_name'
                        },
                        {
                            data: 'bus_pass_type_label'
                        },
                        {
                            data: 'status_badge'
                        },
                        {
                            data: 'applied_date'
                        },
                        {
                            data: 'actions'
                        }
                    ]
                });

                // Store globally for access from chart callbacks
                window.dashboardApplicationsTable = dashboardApplicationsTable;
            }

            // Close applications table
            $('#closeApplicationsTable').on('click', function() {
                $('#applicationsTableSection').hide();
                if (dashboardApplicationsTable) {
                    dashboardApplicationsTable.clear().draw();
                }
            });

            // Initialize DataTable on page load
            initializeDashboardDataTable();
        });
    </script>
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

    <!-- Applications DataTable Section (hidden by default, shown when clicking chart bars) -->
    <div class="row mt-4" id="applicationsTableSection" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="applicationsTableTitle">Applications</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" id="closeApplicationsTable">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dashboardApplicationsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Regiment No</th>
                                    <th>Person Name</th>
                                    <th>Rank</th>
                                    <th>Establishment</th>
                                    <th>Bus Pass Type</th>
                                    <th>Status</th>
                                    <th>Applied Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

@stop

{{-- @endsection --}}

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap4.min.css') }}">

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

        /* DataTable styling */
        #dashboardApplicationsTable {
            width: 100% !important;
        }

        #dashboardApplicationsTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .clickable-chart {
            cursor: pointer;
        }

        .clickable-chart:hover {
            opacity: 0.8;
        }
    </style>
@stop
