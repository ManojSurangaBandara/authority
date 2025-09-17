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
                                <td>{{ $application->person->rank }}</td>
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
                                <td><strong>Branch/Directorate:</strong></td>
                                <td>{{ $application->branch_directorate }}</td>
                            </tr>
                            <tr>
                                <td><strong>Marital Status:</strong></td>
                                <td>{{ ucfirst($application->marital_status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Living Out Approval:</strong></td>
                                <td>{{ ucfirst($application->approval_living_out) }}</td>
                            </tr>
                            <tr>
                                <td><strong>SLTB Season:</strong></td>
                                <td>{{ ucfirst($application->obtain_sltb_season) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Arrival at AHQ:</strong></td>
                                <td>{{ $application->date_arrival_ahq->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bus Pass Type:</strong></td>
                                <td>{{ $application->type_label }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Status:</strong></td>
                                <td>{!! $application->status_badge !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Bus Pass Type Specific Details -->
                @if($application->bus_pass_type === 'daily_travel')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-route"></i> Daily Travel Details</h5>
                            <table class="table table-sm">
                                @if($application->requested_bus_name)
                                    <tr>
                                        <td><strong>Requested Bus:</strong></td>
                                        <td>{{ $application->requested_bus_name }}</td>
                                    </tr>
                                @endif
                                @if($application->destination_from_ahq)
                                    <tr>
                                        <td><strong>Destination from AHQ:</strong></td>
                                        <td>{{ $application->destination_from_ahq }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif

                @if($application->bus_pass_type === 'weekend_monthly_travel')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-calendar-week"></i> Weekend/Monthly Travel Details</h5>
                            <table class="table table-sm">
                                @if($application->living_in_bus)
                                    <tr>
                                        <td><strong>Living In Bus:</strong></td>
                                        <td>{{ $application->living_in_bus }}</td>
                                    </tr>
                                @endif
                                @if($application->destination_location_ahq)
                                    <tr>
                                        <td><strong>Destination Location from AHQ:</strong></td>
                                        <td>{{ $application->destination_location_ahq }}</td>
                                    </tr>
                                @endif
                                @if($application->weekend_bus_name)
                                    <tr>
                                        <td><strong>Weekend Bus:</strong></td>
                                        <td>{{ $application->weekend_bus_name }}</td>
                                    </tr>
                                @endif
                                @if($application->weekend_destination)
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
                                <td>{{ $application->person->grama_seva_division }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nearest Police Station:</strong></td>
                                <td>{{ $application->person->nearest_police_station }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Current Remarks -->
                @if($application->remarks)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-comment"></i> Current Remarks</h5>
                            <div class="alert alert-info">
                                {{ $application->remarks }}
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
