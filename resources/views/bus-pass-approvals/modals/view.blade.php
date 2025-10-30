<!-- View Application Modal -->
<div class="modal fade" id="viewModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-eye"></i>
                    Bus Pass Application #{{ $application->id }}
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-user"></i> Personal Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $application->person->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rank:</strong></td>
                                <td>{{ $application->person->rank ?: 'Not specified' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Regiment No:</strong></td>
                                <td>{{ $application->person->regiment_no }}</td>
                            </tr>
                            <tr>
                                <td><strong>Unit:</strong></td>
                                <td>{{ $application->person->unit }}</td>
                            </tr>
                            <tr>
                                <td><strong>NIC:</strong></td>
                                <td>{{ $application->person->nic }}</td>
                            </tr>
                            <tr>
                                <td><strong>Army ID:</strong></td>
                                <td>{{ $application->person->army_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contact:</strong></td>
                                <td>{{ $application->person->telephone_no }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Application Details -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-clipboard-list"></i> Application Details</h5>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Establishment:</strong></td>
                                <td>{{ $application->establishment ? $application->establishment->name : $application->branch_directorate ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Marital Status:</strong></td>
                                <td>{{ ucfirst($application->marital_status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Living Out Approval:</strong></td>
                                <td>
                                    <span
                                        class="badge badge-{{ $application->approval_living_out === 'yes' ? 'success' : 'danger' }}">
                                        {{ ucfirst($application->approval_living_out) }}
                                    </span>
                                </td>
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
                            @if (auth()->user()->getHierarchyLevel() >= 4)
                                <tr>
                                    <td><strong>Branch Card Availability:</strong></td>
                                    <td>
                                        @if ($application->branch_card_availability)
                                            <span
                                                class="badge badge-{{ $application->branch_card_availability === 'has_branch_card' ? 'success' : 'warning' }}">
                                                {{ $application->branch_card_availability === 'has_branch_card' ? 'Has Branch Card (Integration)' : 'No Branch Card (Temporary)' }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Not Set</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Arrival at AHQ:</strong></td>
                                <td>{{ $application->date_arrival_ahq ? $application->date_arrival_ahq->format('d M Y') : 'N/A' }}
                                </td>
                            </tr>
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
                                <tr>
                                    <td><strong>Rent Allowance Part II Order:</strong></td>
                                    <td>
                                        @if ($application->rent_allowance_order)
                                            <a href="{{ asset('storage/' . $application->rent_allowance_order) }}"
                                                target="_blank" class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($application->bus_pass_type === 'weekend_monthly_travel')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-calendar-week"></i> Weekend/Monthly Travel Details</h5>
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
                                        <a href="{{ asset('storage/' . $application->person_image) }}" target="_blank"
                                            class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-image"></i> View Image
                                        </a>
                                        <small class="text-success ml-2"><i class="fas fa-check-circle"></i>
                                            Uploaded</small>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
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
                                                            class="{{ $history->action === 'not_recommended' ? 'text-warning' : '' }}">
                                                            "{{ $history->remarks }}"
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
            </div>
        </div>
    </div>
</div>
