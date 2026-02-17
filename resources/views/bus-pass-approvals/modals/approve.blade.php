<!-- Approve Application Modal -->
<div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.approve', $application) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white">
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            <i class="fas fa-arrow-right"></i>
                            Forward Application #{{ $application->id }}
                        @else
                            <i class="fas fa-check"></i>
                            Approve Application #{{ $application->id }}
                        @endif
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-info-circle"></i>
                            @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                                Forward Confirmation
                            @else
                                Approval Confirmation
                            @endif
                        </h5>
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            You are about to forward the bus pass application for:
                        @else
                            You are about to approve the bus pass application for:
                        @endif
                        <br><strong>{{ $application->person->name }}</strong>
                        @if ($application->person->regiment_no)
                            ({{ $application->person->regiment_no }})
                        @else
                            (Civil ID: {{ $application->person->civil_id }})
                        @endif
                    </div>

                    {{-- SLTB Season Confirmation for higher level approvers --}}
                    @if (
                        !auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']) &&
                            $application->obtain_sltb_season == 'yes')
                        <div class="alert sltb-confirmation-alert">
                            <div class="form-check">
                                <input class="form-check-input @error('sltb_season_confirmation') is-invalid @enderror"
                                    type="checkbox" id="sltb_season_confirmation{{ $application->id }}"
                                    name="sltb_season_confirmation" required>
                                <label class="form-check-label" for="sltb_season_confirmation{{ $application->id }}">
                                    <i class="fas fa-bus text-warning"></i>
                                    <strong>SLTB Season Available Confirmation:</strong>
                                    This person has obtained SLTB Bus Season. Please confirm your approval.
                                </label>
                                @error('sltb_season_confirmation')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    @endif

                    {{-- Route Statistics Section - Only for DMOV users --}}
                    @if (auth()->user()->hasAnyRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)']))
                        @php
                            $dmovStatuses = ['forwarded_to_movement', 'pending_staff_officer_2_mov', 'pending_col_mov'];
                        @endphp
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Route Statistics</h6>
                            </div>
                            <div class="card-body">
                                @if ($application->requested_bus_name)
                                    @php
                                        $dailyStats = $application->getRouteStatistics(
                                            $application->requested_bus_name,
                                            'living_out',
                                            ['Col Mov (DMOV)', 'Director (DMOV)'],
                                            null, // Don't use single status
);
$dailyStats[
    'dmov_pending_count'
] = $application->getPendingCountForRouteByStatuses(
    $dmovStatuses,
    $application->requested_bus_name,
    'living_out',
);
$dailyPendingIntegrationCount = $application->getPendingCountForRouteByStatuses(
    ['approved_for_integration', 'approved_for_temp_card'],
    $application->requested_bus_name,
    'living_out',
                                        );
                                    @endphp
                                    <div class="row mb-2" data-route-section="daily">
                                        <div class="col-md-12">
                                            <strong data-route-name-display="daily"><i class="fas fa-bus"></i> Daily
                                                Travel Route:
                                                {{ $application->requested_bus_name }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-list"></i> All:
                                                    {{ $dailyStats['dmov_pending_count'] + $dailyStats['integrated_count'] + $dailyPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-id-card"></i> Integrated:
                                                    {{ $dailyStats['integrated_count'] }}
                                                </span>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-clock"></i> Pending Integration:
                                                    {{ $dailyPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $dailyStats['dmov_pending_count'] }}
                                                </span>
                                                @if ($dailyStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $dailyStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($application->living_in_bus)
                                    @php
                                        $livingInStats = $application->getRouteStatistics(
                                            $application->living_in_bus,
                                            'living_in',
                                            ['Col Mov (DMOV)', 'Director (DMOV)'],
                                            null, // Don't use single status
);
$livingInStats[
    'dmov_pending_count'
] = $application->getPendingCountForRouteByStatuses(
    $dmovStatuses,
    $application->living_in_bus,
    'living_in',
);
$livingInPendingIntegrationCount = $application->getPendingCountForRouteByStatuses(
    ['approved_for_integration', 'approved_for_temp_card'],
    $application->living_in_bus,
    'living_in',
                                        );
                                    @endphp
                                    <div class="row mb-2" data-route-section="living-in">
                                        <div class="col-md-12">
                                            <strong data-route-name-display="living-in"><i class="fas fa-home"></i>
                                                Living In Route:
                                                {{ $application->living_in_bus }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-list"></i> All:
                                                    {{ $livingInStats['dmov_pending_count'] + $livingInStats['integrated_count'] + $livingInPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-id-card"></i> Integrated:
                                                    {{ $livingInStats['integrated_count'] }}
                                                </span>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-clock"></i> Pending Integration:
                                                    {{ $livingInPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $livingInStats['dmov_pending_count'] }}
                                                </span>
                                                @if ($livingInStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $livingInStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($application->weekend_bus_name)
                                    @php
                                        $weekendStats = $application->getRouteStatistics(
                                            $application->weekend_bus_name,
                                            'weekend',
                                            ['Col Mov (DMOV)', 'Director (DMOV)'],
                                            null, // Don't use single status
);
$weekendStats[
    'dmov_pending_count'
] = $application->getPendingCountForRouteByStatuses(
    $dmovStatuses,
    $application->weekend_bus_name,
    'weekend',
);
$weekendPendingIntegrationCount = $application->getPendingCountForRouteByStatuses(
    ['approved_for_integration', 'approved_for_temp_card'],
    $application->weekend_bus_name,
    'weekend',
                                        );
                                    @endphp
                                    <div class="row mb-2" data-route-section="weekend">
                                        <div class="col-md-12">
                                            <strong data-route-name-display="weekend"><i
                                                    class="fas fa-calendar-weekend"></i> Weekend Route:
                                                {{ $application->weekend_bus_name }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-list"></i> All:
                                                    {{ $weekendStats['dmov_pending_count'] + $weekendStats['integrated_count'] + $weekendPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-id-card"></i> Integrated:
                                                    {{ $weekendStats['integrated_count'] }}
                                                </span>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-clock"></i> Pending Integration:
                                                    {{ $weekendPendingIntegrationCount }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $weekendStats['dmov_pending_count'] }}
                                                </span>
                                                @if ($weekendStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $weekendStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!$application->requested_bus_name && !$application->living_in_bus && !$application->weekend_bus_name)
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle"></i> No route information available for this
                                        application.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('Subject Clerk (DMOV)'))
                        <div class="form-group">
                            <label for="obtain_sltb_season{{ $application->id }}">
                                <i class="fas fa-bus"></i> SLTB Season Availability <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="obtain_sltb_season{{ $application->id }}"
                                name="obtain_sltb_season" required>
                                <option value="">-- Select SLTB Season Status --</option>
                                <option value="yes"
                                    {{ $application->obtain_sltb_season == 'yes' ? 'selected' : '' }}>Yes - Season
                                    Available</option>
                                <option value="no"
                                    {{ $application->obtain_sltb_season == 'no' ? 'selected' : '' }}>
                                    No - Season Not Available</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Current Status:
                                <strong>{{ $application->obtain_sltb_season == 'yes' ? 'Season Available' : 'Season Not Available' }}</strong>
                            </small>
                        </div>
                    @endif

                    {{-- Bus Name Editing Section for SO2 DMOV, Col Mov, and Director Mov --}}
                    @if (auth()->user()->hasAnyRole(['Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)']))
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-bus"></i> Bus Name Modification (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle"></i> You can modify the bus names for this
                                        application if needed. Leave fields empty to keep current values.</small>
                                </div>

                                @if (in_array($application->bus_pass_type, ['daily_travel', 'unmarried_daily_travel']))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="requested_bus_name{{ $application->id }}">
                                                    <i class="fas fa-bus"></i> Requested Bus Name
                                                </label>
                                                <select class="form-control"
                                                    id="requested_bus_name{{ $application->id }}"
                                                    name="requested_bus_name">
                                                    <option value="">-- Keep Current:
                                                        {{ $application->requested_bus_name ?: 'Not set' }} --</option>
                                                </select>
                                                <small class="text-muted">Current:
                                                    {{ $application->requested_bus_name ?: 'Not set' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($application->bus_pass_type === 'living_in_only')
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="living_in_bus{{ $application->id }}">
                                                    <i class="fas fa-bus"></i> Living In Bus
                                                </label>
                                                <select class="form-control" id="living_in_bus{{ $application->id }}"
                                                    name="living_in_bus">
                                                    <option value="">-- Keep Current:
                                                        {{ $application->living_in_bus ?: 'Not set' }} --</option>
                                                </select>
                                                <small class="text-muted">Current:
                                                    {{ $application->living_in_bus ?: 'Not set' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($application->bus_pass_type === 'weekend_only')
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="weekend_bus_name{{ $application->id }}">
                                                    <i class="fas fa-bus"></i> Weekend Bus Name
                                                </label>
                                                <select class="form-control"
                                                    id="weekend_bus_name{{ $application->id }}"
                                                    name="weekend_bus_name">
                                                    <option value="">-- Keep Current:
                                                        {{ $application->weekend_bus_name ?: 'Not set' }} --</option>
                                                </select>
                                                <small class="text-muted">Current:
                                                    {{ $application->weekend_bus_name ?: 'Not set' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($application->bus_pass_type === 'weekend_monthly_travel')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="living_in_bus{{ $application->id }}">
                                                    <i class="fas fa-bus"></i> Living In Bus
                                                </label>
                                                <select class="form-control" id="living_in_bus{{ $application->id }}"
                                                    name="living_in_bus">
                                                    <option value="">-- Keep Current:
                                                        {{ $application->living_in_bus ?: 'Not set' }} --</option>
                                                </select>
                                                <small class="text-muted">Current:
                                                    {{ $application->living_in_bus ?: 'Not set' }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_bus_name{{ $application->id }}">
                                                    <i class="fas fa-bus"></i> Weekend Bus Name
                                                </label>
                                                <select class="form-control"
                                                    id="weekend_bus_name{{ $application->id }}"
                                                    name="weekend_bus_name">
                                                    <option value="">-- Keep Current:
                                                        {{ $application->weekend_bus_name ?: 'Not set' }} --</option>
                                                </select>
                                                <small class="text-muted">Current:
                                                    {{ $application->weekend_bus_name ?: 'Not set' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i>
                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                Forward Remarks (Optional)
                            @elseif(auth()->user()->hasRole('Subject Clerk (DMOV)'))
                                Forward Remarks (Optional)
                            @else
                                Approval Remarks (Optional)
                            @endif
                        </label>
                        <textarea class="form-control" id="remarks{{ $application->id }}" name="remarks" rows="3"
                            placeholder="@if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)'])) Enter any remarks for forwarding this application...@else Enter any remarks for this approval... @endif"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                This forward action will send the application to the Staff Officer (Branch) for
                                recommendation.
                            @elseif(auth()->user()->hasRole('Subject Clerk (DMOV)'))
                                This forward action will send the application to the Staff Officer 2 (DMOV) for final
                                movement approval.
                                <br><strong>Note:</strong> You must specify SLTB Season availability before forwarding.
                            @elseif(auth()->user()->hasRole('Staff Officer 2 (DMOV)'))
                                This approval will send the application to Col Mov (DMOV) / Director (DMOV) for final
                                authorization.
                            @elseif(auth()->user()->hasAnyRole(['Col Mov (DMOV)', 'Director (DMOV)']))
                                This is the final approval. The application will be approved for integration into the
                                system.
                            @else
                                This approval will move the application to the next stage in the workflow.
                            @endif
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="approveBtn{{ $application->id }}"
                        @if (
                            !auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']) &&
                                $application->obtain_sltb_season == 'yes') disabled @endif>
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            <i class="fas fa-arrow-right"></i> Confirm Forward
                        @else
                            <i class="fas fa-check"></i> Confirm Approval
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript for SLTB Season Confirmation --}}
@if (
    !auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']) &&
        $application->obtain_sltb_season == 'yes')
    <script>
        // Initialize SLTB confirmation checkbox handler immediately
        (function() {
            const checkbox = document.getElementById('sltb_season_confirmation{{ $application->id }}');
            const approveBtn = document.getElementById('approveBtn{{ $application->id }}');

            if (checkbox && approveBtn) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        approveBtn.disabled = false;
                        approveBtn.classList.remove('disabled');
                    } else {
                        approveBtn.disabled = true;
                        approveBtn.classList.add('disabled');
                    }
                });
            }
        })();
    </script>
@endif

{{-- JavaScript for SO2 DMOV Bus Name Editing --}}
@if (auth()->user()->hasAnyRole(['Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)']))
    <script>
        // Initialize bus name editing functionality immediately
        (function() {
            // Load bus names when modal is opened
            $('#approveModal{{ $application->id }}').on('shown.bs.modal', function() {
                loadBusNames{{ $application->id }}();
            });

            function loadBusNames{{ $application->id }}() {
                // Load bus routes for daily travel and unmarried daily travel
                @if (in_array($application->bus_pass_type, ['daily_travel', 'unmarried_daily_travel']))
                    loadBusRoutes{{ $application->id }}();
                @endif

                // Load living in buses for living in only
                @if ($application->bus_pass_type === 'living_in_only')
                    loadLivingInBuses{{ $application->id }}();
                @endif

                // Load bus routes for weekend only
                @if ($application->bus_pass_type === 'weekend_only')
                    loadBusRoutesWeekend{{ $application->id }}();
                @endif

                // Load living in buses and bus routes for weekend/monthly travel
                @if ($application->bus_pass_type === 'weekend_monthly_travel')
                    loadLivingInBuses{{ $application->id }}();
                    loadBusRoutesWeekend{{ $application->id }}();
                @endif
            }

            @if (in_array($application->bus_pass_type, ['daily_travel', 'unmarried_daily_travel']))
                function loadBusRoutes{{ $application->id }}() {
                    $.ajax({
                        url: '{{ route('bus-routes.api') }}',
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                populateSelect('requested_bus_name{{ $application->id }}', response
                                    .data, '{{ $application->requested_bus_name }}');
                            } else {
                                console.error('Failed to load bus routes');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading bus routes:', xhr);
                        }
                    });
                }
            @endif

            @if (in_array($application->bus_pass_type, ['living_in_only', 'weekend_monthly_travel']))
                function loadLivingInBuses{{ $application->id }}() {
                    $.ajax({
                        url: '{{ route('living-in-buses.api') }}',
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                populateSelect('living_in_bus{{ $application->id }}', response.data,
                                    '{{ $application->living_in_bus }}');
                            } else {
                                console.error('Failed to load living in buses');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading living in buses:', xhr);
                        }
                    });
                }
            @endif

            @if (in_array($application->bus_pass_type, ['weekend_only', 'weekend_monthly_travel']))
                function loadBusRoutesWeekend{{ $application->id }}() {
                    $.ajax({
                        url: '{{ route('bus-routes.api') }}',
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                populateSelect('weekend_bus_name{{ $application->id }}', response.data,
                                    '{{ $application->weekend_bus_name }}');
                            } else {
                                console.error('Failed to load weekend bus routes');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading weekend bus routes:', xhr);
                        }
                    });
                }
            @endif

            function populateSelect(selectId, data, currentValue) {
                const select = document.getElementById(selectId);
                if (!select) return;

                // Clear existing options except the first one (keep current)
                while (select.children.length > 1) {
                    select.removeChild(select.lastChild);
                }

                // Add options
                data.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    if (item.name === currentValue) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });

                // Add change event listener to update statistics
                select.addEventListener('change', function() {
                    updateRouteStatistics{{ $application->id }}();
                });
            }

            // Function to update route statistics when route changes
            function updateRouteStatistics{{ $application->id }}() {
                // Get current selected routes
                const requestedBus = document.getElementById('requested_bus_name{{ $application->id }}')?.value;
                const livingInBus = document.getElementById('living_in_bus{{ $application->id }}')?.value;
                const weekendBus = document.getElementById('weekend_bus_name{{ $application->id }}')?.value;

                // Update daily travel statistics if route changed
                @if (in_array($application->bus_pass_type, ['daily_travel', 'unmarried_daily_travel']))
                    if (requestedBus && requestedBus !== '-- Keep Current:') {
                        updateStatisticsSection('daily', requestedBus, 'living_out');
                    }
                @endif

                // Update living in statistics if route changed
                @if ($application->bus_pass_type === 'living_in_only')
                    if (livingInBus && livingInBus !== '-- Keep Current:') {
                        updateStatisticsSection('living-in', livingInBus, 'living_in');
                    }
                @endif

                // Update weekend statistics if route changed
                @if ($application->bus_pass_type === 'weekend_only')
                    if (weekendBus && weekendBus !== '-- Keep Current:') {
                        updateStatisticsSection('weekend', weekendBus, 'weekend');
                    }
                @endif

                // Update living in and weekend statistics for weekend/monthly travel
                @if ($application->bus_pass_type === 'weekend_monthly_travel')
                    if (livingInBus && livingInBus !== '-- Keep Current:') {
                        updateStatisticsSection('living-in', livingInBus, 'living_in');
                    }
                    if (weekendBus && weekendBus !== '-- Keep Current:') {
                        updateStatisticsSection('weekend', weekendBus, 'weekend');
                    }
                @endif
            }

            function updateStatisticsSection(sectionType, routeName, routeType) {
                $.ajax({
                    url: '{{ route('route-statistics.api', ['application' => $application->id, 'routeName' => '__routeName__', 'routeType' => '__routeType__']) }}'
                        .replace('__routeName__', encodeURIComponent(routeName))
                        .replace('__routeType__', routeType),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            updateStatisticsBadges(sectionType, response.data);
                        } else {
                            console.error('Failed to load route statistics');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading route statistics:', xhr);
                    }
                });
            }

            function updateStatisticsBadges(sectionType, stats) {
                // Find the statistics section for this route type
                const sectionContainer = document.querySelector(
                    `#approveModal{{ $application->id }} [data-route-section="${sectionType}"]`);

                if (!sectionContainer) return;

                // Update route name display
                const routeNameDisplay = sectionContainer.querySelector(
                    `[data-route-name-display="${sectionType}"]`);
                if (routeNameDisplay) {
                    // Get the current selected route from the dropdown
                    let selectedRoute = '';
                    if (sectionType === 'daily') {
                        const select = document.getElementById('requested_bus_name{{ $application->id }}');
                        selectedRoute = select ? select.value : '';
                    } else if (sectionType === 'living-in') {
                        const select = document.getElementById('living_in_bus{{ $application->id }}');
                        selectedRoute = select ? select.value : '';
                    } else if (sectionType === 'weekend') {
                        const select = document.getElementById('weekend_bus_name{{ $application->id }}');
                        selectedRoute = select ? select.value : '';
                    }

                    if (selectedRoute && selectedRoute !== '-- Keep Current:') {
                        // Update the route name in the display
                        const textNode = routeNameDisplay.lastChild;
                        if (textNode && textNode.nodeType === Node.TEXT_NODE) {
                            textNode.textContent = selectedRoute;
                        }
                    }
                }

                // Update badge values within this section
                const badgeMappings = [{
                        key: 'all_pending_count',
                        label: 'All:'
                    },
                    {
                        key: 'integrated_count',
                        label: 'Integrated:'
                    },
                    {
                        key: 'approved_count',
                        label: 'Approved:'
                    },
                    {
                        key: 'pending_count',
                        label: 'Pending:'
                    }
                ];

                badgeMappings.forEach(function(mapping) {
                    const badges = sectionContainer.querySelectorAll('.badge');
                    badges.forEach(function(badge) {
                        if (badge.textContent.includes(mapping.label)) {
                            // Replace the number after the label
                            const regex = new RegExp(mapping.label + '\\s*\\d+');
                            badge.innerHTML = badge.innerHTML.replace(regex, mapping.label + ' ' + (
                                stats[mapping.key] || 0));
                        }
                    });
                });
            }
        })();
    </script>
@endif
