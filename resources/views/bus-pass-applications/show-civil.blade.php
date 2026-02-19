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
                        <i class="nav-icon fas fa-id-card nav-icon"></i> {{ __('Civil Bus Pass Application Details') }}
                        <div class="float-right">
                            <span class="badge badge-success mr-2">Civil Application</span>
                            <span
                                class="badge badge-{{ $bus_pass_application->status === 'approved' ? 'success' : ($bus_pass_application->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ $bus_pass_application->getStatusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Civil Person Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Civil Person Information</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Full Name:</strong><br>
                                {{ $bus_pass_application->person->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>NIC Number:</strong><br>
                                {{ $bus_pass_application->person->nic }}
                            </div>
                            <div class="col-md-4">
                                <strong>Civil ID:</strong><br>
                                {{ $bus_pass_application->person->civil_id }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Permanent Address:</strong><br>
                                {{ $bus_pass_application->person->permanent_address }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Telephone No:</strong><br>
                                {{ $bus_pass_application->person->telephone_no }}
                            </div>
                            <div class="col-md-4">
                                <strong>Blood Group:</strong><br>
                                {{ $bus_pass_application->person->blood_group ?: 'Not specified' }}
                            </div>
                            @if (!Auth::user()->hasRole('Subject Clerk (DMOV)'))
                                <div class="col-md-4">
                                    <strong>NOK Name:</strong><br>
                                    {{ $bus_pass_application->person->nok_name ?: 'Not specified' }}
                                </div>
                            @endif
                        </div>

                        <div class="row mt-3">
                            @if (!Auth::user()->hasRole('Subject Clerk (DMOV)'))
                                <div class="col-md-4">
                                    <strong>NOK Telephone No:</strong><br>
                                    {{ $bus_pass_application->person->nok_telephone_no ?: 'Not specified' }}
                                </div>
                            @endif
                            <div class="col-md-4">
                                <strong>Province:</strong><br>
                                {{ $bus_pass_application->person->province->name ?? 'Not specified' }}
                            </div>
                            <div class="col-md-4">
                                <strong>District:</strong><br>
                                {{ $bus_pass_application->person->district->name ?? 'Not specified' }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Grama Seva Division:</strong><br>
                                {{ $bus_pass_application->person->gsDivision->name ?? 'Not specified' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Nearest Police Station:</strong><br>
                                {{ $bus_pass_application->person->policeStation->name ?? 'Not specified' }}
                            </div>
                        </div>

                        <!-- Application Information -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Application Information</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Establishment:</strong><br>
                                @if ($bus_pass_application->establishment)
                                    <span class="badge badge-info">{{ $bus_pass_application->establishment->name }}</span>
                                @else
                                    <span
                                        class="text-muted">{{ $bus_pass_application->branch_directorate ?? 'Not specified' }}</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Date of Arrival at AHQ:</strong><br>
                                {{ $bus_pass_application->date_arrival_ahq ? \Carbon\Carbon::parse($bus_pass_application->date_arrival_ahq)->format('d M Y') : 'Not specified' }}
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Bus Pass Type:</strong><br>
                                <span class="badge badge-info">{{ $bus_pass_application->type_label }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Obtained SLTB Season:</strong><br>
                                <span
                                    class="badge badge-{{ $bus_pass_application->obtain_sltb_season === 'yes' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($bus_pass_application->obtain_sltb_season) }}
                                </span>
                            </div>
                        </div>

                        <!-- Branch Card Information -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Branch Card Availability:</strong><br>
                                <span
                                    class="badge badge-{{ $bus_pass_application->branch_card_availability === 'has_branch_card' ? 'success' : 'secondary' }}">
                                    {{ $bus_pass_application->branch_card_availability === 'has_branch_card' ? 'Has Branch Card' : 'No Branch Card' }}
                                </span>
                            </div>
                            @if ($bus_pass_application->branch_card_availability === 'has_branch_card' && $bus_pass_application->branch_card_id)
                                <div class="col-md-6">
                                    <strong>Branch Card ID:</strong><br>
                                    {{ $bus_pass_application->branch_card_id }}
                                </div>
                            @endif
                        </div>

                        <!-- Travel Information (Conditional based on bus pass type) -->
                        @if (
                            $bus_pass_application->bus_pass_type === 'daily_travel' ||
                                $bus_pass_application->bus_pass_type === 'unmarried_daily_travel')
                            <div class="row mb-4 mt-4">
                                <div class="col-12">
                                    <h4 class="text-success border-bottom pb-2">Daily Travel Information</h4>
                                </div>
                            </div>

                            <div class="row">
                                @if ($bus_pass_application->requested_bus_name)
                                    <div class="col-md-6">
                                        <strong>Requested Bus Name:</strong><br>
                                        {{ $bus_pass_application->requested_bus_name }}
                                    </div>
                                @endif
                                @if ($bus_pass_application->destination_from_ahq)
                                    <div class="col-md-6">
                                        <strong>Destination from AHQ:</strong><br>
                                        {{ $bus_pass_application->destination_from_ahq }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($bus_pass_application->bus_pass_type === 'weekend_monthly_travel')
                            <div class="row mb-4 mt-4">
                                <div class="col-12">
                                    <h4 class="text-success border-bottom pb-2">Weekend/Monthly Travel Information</h4>
                                </div>
                            </div>

                            <div class="row">
                                @if ($bus_pass_application->living_in_bus)
                                    <div class="col-md-6">
                                        <strong>Living in Bus:</strong><br>
                                        {{ $bus_pass_application->living_in_bus }}
                                    </div>
                                @endif
                                @if ($bus_pass_application->destination_location_ahq)
                                    <div class="col-md-6">
                                        <strong>Destination Location from AHQ:</strong><br>
                                        {{ $bus_pass_application->destination_location_ahq }}
                                    </div>
                                @endif
                            </div>

                            <div class="row mt-3">
                                @if ($bus_pass_application->weekend_bus_name)
                                    <div class="col-md-6">
                                        <strong>Weekend Bus Name:</strong><br>
                                        {{ $bus_pass_application->weekend_bus_name }}
                                    </div>
                                @endif
                                @if ($bus_pass_application->weekend_destination)
                                    <div class="col-md-6">
                                        <strong>Weekend Destination:</strong><br>
                                        {{ $bus_pass_application->weekend_destination }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Documents Section -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Documents</h4>
                            </div>
                        </div>

                        <div class="row">
                            @if ($bus_pass_application->grama_niladari_certificate)
                                <div class="col-md-4">
                                    <strong>Grama Niladari Certificate:</strong><br>
                                    <a href="{{ asset('storage/' . $bus_pass_application->grama_niladari_certificate) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> View Document
                                    </a>
                                </div>
                            @endif

                            @if ($bus_pass_application->person_image)
                                <div class="col-md-4">
                                    <strong>Person Image:</strong><br>
                                    <a href="{{ asset('storage/' . $bus_pass_application->person_image) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-image"></i> View Image
                                    </a>
                                </div>
                            @endif

                            @if (
                                $bus_pass_application->marriage_part_ii_order &&
                                    !(
                                        $bus_pass_application->bus_pass_type === 'weekend_monthly_travel' &&
                                        $bus_pass_application->marital_status !== 'married'
                                    ))
                                <div class="col-md-4">
                                    <strong>Marriage Part II Order:</strong><br>
                                    <a href="{{ asset('storage/' . $bus_pass_application->marriage_part_ii_order) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> View Document
                                    </a>
                                </div>
                            @endif

                            @if (
                                $bus_pass_application->permission_letter &&
                                    ($bus_pass_application->bus_pass_type === 'unmarried_daily_travel' ||
                                        ($bus_pass_application->bus_pass_type === 'weekend_monthly_travel' &&
                                            $bus_pass_application->marital_status !== 'married')))
                                <div class="col-md-4">
                                    <strong>Letter of Permission from the Head of Establishment:</strong><br>
                                    <a href="{{ asset('storage/' . $bus_pass_application->permission_letter) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> View Document
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Declarations -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Declarations</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" disabled
                                        {{ $bus_pass_application->declaration_1 === 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        The applicant has declared that the information provided above is true and correct
                                        to the best of his/her knowledge.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" disabled
                                        {{ $bus_pass_application->declaration_2 === 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        The applicant understands that any false information may result in the rejection of
                                        this application and/or disciplinary action.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status & Timeline -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h4 class="text-primary border-bottom pb-2">Application Status</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Current Status:</strong><br>
                                {!! $bus_pass_application->status_badge !!}
                            </div>
                            <div class="col-md-4">
                                <strong>Applied Date:</strong><br>
                                {{ $bus_pass_application->created_at->format('d M Y, h:i A') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Created By:</strong><br>
                                {{ $bus_pass_application->created_by ?? 'System' }}
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
                                    {{ $bus_pass_application->rejected_at ? $bus_pass_application->rejected_at->format('d M Y, h:i A') : 'Unknown' }}
                                </div>
                            </div>

                            @if ($bus_pass_application->rejection_reason || $bus_pass_application->remarks)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <strong>Rejection Reason:</strong><br>
                                        <div class="alert alert-danger">
                                            {{ $bus_pass_application->rejection_reason ?: $bus_pass_application->remarks }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>

                    @if ($bus_pass_application->remarks && $bus_pass_application->status !== 'rejected')
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Remarks:</strong><br>
                                <div class="alert alert-info">
                                    {{ $bus_pass_application->remarks }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card-footer">
                        @php
                            $showFooterButtons = true; // Default to true, hide when from emergency details
                            $fromParam = request('from');
                            if ($fromParam === 'emergency-details' || $fromParam === 'onboarded-passengers') {
                                $showFooterButtons = false; // Hide all footer buttons
                            }
                        @endphp

                        @if ($showFooterButtons)
                            <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Applications
                            </a>

                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)') &&
                                    $bus_pass_application->status === 'pending_subject_clerk')
                                <a href="{{ route('bus-pass-applications.edit', $bus_pass_application->id) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Application
                                </a>
                            @endif

                            @if (
                                $bus_pass_application->status === 'approved_for_integration' ||
                                    $bus_pass_application->status === 'approved_for_temp_card')
                                <button class="btn btn-success" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Application
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')
@endsection

@section('css')
    <style>
        @media print {

            .card-footer,
            .btn {
                display: none !important;
            }
        }
    </style>
@stop
