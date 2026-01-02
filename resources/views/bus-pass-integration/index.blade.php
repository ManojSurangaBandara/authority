@extends('adminlte::page')

@section('title', 'Bus Pass Integration Dashboard')

@section('content_header')
    <h1>Bus Pass Integration Dashboard</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role"
        content="{{ auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)'])? 'integration_allowed': 'view_only' }}">
@stop

@section('plugins.Datatables', true)

@section('content')
    <div class="container-fluid">
        <!-- Route Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="routeFilter">Filter by Route:</label>
                <select id="routeFilter" class="form-control">
                    <option value="all">All Routes</option>
                    <optgroup label="Living Out Routes">
                        @foreach ($busRoutes as $route)
                            <option value="{{ $route->id }}" data-route-type="living_out">{{ $route->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Living In Routes">
                        @foreach ($livingInBuses as $livingInBus)
                            <option value="{{ $livingInBus->id }}" data-route-type="living_in">{{ $livingInBus->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            @if (!auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)']))
                <div class="col-md-8">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> You have view-only access. Only Director and Col MOV can perform integration
                        actions.
                    </div>
                </div>
            @endif
        </div>

        <!-- Chart Container -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Integration Status by Establishment</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto; overflow-y: hidden; min-height: 400px;">
                            <canvas id="integrationChart" style="height: 400px; width: 100%; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" id="tableTitle">Click on chart bars to view applications</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="applicationsTableSearch"
                                        placeholder="Search applications...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table id="applicationsTable" class="table table-bordered table-striped" style="display: none;">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Person Name</th>
                                    <th>Rank</th>
                                    <th>Establishment</th>
                                    <th>Requested Bus</th>
                                    <th>Weekend Bus</th>
                                    <th>Status</th>
                                    <th>Branch Card ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal container for dynamically loaded modals -->
    <div id="modal-container"></div>

@stop

@section('css')
    <style>
        .chart-container {
            position: relative;
            height: 400px;
        }

        .clickable-bar {
            cursor: pointer;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .status-approved_for_integration {
            background-color: #ffc107;
            color: #212529;
        }

        .status-approved_for_temp_card {
            background-color: #17a2b8;
            color: white;
        }

        .status-integrated_to_branch_card {
            background-color: #28a745;
            color: white;
        }

        .status-integrated_to_temp_card {
            background-color: #007bff;
            color: white;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>
        let chart = null;
        let currentRouteId = 'all';
        let currentRouteType = 'living_out';
        let applicationsTable = null;
        const canIntegrate = $('meta[name="user-role"]').attr('content') === 'integration_allowed';

        $(document).ready(function() {
            console.log('Document ready, initializing...');
            console.log('Chart.js available:', typeof Chart);
            initializeDataTable();
            loadChartData();

            // Route filter change
            $('#routeFilter').on('change', function() {
                currentRouteId = $(this).val();
                currentRouteType = $(this).find('option:selected').data('route-type') || 'living_out';
                loadChartData();
                hideApplicationsTable();
            });

            // Search functionality
            $('#applicationsTableSearch').on('keyup', function() {
                if (applicationsTable) {
                    applicationsTable.search($(this).val()).draw();
                }
            });

            // Clear search
            $('#clearSearch').on('click', function() {
                $('#applicationsTableSearch').val('');
                if (applicationsTable) {
                    applicationsTable.search('').draw();
                }
            });
        });

        function initializeDataTable() {
            applicationsTable = $('#applicationsTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                order: [], // Preserve server-side ordering
                info: true,
                autoWidth: false,
                responsive: true,
                pageLength: 25,
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No entries found",
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
                    },
                    {
                        orderable: false,
                        targets: [8]
                    } // Actions column not sortable
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Hide the default search input since we have our own
            $('.dataTables_filter').hide();
        }

        function loadChartData() {
            console.log('Loading chart data...');
            $.ajax({
                url: '{{ route('bus-pass-integration.chart-data') }}',
                method: 'GET',
                data: {
                    route_id: currentRouteId,
                    route_type: currentRouteType
                },
                success: function(data) {
                    console.log('Chart data received:', data);
                    renderChart(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    alert('Error loading chart data');
                }
            });
        }

        function renderChart(data) {
            console.log('Rendering chart with data:', data);
            const ctx = document.getElementById('integrationChart');

            if (!ctx) {
                console.error('Canvas element not found!');
                alert('Canvas element not found!');
                return;
            }

            if (!window.Chart) {
                console.error('Chart.js not loaded!');
                alert('Chart.js library not loaded!');
                return;
            }

            if (chart) {
                chart.destroy();
            }

            const labels = data.map(item => item.establishment_name);
            const pendingData = data.map(item => item.pending_integration);
            const integratedData = data.map(item => -item.integrated); // Negative for below axis

            // Calculate total counts
            const totalPending = pendingData.reduce((sum, value) => sum + value, 0);
            const totalIntegrated = integratedData.reduce((sum, value) => sum + Math.abs(value), 0);

            console.log('Labels:', labels);
            console.log('Pending data:', pendingData);
            console.log('Integrated data:', integratedData);
            console.log('Total Pending:', totalPending);
            console.log('Total Integrated:', totalIntegrated);

            // If no data, show a message
            if (labels.length === 0) {
                console.log('No data to display');
                const context = ctx.getContext('2d');
                context.font = '20px Arial';
                context.fillStyle = 'gray';
                context.textAlign = 'center';
                context.fillText('No data available', ctx.width / 2, ctx.height / 2);
                return;
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pending Integration',
                        data: pendingData,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Integrated',
                        data: integratedData,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Applications'
                            },
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Math.abs(value); // Show positive values for negative integrated data
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Establishments'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                generateLabels: function(chart) {
                                    const datasets = chart.data.datasets;
                                    return datasets.map((dataset, i) => {
                                        const count = i === 0 ? totalPending : totalIntegrated;
                                        return {
                                            text: `${dataset.label} (${count})`,
                                            fillStyle: dataset.backgroundColor,
                                            strokeStyle: dataset.borderColor,
                                            lineWidth: dataset.borderWidth,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        }
                    },
                    onHover: function(event, elements) {
                        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    },
                    onClick: function(event, elements) {
                        if (elements.length > 0) {
                            const elementIndex = elements[0].index;
                            const datasetIndex = elements[0].datasetIndex;
                            const establishmentId = data[elementIndex].establishment_id;
                            const type = datasetIndex === 0 ? 'pending' : 'integrated';

                            loadApplications(establishmentId, type, data[elementIndex].establishment_name);
                        }
                    }
                }
            });

            console.log('Chart created successfully:', chart);
        }

        function loadApplications(establishmentId, type, establishmentName) {
            $.ajax({
                url: '{{ route('bus-pass-integration.applications') }}',
                method: 'GET',
                data: {
                    establishment_id: establishmentId,
                    type: type,
                    route_id: currentRouteId,
                    route_type: currentRouteType
                },
                success: function(response) {
                    displayApplications(response.data, establishmentName, type);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading applications:', error);
                    alert('Error loading applications');
                }
            });
        }

        function displayApplications(applications, establishmentName, type) {
            const title =
                `${establishmentName} - ${type === 'pending' ? 'Pending Integration' : 'Integrated'} Applications (${applications.length})`;
            $('#tableTitle').text(title);

            // Clear existing data
            if (applicationsTable) {
                applicationsTable.clear();
            }

            // Add new data
            applications.forEach(function(app) {
                const statusClass = `status-${app.status.replace(/_/g, '_')}`;
                const statusText = app.status.replace(/_/g, ' ');
                const actionsHtml = `
                    <button class="btn btn-info btn-xs view-application" data-id="${app.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${canIntegrate ?
                        (app.status === 'approved_for_integration' || app.status === 'approved_for_temp_card') ?
                            `<button class="btn btn-warning btn-xs integrate-application ml-1" data-id="${app.id}" title="Integrate Application">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>` :
                        (app.status === 'integrated_to_branch_card' || app.status === 'integrated_to_temp_card') ?
                            `<button class="btn btn-danger btn-xs undo-integration ml-1" data-id="${app.id}" title="Undo Integration">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>` : ''
                        : ''}`;

                applicationsTable.row.add([
                    app.id,
                    app.person_name,
                    app.person_rank,
                    app.establishment,
                    app.requested_bus_name || 'N/A',
                    app.weekend_bus_name || 'N/A',
                    `<span class="status-badge ${statusClass}">${statusText}</span>`,
                    app.branch_card_id || 'N/A',
                    actionsHtml
                ]);
            });

            // Draw the table
            if (applicationsTable) {
                applicationsTable.draw();
            }

            $('#applicationsTable').show();
        }

        function hideApplicationsTable() {
            $('#applicationsTable').hide();
            $('#tableTitle').text('Click on chart bars to view applications');
            // Clear search when hiding table
            $('#applicationsTableSearch').val('');
            if (applicationsTable) {
                applicationsTable.clear().draw();
            }
        }

        // View application modal
        $(document).on('click', '.view-application', function(e) {
            e.preventDefault();

            var button = $(this);
            var appId = button.data('id');

            if (!appId || button.prop('disabled')) return;

            // Show loading state
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            // Load modal content via AJAX
            $.ajax({
                url: '{{ url('bus-pass-approvals') }}/' + appId + '/modal?view_only=true',
                method: 'GET',
                success: function(response) {
                    // Clean up any existing modals and backdrops
                    $('.modal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    // Clear modal container
                    $('#modal-container').empty();

                    // Add new modals
                    $('#modal-container').html(response.modal + response.actionModals);

                    // Show the view modal
                    $('#viewModal' + appId).modal('show');

                    // Re-enable button
                    button.prop('disabled', false).html('<i class="fas fa-eye"></i>');
                },
                error: function(xhr) {
                    console.error('Error loading modal:', xhr);
                    alert('Error loading application details. Please try again.');

                    // Re-enable button
                    button.prop('disabled', false).html('<i class="fas fa-eye"></i>');
                }
            });
        });

        // Handle modal close events to clean up properly
        $(document).on('hidden.bs.modal', '.modal', function() {
            // Clean up modal backdrops and body classes
            if ($('.modal:visible').length === 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }

            // Remove the modal from DOM after it's hidden
            $(this).remove();
        });

        // Handle modal show events
        $(document).on('show.bs.modal', '.modal', function() {
            // Ensure body has modal-open class
            $('body').addClass('modal-open');
        });

        // Integration action
        $(document).on('click', '.integrate-application', function() {
            const applicationId = $(this).data('id');

            if (confirm('Are you sure you want to integrate this application?')) {
                $.ajax({
                    url: '{{ url('bus-pass-integration') }}/' + applicationId + '/integrate',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            loadChartData();
                            hideApplicationsTable();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error integrating application:', error);
                        alert('Error integrating application');
                    }
                });
            }
        });

        // Undo integration action
        $(document).on('click', '.undo-integration', function() {
            const applicationId = $(this).data('id');

            if (confirm('Are you sure you want to undo the integration for this application?')) {
                $.ajax({
                    url: '{{ url('bus-pass-integration') }}/' + applicationId + '/undo-integrate',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            loadChartData();
                            hideApplicationsTable();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error undoing integration:', error);
                        alert('Error undoing integration');
                    }
                });
            }
        });
    </script>
@stop
