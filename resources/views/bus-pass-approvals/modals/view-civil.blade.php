<!-- View Civil Application Modal -->
<div class="modal fade" id="viewModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-eye"></i>
                    Civil Bus Pass Application #{{ $application->id }}
                    <span class="badge badge-success ml-2">Civil</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Civil Person Information -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-user"></i> Civil Person Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $application->person->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Civil ID:</strong></td>
                                <td><span class="badge badge-info">{{ $application->person->civil_id }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>NIC:</strong></td>
                                <td>{{ $application->person->nic }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telephone No:</strong></td>
                                <td>{{ $application->person->telephone_no }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Application Details -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-clipboard-list"></i> Application Details</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Branch/Directorate:</strong></td>
                                <td>{{ $application->branch_directorate ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Obtained SLTB Season:</strong></td>
                                <td>
                                    <span
                                        class="badge badge-{{ $application->obtain_sltb_season === 'yes' ? 'success' : 'danger' }}">
                                        {{ ucfirst($application->obtain_sltb_season) }}
                                    </span>
                                </td>
                            </tr>
                            @if ($application->branch_card_availability)
                                <tr>
                                    <td><strong>Branch Card Availability:</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $application->branch_card_availability === 'has_branch_card' ? 'success' : 'warning' }}">
                                            {{ $application->branch_card_availability === 'has_branch_card' ? 'Has Branch Card (Integration)' : 'No Branch Card (Temporary)' }}
                                        </span>
                                    </td>
                                </tr>
                                @if ($application->branch_card_id)
                                    <tr>
                                        <td><strong>Branch Card ID:</strong></td>
                                        <td>
                                            <span class="badge badge-info">{{ $application->branch_card_id }}</span>
                                            <br><small class="text-muted">
                                                <i class="fas fa-check-circle text-success"></i> Verified via API
                                            </small>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            <tr>
                                <td><strong>Arrival at AHQ:</strong></td>
                                <td>{{ $application->date_arrival_ahq ? $application->date_arrival_ahq->format('d M Y') : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Bus Pass Type:</strong></td>
                                <td><span class="badge badge-info">{{ $application->type_label }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Current Status:</strong></td>
                                <td>{!! $application->status_badge !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Bus Pass Type Specific Details -->
                @if ($application->bus_pass_type === 'daily_travel')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-route"></i> Daily Travel Details</h5>
                            @if (isset($canEditRoute) && $canEditRoute)
                                <form id="routeUpdateFormDailyTravel">
                                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                                    <div class="mb-3">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label for="requested_bus_name">Requested Bus:</label>
                                                <select name="requested_bus_name" id="requested_bus_name"
                                                    class="form-control form-control-sm">
                                                    <option value="">Select Bus Route</option>
                                                    @foreach ($busRoutes as $route)
                                                        <option value="{{ $route->name }}"
                                                            {{ $application->requested_bus_name == $route->name ? 'selected' : '' }}>
                                                            {{ $route->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="destination_from_ahq_readonly">Destination from AHQ:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $application->destination_from_ahq }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-row mt-2">
                                            <div class="col-12">
                                                <button type="button" id="updateRouteBtnDailyTravel"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Update Route
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <table class="table table-sm">
                                    @if ($application->requested_bus_name)
                                        <tr>
                                            <td><strong>Requested Bus:</strong></td>
                                            <td>{{ $application->requested_bus_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($application->destination_from_ahq)
                                        <tr>
                                            <td><strong>Destination from AHQ:</strong></td>
                                            <td>{{ $application->destination_from_ahq }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($application->bus_pass_type === 'weekend_monthly_travel')
                    <div class="col-12">
                        <h5><i class="fas fa-calendar-week"></i> Weekend/Monthly Travel Details</h5>
                        @if (isset($canEditRoute) && $canEditRoute)
                            <form id="routeUpdateFormWeekendMonthly">
                                <input type="hidden" name="application_id" value="{{ $application->id }}">
                                <div class="mb-3">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label for="living_in_bus_readonly">Living In Bus:</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $application->living_in_bus }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="destination_location_ahq_readonly">Destination Location from
                                                AHQ:</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $application->destination_location_ahq }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="col-md-6">
                                            <label for="weekend_bus_name">Weekend Bus:</label>
                                            <select name="weekend_bus_name" id="weekend_bus_name"
                                                class="form-control form-control-sm">
                                                <option value="">Select Bus Route</option>
                                                @foreach ($busRoutes as $route)
                                                    <option value="{{ $route->name }}"
                                                        {{ $application->weekend_bus_name == $route->name ? 'selected' : '' }}>
                                                        {{ $route->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="weekend_destination_readonly">Weekend Destination:</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ $application->weekend_destination }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="col-12">
                                            <button type="button" id="updateRouteBtnWeekendMonthly"
                                                class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Route
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <table class="table table-sm">
                                @if ($application->living_in_bus)
                                    <tr>
                                        <td><strong>Living In Bus:</strong></td>
                                        <td>{{ $application->living_in_bus }}</td>
                                    </tr>
                                @endif
                                @if ($application->destination_location_ahq)
                                    <tr>
                                        <td><strong>Destination Location from AHQ:</strong></td>
                                        <td>{{ $application->destination_location_ahq }}</td>
                                    </tr>
                                @endif
                                @if ($application->weekend_bus_name)
                                    <tr>
                                        <td><strong>Weekend Bus:</strong></td>
                                        <td>{{ $application->weekend_bus_name }}</td>
                                    </tr>
                                @endif
                                @if ($application->weekend_destination)
                                    <tr>
                                        <td><strong>Weekend Destination:</strong></td>
                                        <td>{{ $application->weekend_destination }}</td>
                                    </tr>
                                @endif
                            </table>
                        @endif
                    </div>
                @endif

                @if ($application->bus_pass_type === 'weekend_only')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-calendar-weekend"></i> Weekend Only Details</h5>
                            @if (isset($canEditRoute) && $canEditRoute)
                                <form id="routeUpdateFormWeekendOnly">
                                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                                    <div class="mb-3">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label for="weekend_bus_name">Weekend Bus:</label>
                                                <select name="weekend_bus_name" id="weekend_bus_name"
                                                    class="form-control form-control-sm">
                                                    <option value="">Select Bus Route</option>
                                                    @foreach ($busRoutes as $route)
                                                        <option value="{{ $route->name }}"
                                                            {{ $application->weekend_bus_name == $route->name ? 'selected' : '' }}>
                                                            {{ $route->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="weekend_destination_readonly">Weekend Destination:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $application->weekend_destination }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-row mt-2">
                                            <div class="col-12">
                                                <button type="button" id="updateRouteBtnWeekendOnly"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Update Route
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <table class="table table-sm">
                                    @if ($application->weekend_bus_name)
                                        <tr>
                                            <td><strong>Weekend Bus:</strong></td>
                                            <td>{{ $application->weekend_bus_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($application->weekend_destination)
                                        <tr>
                                            <td><strong>Weekend Destination:</strong></td>
                                            <td>{{ $application->weekend_destination }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Address Information -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h5><i class="fas fa-map-marker-alt"></i> Address Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Permanent Address:</strong></td>
                                <td>{{ $application->person->permanent_address }}</td>
                            </tr>
                            <tr>
                                <td><strong>Grama Seva Division:</strong></td>
                                <td>{{ $application->person->gsDivision->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nearest Police Station:</strong></td>
                                <td>{{ $application->person->policeStation->name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h5><i class="fas fa-folder-open"></i> Documents</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Grama Niladari Certificate:</strong></td>
                                <td>
                                    @if ($application->grama_niladari_certificate)
                                        <a href="{{ asset('storage/' . $application->grama_niladari_certificate) }}"
                                            target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                        <small class="text-success ml-2"><i class="fas fa-check-circle"></i>
                                            Uploaded</small>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Person Image:</strong></td>
                                <td>
                                    @if ($application->person_image)
                                        <a href="{{ asset('storage/' . $application->person_image) }}"
                                            target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-image"></i> View Image
                                        </a>
                                        <small class="text-success ml-2"><i class="fas fa-check-circle"></i>
                                            Uploaded</small>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            @if (
                                $application->marriage_part_ii_order &&
                                    !($application->bus_pass_type === 'weekend_monthly_travel' && $application->marital_status !== 'married'))
                                <tr>
                                    <td><strong>Marriage Part II Order:</strong></td>
                                    <td>
                                        <a href="{{ asset('storage/' . $application->marriage_part_ii_order) }}"
                                            target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                        <small class="text-success ml-2"><i class="fas fa-check-circle"></i>
                                            Uploaded</small>
                                    </td>
                                </tr>
                            @endif
                            @if (
                                $application->permission_letter &&
                                    ($application->bus_pass_type === 'unmarried_daily_travel' ||
                                        ($application->bus_pass_type === 'weekend_monthly_travel' && $application->marital_status !== 'married')))
                                <tr>
                                    <td><strong>Letter of Permission:</strong></td>
                                    <td>
                                        <a href="{{ asset('storage/' . $application->permission_letter) }}"
                                            target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                        <small class="text-success ml-2"><i class="fas fa-check-circle"></i>
                                            Uploaded</small>
                                        <br><small class="text-info">
                                            <i class="fas fa-info-circle"></i>
                                            {{ $application->bus_pass_type === 'unmarried_daily_travel' ? 'For Unmarried Daily Travel only' : 'For Single Personnel with Weekend and Living in Travel' }}
                                        </small>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Application Timeline -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h5><i class="fas fa-clock"></i> Application Timeline</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Created By:</strong></td>
                                <td>{{ $application->created_by ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created Date:</strong></td>
                                <td>{{ $application->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $application->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Current Remarks -->
                @if ($application->remarks)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-comment"></i> Current Remarks</h5>
                            <div class="alert alert-info">
                                {{ $application->remarks }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Approval History -->
                @if ($application->approvalHistory->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-history"></i> Approval History</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Action By</th>
                                            <th>Action</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($application->approvalHistory as $history)
                                            <tr
                                                class="{{ $history->action === 'not_recommended' ? 'table-warning' : '' }}">
                                                <td>
                                                    <small>{{ $history->action_date->format('d M Y') }}<br>{{ $history->action_date->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $history->user->name }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $history->user->roles->first()->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    {!! $history->action_badge !!}
                                                </td>
                                                <td>
                                                    @if ($history->remarks)
                                                        <small
                                                            class="{{ $history->action === 'not_recommended' ? 'text-warning' : '' }}"
                                                            style="white-space: pre-line;">
                                                            {{ $history->remarks }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">No remarks</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                @if (!$isViewOnly ?? false)
                    @if (auth()->user()->can('approve_bus_pass'))
                        @if (auth()->user()->hasRole('Staff Officer (Branch)'))
                            @if ($application->wasRecentlyDmovNotRecommended())
                                {{-- Application returned from DMOV: Only show forward to branch clerk --}}
                                <button type="button" class="btn btn-primary" data-dismiss="modal"
                                    data-toggle="modal"
                                    data-target="#forwardToBranchClerkModal{{ $application->id }}">
                                    <i class="fas fa-arrow-left"></i> Forward to Branch Clerk
                                </button>
                            @else
                                {{-- Normal workflow: Recommend/Not Recommend --}}
                                <button type="button" class="btn btn-success" data-dismiss="modal"
                                    data-toggle="modal" data-target="#recommendModal{{ $application->id }}">
                                    <i class="fas fa-thumbs-up"></i> Recommend
                                </button>
                                <button type="button" class="btn btn-warning" data-dismiss="modal"
                                    data-toggle="modal" data-target="#notRecommendModal{{ $application->id }}">
                                    <i class="fas fa-thumbs-down"></i> Not Recommend
                                </button>
                            @endif
                        @elseif (auth()->user()->hasRole('Subject Clerk (DMOV)'))
                            {{-- Subject Clerk DMOV: Forward/Not Recommend --}}
                            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal"
                                data-target="#approveModal{{ $application->id }}">
                                <i class="fas fa-arrow-right"></i> Forward
                            </button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal" data-toggle="modal"
                                data-target="#dmovNotRecommendModal{{ $application->id }}">
                                <i class="fas fa-thumbs-down"></i> Not Recommend
                            </button>
                        @elseif (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                            {{-- Bus Pass Subject Clerk (Branch): Edit for returned applications, Forward --}}
                            @php
                                $hasDmovReturned =
                                    method_exists($application, 'wasRecentlyDmovNotRecommended') &&
                                    $application->wasRecentlyDmovNotRecommended();
                                $hasBranchReturned =
                                    method_exists($application, 'wasRecentlyNotRecommended') &&
                                    $application->wasRecentlyNotRecommended();
                            @endphp
                            @if ($hasDmovReturned || $hasBranchReturned)
                                <a href="{{ route('bus-pass-applications.edit', $application) }}"
                                    class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Application
                                </a>
                            @endif
                            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal"
                                data-target="#approveModal{{ $application->id }}">
                                <i class="fas fa-arrow-right"></i> Forward
                            </button>
                        @else
                            {{-- All other roles: Approve/Reject or Forward --}}
                            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal"
                                data-target="#approveModal{{ $application->id }}">
                                <i class="fas fa-check"></i> Approve
                            </button>

                            <button type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal"
                                data-target="#rejectModal{{ $application->id }}">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@if (isset($canEditRoute) && $canEditRoute)
    <script>
        $(document).ready(function() {
            // Handle route update for daily travel
            $('#updateRouteBtn').on('click', function() {
                updateRoute('#routeUpdateForm');
            });

            // Handle route update for unmarried daily travel
            $('#updateRouteBtnUnmarried').on('click', function() {
                updateRoute('#routeUpdateFormUnmarried');
            });

            // Handle route update for weekend
            $('#updateRouteBtnWeekend').on('click', function() {
                updateRoute('#routeUpdateFormWeekend');
            });

            // Handle route update for weekend monthly
            $('#updateRouteBtnWeekendMonthly').on('click', function() {
                updateRoute('#routeUpdateFormWeekendMonthly');
            });

            // Handle route update for weekend only
            $('#updateRouteBtnWeekendOnly').on('click', function() {
                updateRoute('#routeUpdateFormWeekendOnly');
            });

            // Handle route update for daily travel
            $('#updateRouteBtnDailyTravel').on('click', function() {
                updateRoute('#routeUpdateFormDailyTravel');
            });

            function updateRoute(formSelector) {
                var formData = $(formSelector).serialize();
                var applicationId = $(formSelector + ' input[name="application_id"]').val();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Add current route filter information if on integration page
                var currentRouteId = window.currentRouteId || 'all';
                var currentRouteType = window.currentRouteType || 'living_out';
                if (currentRouteId !== 'all') {
                    formData += '&current_route_id=' + encodeURIComponent(currentRouteId) +
                        '&current_route_type=' + encodeURIComponent(currentRouteType);
                }

                console.log('=== Route Update Debug ===');
                console.log('CSRF token:', csrfToken);
                console.log('Form data:', formData);

                $.ajax({
                    url: '{{ url('bus-pass-approvals') }}/' + applicationId + '/update-route',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        console.log('Success:', response);
                        if (response.success) {
                            if (response.changed) {
                                // Route was actually changed
                                toastr.success(response.message || 'Route updated successfully!');

                                // Check if application should be removed from current view
                                if (response.should_remove_from_view && window
                                    .removeApplicationFromView) {
                                    window.removeApplicationFromView(applicationId);
                                }
                            } else {
                                // No changes were made
                                toastr.info(response.message ||
                                    'No changes were made - the selected route is already set.');
                            }
                        } else {
                            // Handle validation or other errors
                            toastr.error(response.message ||
                                'Failed to update route. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to update route. Please try again.';

                        if (xhr.status === 403) {
                            errorMessage = 'You do not have permission to update this route.';
                        } else if (xhr.status === 422) {
                            // Validation errors
                            var response = xhr.responseJSON;
                            if (response && response.errors) {
                                var errors = [];
                                for (var field in response.errors) {
                                    errors = errors.concat(response.errors[field]);
                                }
                                errorMessage = errors.join(', ');
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    </script>
@endif
