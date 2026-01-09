@extends('adminlte::page')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-3">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-id-card nav-icon"></i> {{ __('Bus Pass Application Details') }}
                        <div class="float-right">
                            <span
                                class="badge badge-{{ $bus_pass_application->status === 'approved' ? 'success' : ($bus_pass_application->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ $bus_pass_application->getStatusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Personal Information</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <strong>Regiment No:</strong><br>
                                {{ $bus_pass_application->person->regiment_no }}
                            </div>
                            <div class="col-md-3">
                                <strong>Rank:</strong><br>
                                {{ $bus_pass_application->person->rank ?: 'Not specified' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Name:</strong><br>
                                {{ $bus_pass_application->person->name }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Unit:</strong><br>
                                {{ $bus_pass_application->person->unit }}
                            </div>
                            <div class="col-md-3">
                                <strong>NIC:</strong><br>
                                {{ $bus_pass_application->person->nic }}
                            </div>
                            <div class="col-md-3">
                                <strong>Navy ID:</strong><br>
                                {{ $bus_pass_application->person->navy_id }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Permanent Address:</strong><br>
                                {{ $bus_pass_application->person->permanent_address }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Province:</strong><br>
                                {{ $bus_pass_application->person->province->name ?? 'Not specified' }}
                            </div>
                            <div class="col-md-6">
                                <strong>District:</strong><br>
                                {{ $bus_pass_application->person->district->name ?? 'Not specified' }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Telephone No:</strong><br>
                                {{ $bus_pass_application->person->telephone_no }}
                            </div>
                            <div class="col-md-4">
                                <strong>Grama Seva Division:</strong><br>
                                {{ $bus_pass_application->person->gsDivision->name ?? 'Not specified' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Nearest Police Station:</strong><br>
                                {{ $bus_pass_application->person->policeStation->name ?? 'Not specified' }}
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Application Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Application Information</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Establishment</strong><br>
                                {{ $bus_pass_application->establishment ? $bus_pass_application->establishment->name : 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Marital Status:</strong><br>
                                {{ ucfirst($bus_pass_application->marital_status) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Date of Arrival at AHQ:</strong><br>
                                {{ $bus_pass_application->date_arrival_ahq ? \Carbon\Carbon::parse($bus_pass_application->date_arrival_ahq)->format('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Approval for Living Out:</strong><br>
                                <span
                                    class="badge badge-{{ $bus_pass_application->approval_living_out === 'yes' ? 'success' : 'danger' }}">
                                    {{ ucfirst($bus_pass_application->approval_living_out) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <strong>Obtained SLTB Season:</strong><br>
                                <span
                                    class="badge badge-{{ $bus_pass_application->obtain_sltb_season === 'yes' ? 'success' : 'danger' }}">
                                    {{ ucfirst($bus_pass_application->obtain_sltb_season) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <strong>Bus Pass Type:</strong><br>
                                <span class="badge badge-info">{{ $bus_pass_application->getTypeLabel() }}</span>
                            </div>
                        </div>

                        @if ($bus_pass_application->branch_card_availability)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Branch Card Availability:</strong><br>
                                    <span
                                        class="badge badge-{{ $bus_pass_application->branch_card_availability === 'has_branch_card' ? 'success' : 'warning' }}">
                                        {{ $bus_pass_application->branch_card_availability === 'has_branch_card' ? 'Has Branch Card (Integration)' : 'No Branch Card (Temporary)' }}
                                    </span>
                                </div>
                                @if ($bus_pass_application->branch_card_id)
                                    <div class="col-md-6">
                                        <strong>Branch Card ID:</strong><br>
                                        <span class="badge badge-info">{{ $bus_pass_application->branch_card_id }}</span>
                                        <br><small class="text-muted">
                                            <i class="fas fa-check-circle text-success"></i> Verified via API
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>

                    <div class="card-body">

                        <!-- Conditional Travel Details -->
                        @if ($bus_pass_application->bus_pass_type === 'daily_travel')
                            <div class="row mb-4 mt-5">
                                <div class="col-12">
                                    <h4 class="text-info border-bottom pb-2">Living out Person - Daily Traveling</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Requested Bus Name:</strong><br>
                                    {{ $bus_pass_application->requested_bus_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination location from AHQ:</strong><br>
                                    {{ $bus_pass_application->destination_from_ahq ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Marriage Part II Order:</strong><br>
                                    @if ($bus_pass_application->marriage_part_ii_order)
                                        <a href="{{ asset('storage/' . $bus_pass_application->marriage_part_ii_order) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($bus_pass_application->bus_pass_type === 'unmarried_daily_travel')
                            <div class="row mb-4 mt-5">
                                <div class="col-12">
                                    <h4 class="text-info border-bottom pb-2">Unmarried Daily Travel</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Requested Bus Name:</strong><br>
                                    {{ $bus_pass_application->requested_bus_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination location from AHQ:</strong><br>
                                    {{ $bus_pass_application->destination_from_ahq ?? 'N/A' }}
                                </div>
                            </div>
                        @endif

                        @if ($bus_pass_application->bus_pass_type === 'weekend_monthly_travel')
                            <div class="row mb-4 mt-5">
                                <div class="col-12">
                                    <h4 class="text-info border-bottom pb-2">Living in Person - Weekend/Monthly
                                        Traveling
                                    </h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Living in bus:</strong><br>
                                    {{ $bus_pass_application->living_in_bus ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination Location from AHQ:</strong><br>
                                    @if ($bus_pass_application->destinationLocation)
                                        {{ $bus_pass_application->destinationLocation->destination_location }}
                                    @elseif ($bus_pass_application->destination_location_ahq)
                                        {{ $bus_pass_application->destination_location_ahq }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Weekend Bus Name:</strong><br>
                                    {{ $bus_pass_application->weekend_bus_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination:</strong><br>
                                    {{ $bus_pass_application->weekend_destination ?? 'N/A' }}
                                </div>
                            </div>
                        @endif

                        @if ($bus_pass_application->bus_pass_type === 'living_in_only')
                            <div class="row mb-4 mt-5">
                                <div class="col-12">
                                    <h4 class="text-info border-bottom pb-2">Living in Bus only</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Living in bus:</strong><br>
                                    {{ $bus_pass_application->living_in_bus ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination Location from AHQ (Living in):</strong><br>
                                    @if ($bus_pass_application->destinationLocation)
                                        {{ $bus_pass_application->destinationLocation->destination_location }}
                                    @elseif ($bus_pass_application->destination_location_ahq)
                                        {{ $bus_pass_application->destination_location_ahq }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($bus_pass_application->bus_pass_type === 'weekend_only')
                            <div class="row mb-4 mt-5">
                                <div class="col-12">
                                    <h4 class="text-info border-bottom pb-2">Weekend only</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Weekend Bus Name:</strong><br>
                                    {{ $bus_pass_application->weekend_bus_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination Location from AHQ:</strong><br>
                                    {{ $bus_pass_application->weekend_destination ?? 'N/A' }}
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="card-body">
                        <!-- Documents -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Documents</h4>
                            </div>
                        </div>

                        <div class="row">
                            @if ($bus_pass_application->bus_pass_type !== 'living_in_only')
                                <div class="col-md-6">
                                    <strong>Grama Niladari Certificate:</strong><br>
                                    @if ($bus_pass_application->grama_niladari_certificate)
                                        <a href="{{ asset('storage/' . $bus_pass_application->grama_niladari_certificate) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            @endif
                            <div
                                class="col-md-{{ $bus_pass_application->bus_pass_type === 'living_in_only' ? '12' : '6' }}">
                                <strong>Person Image:</strong><br>
                                @if ($bus_pass_application->person_image)
                                    <a href="{{ asset('storage/' . $bus_pass_application->person_image) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-image"></i> View Image
                                    </a>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </div>
                        </div>

                        <!-- Rent Allowance Document (Conditional) -->
                        @if (
                            $bus_pass_application->bus_pass_type !== 'living_in_only' &&
                                $bus_pass_application->bus_pass_type !== 'unmarried_daily_travel' &&
                                !(
                                    $bus_pass_application->bus_pass_type === 'weekend_monthly_travel' &&
                                    $bus_pass_application->marital_status !== 'married'
                                ))
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <strong>Marriage Part II Order:</strong>
                                    <span class="text-info">(For Married Personnel - Not applicable for Living in Bus only
                                        and Unmarried Daily Travel)</span><br>
                                    @if ($bus_pass_application->marriage_part_ii_order)
                                        <a href="{{ asset('storage/' . $bus_pass_application->marriage_part_ii_order) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> View Document
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Permission Letter Document (Conditional) -->
                        @if (
                            $bus_pass_application->bus_pass_type === 'unmarried_daily_travel' ||
                                ($bus_pass_application->bus_pass_type === 'weekend_monthly_travel' &&
                                    $bus_pass_application->marital_status !== 'married'))
                            <div class="row mt-3">
                                @if (
                                    $bus_pass_application->bus_pass_type === 'unmarried_daily_travel' ||
                                        $bus_pass_application->marital_status !== 'married')
                                    <div class="col-md-12">
                                        <strong>Letter of Permission from the Head of Establishment:</strong>
                                        <span
                                            class="text-info">({{ $bus_pass_application->bus_pass_type === 'unmarried_daily_travel' ? 'For Unmarried Daily Travel only' : 'For Single Personnel with Weekend and Living in Travel' }})</span><br>
                                        @if ($bus_pass_application->permission_letter)
                                            <a href="{{ asset('storage/' . $bus_pass_application->permission_letter) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>

                    <div class="card-body">

                        <!-- Application Metadata -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Application Status</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <strong>Created By:</strong><br>
                                {{ $bus_pass_application->created_by }}
                            </div>
                            <div class="col-md-3">
                                <strong>Created Date:</strong><br>
                                {{ $bus_pass_application->created_at->format('d M Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Last Updated:</strong><br>
                                {{ $bus_pass_application->updated_at->format('d M Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                {!! $bus_pass_application->status_badge !!}
                            </div>
                        </div>

                        @if ($bus_pass_application->status === 'rejected')
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Rejected By:</strong><br>
                                    {{ $bus_pass_application->rejected_by ?? 'Unknown' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Rejected Date:</strong><br>
                                    {{ $bus_pass_application->rejected_at ? $bus_pass_application->rejected_at->format('d M Y H:i') : 'Unknown' }}
                                </div>
                            </div>

                            @if ($bus_pass_application->rejection_reason)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <strong>Rejection Reason:</strong><br>
                                        <div class="alert alert-danger">
                                            {{ $bus_pass_application->rejection_reason }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if ($bus_pass_application->remarks)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Remarks:</strong><br>
                                <div class="alert alert-info">
                                    {{ $bus_pass_application->remarks }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    @php
                        $showEditButton = false; // Default to false, only show for specific conditions
                        // Only Bus Pass Subject Clerk (Branch) should be able to edit applications
                        if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)')) {
                            // Show edit button only if status is 'pending_subject_clerk' (before forwarding)
                            if ($bus_pass_application->status === 'pending_subject_clerk') {
                                $showEditButton = true;
                            }
                        }
                    @endphp

                    @if ($showEditButton)
                        <a href="{{ route('bus-pass-applications.edit', $bus_pass_application) }}"
                            class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif
                    <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
