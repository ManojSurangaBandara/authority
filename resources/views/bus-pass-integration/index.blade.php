@extends('adminlte::page')

@section('title', 'Bus Pass Integration Dashboard')

@section('content_header')
    <h1>Bus Pass Integration Dashboard</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role"
        content="{{ auth()->user()->hasRole(['Col Mov (DMOV)', 'Director (DMOV)'])? 'integration_allowed': 'view_only' }}">
@stop

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
                        <div style="overflow-x: auto; overflow-y: hidden;">
                            <canvas id="integrationChart" height="400"></canvas>
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
                                    <th>Temp Card QR</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="applicationsTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application View Modal -->
    <div class="modal fade" id="viewApplicationModal" tabindex="-1" role="dialog"
        aria-labelledby="viewApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewApplicationModalLabel">Application Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="applicationModalBody">
                    <!-- Application details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        let chart = null;
        let currentRouteId = 'all';
        let currentRouteType = 'living_out';
        const canIntegrate = $('meta[name="user-role"]').attr('content') === 'integration_allowed';

        $(document).ready(function() {
            loadChartData();

            // Route filter change
            $('#routeFilter').on('change', function() {
                currentRouteId = $(this).val();
                currentRouteType = $(this).find('option:selected').data('route-type') || 'living_out';
                loadChartData();
                hideApplicationsTable();
            });
        });

        function loadChartData() {
            $.ajax({
                url: '{{ route('bus-pass-integration.chart-data') }}',
                method: 'GET',
                data: {
                    route_id: currentRouteId,
                    route_type: currentRouteType
                },
                success: function(data) {
                    renderChart(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    alert('Error loading chart data');
                }
            });
        }

        function renderChart(data) {
            const ctx = document.getElementById('integrationChart').getContext('2d');

            if (chart) {
                chart.destroy();
            }

            const labels = data.map(item => item.establishment_name);
            const pendingData = data.map(item => item.pending_integration);
            const integratedData = data.map(item => -item.integrated); // Negative for below axis

            // Calculate dynamic width based on number of establishments
            const minBarWidth = 60; // Minimum width per bar
            const dynamicWidth = Math.max(600, labels.length * minBarWidth);

            // Set canvas width
            const canvas = document.getElementById('integrationChart');
            canvas.style.width = dynamicWidth + 'px';
            canvas.width = dynamicWidth;

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
                plugins: [ChartDataLabels],
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Applications'
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
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += Math.abs(context.parsed.y);
                                    return label;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: 'black',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                const absoluteValue = Math.abs(value);
                                return absoluteValue > 0 ? absoluteValue : '';
                            }
                        }
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

            const tbody = $('#applicationsTableBody');
            tbody.empty();

            applications.forEach(function(app) {
                const statusClass = `status-${app.status.replace(/_/g, '_')}`;
                const row = `
                    <tr>
                        <td>${app.id}</td>
                        <td>${app.person_name}</td>
                        <td>${app.person_rank}</td>
                        <td>${app.establishment}</td>
                        <td>${app.requested_bus_name || 'N/A'}</td>
                        <td>${app.weekend_bus_name || 'N/A'}</td>
                        <td><span class="status-badge ${statusClass}">${app.status.replace(/_/g, ' ')}</span></td>
                        <td>${app.branch_card_id || 'N/A'}</td>
                        <td>${app.temp_card_qr || 'N/A'}</td>
                        <td>
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
                                : ''}
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            $('#applicationsTable').show();
        }

        function hideApplicationsTable() {
            $('#applicationsTable').hide();
            $('#tableTitle').text('Click on chart bars to view applications');
        }

        // View application modal
        $(document).on('click', '.view-application', function() {
            const applicationId = $(this).data('id');

            $.ajax({
                url: '{{ url("bus-pass-integration") }}/' + applicationId,
                method: 'GET',
                success: function(response) {
                    displayApplicationModal(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading application details:', error);
                    alert('Error loading application details');
                }
            });
        });

        // Integration action
        $(document).on('click', '.integrate-application', function() {
            const applicationId = $(this).data('id');

            if (confirm('Are you sure you want to integrate this application?')) {
                $.ajax({
                    url: '{{ url("bus-pass-integration") }}/' + applicationId + '/integrate',
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
                    url: '{{ url("bus-pass-integration") }}/' + applicationId + '/undo-integrate',
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

        function displayApplicationModal(data) {
            const app = data.application;
            const person = data.person;
            const personTypeName = data.person_type_name;
            const establishment = data.establishment;

            // Determine service ID label and value based on person type
            let serviceIdLabel = 'Service ID';
            let serviceIdValue = 'N/A';

            if (personTypeName === 'Army') {
                serviceIdLabel = 'Army ID';
                serviceIdValue = person?.army_id || 'N/A';
            } else if (personTypeName === 'Navy') {
                serviceIdLabel = 'Navy ID';
                serviceIdValue = person?.navy_id || 'N/A';
            } else if (personTypeName === 'Air Force') {
                serviceIdLabel = 'Airforce ID';
                serviceIdValue = person?.airforce_id || 'N/A';
            } else {
                // Default fallback - try to find any service ID
                serviceIdValue = person?.army_id || person?.navy_id || person?.airforce_id || 'N/A';
            }

            const modalContent = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>Application Information</h5>
                        <p><strong>Application ID:</strong> ${app.id}</p>
                        <p><strong>Status:</strong> <span class="badge badge-info">${app.status}</span></p>
                        <p><strong>Branch Card ID:</strong> ${app.branch_card_id || 'N/A'}</p>
                        <p><strong>Temp Card QR:</strong> ${app.temp_card_qr || 'N/A'}</p>
                        <p><strong>Requested Bus:</strong> ${app.requested_bus_name || 'N/A'}</p>
                        <p><strong>Weekend Bus:</strong> ${app.weekend_bus_name || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Person Information</h5>
                        <p><strong>Name:</strong> ${person?.name || 'N/A'}</p>
                        <p><strong>Rank:</strong> ${person?.rank || 'N/A'}</p>
                        <p><strong>${serviceIdLabel}:</strong> ${serviceIdValue}</p>
                        <p><strong>NIC:</strong> ${person?.nic || 'N/A'}</p>
                        <p><strong>Unit:</strong> ${person?.unit || 'N/A'}</p>
                        <p><strong>Establishment:</strong> ${establishment?.name || 'N/A'}</p>
                    </div>
                </div>
            `;

            $('#applicationModalBody').html(modalContent);
            $('#viewApplicationModal').modal('show');
        }
    </script>
@stop
