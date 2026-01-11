@extends('adminlte::page')

@section('title', 'Edit Civil Bus Pass Application')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-edit"></i> Edit Civil Bus Pass Application</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bus-pass-applications.index') }}">Bus Pass Applications</a>
                </li>
                <li class="breadcrumb-item active">Edit Civil Application</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-user-edit nav-icon"></i> {{ __('Edit Civil Bus Pass Application') }}
                            <div class="float-right">
                                <span class="badge badge-success mr-2">Civil Application</span>
                                <span
                                    class="badge badge-{{ $bus_pass_application->status === 'approved' ? 'success' : ($bus_pass_application->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ $bus_pass_application->getStatusLabel() }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('bus-pass-applications.update', $bus_pass_application) }}" method="POST"
                            enctype="multipart/form-data" id="busPassForm">
                            <input type="hidden" name="application_type" value="civil">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <!-- Personal Information Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Civil Person Information</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', $bus_pass_application->person->name) }}" required
                                                placeholder="Enter full name">
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nic">NIC Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nic') is-invalid @enderror"
                                                id="nic" name="nic"
                                                value="{{ old('nic', $bus_pass_application->person->nic) }}" required
                                                placeholder="Enter NIC number">
                                            @error('nic')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="civil_id">Civil ID<span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('civil_id') is-invalid @enderror" id="civil_id"
                                                name="civil_id"
                                                value="{{ old('civil_id', $bus_pass_application->person->civil_id) }}"
                                                required placeholder="Enter Civil ID">
                                            @error('civil_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="permanent_address">Permanent Address <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address"
                                                name="permanent_address" rows="3" required>{{ old('permanent_address', $bus_pass_application->person->permanent_address) }}</textarea>
                                            @error('permanent_address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="province_id">Province <span class="text-danger">*</span></label>
                                            <select class="form-control select2 @error('province_id') is-invalid @enderror"
                                                id="province_id" name="province_id" required>
                                                <option value="">Select Province</option>
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}"
                                                        {{ old('province_id', $bus_pass_application->person->province_id) == $province->id ? 'selected' : '' }}>
                                                        {{ $province->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('province_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="district_id">District <span class="text-danger">*</span></label>
                                            <select class="form-control select2 @error('district_id') is-invalid @enderror"
                                                id="district_id" name="district_id" required>
                                                <option value="">Select District</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        {{ old('district_id', $bus_pass_application->person->district_id) == $district->id ? 'selected' : '' }}>
                                                        {{ $district->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('district_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telephone_no">Telephone No <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('telephone_no') is-invalid @enderror"
                                                id="telephone_no" name="telephone_no"
                                                value="{{ old('telephone_no', $bus_pass_application->person->telephone_no) }}"
                                                required>
                                            @error('telephone_no')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gs_division_id">Grama Seva Division <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-control select2 @error('gs_division_id') is-invalid @enderror"
                                                id="gs_division_id" name="gs_division_id" required>
                                                <option value="">Select Grama Seva Division</option>
                                                @foreach ($gsDivisions as $gsDivision)
                                                    <option value="{{ $gsDivision->id }}"
                                                        {{ old('gs_division_id', $bus_pass_application->person->gs_division_id) == $gsDivision->id ? 'selected' : '' }}>
                                                        {{ $gsDivision->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('gs_division_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="police_station_id">Nearest Police Station <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-control select2 @error('police_station_id') is-invalid @enderror"
                                                id="police_station_id" name="police_station_id" required>
                                                <option value="">Select Police Station</option>
                                                @foreach ($policeStations as $policeStation)
                                                    <option value="{{ $policeStation->id }}"
                                                        {{ old('police_station_id', $bus_pass_application->person->police_station_id) == $policeStation->id ? 'selected' : '' }}>
                                                        {{ $policeStation->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('police_station_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Establishment (Select2 Dropdown) -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="establishment_id">Establishment <span
                                                    class="text-danger">*</span></label>
                                            @if (auth()->user()->isBranchUser())
                                                <!-- Branch users see their establishment locked -->
                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->establishment ? auth()->user()->establishment->name : 'Not assigned' }}"
                                                    readonly>
                                                <input type="hidden" name="establishment_id"
                                                    value="{{ auth()->user()->establishment_id }}">
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-lock"></i> Your establishment is automatically
                                                    assigned.
                                                </small>
                                            @else
                                                <!-- System admin and movement users can select establishment -->
                                                <select
                                                    class="form-control @error('establishment_id') is-invalid @enderror"
                                                    id="establishment_id" name="establishment_id" required>
                                                    <option value="">Select Establishment</option>
                                                    @foreach ($establishment as $est)
                                                        <option value="{{ $est->id }}"
                                                            {{ old('establishment_id', $bus_pass_application->establishment_id) == $est->id ? 'selected' : '' }}>
                                                            {{ $est->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Select the Establishment from the available options.
                                                </small>
                                            @endif
                                            @error('establishment_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_arrival_ahq">Date of Arrival at AHQ <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('date_arrival_ahq') is-invalid @enderror"
                                                id="date_arrival_ahq" name="date_arrival_ahq"
                                                value="{{ old('date_arrival_ahq', $bus_pass_application->date_arrival_ahq ? $bus_pass_application->date_arrival_ahq->format('Y-m-d') : '') }}"
                                                max="{{ date('Y-m-d') }}" required>
                                            @error('date_arrival_ahq')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Application Information Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Application Information</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="obtain_sltb_season">Obtained SLTB Season <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('obtain_sltb_season') is-invalid @enderror"
                                                id="obtain_sltb_season" name="obtain_sltb_season" required>
                                                <option value="">Select</option>
                                                <option value="yes"
                                                    {{ old('obtain_sltb_season', $bus_pass_application->obtain_sltb_season) == 'yes' ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="no"
                                                    {{ old('obtain_sltb_season', $bus_pass_application->obtain_sltb_season) == 'no' ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                            @error('obtain_sltb_season')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bus_pass_type">Bus Pass Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('bus_pass_type') is-invalid @enderror"
                                                id="bus_pass_type" name="bus_pass_type" required>
                                                <option value="">Select Type</option>
                                                <option value="daily_travel"
                                                    {{ old('bus_pass_type', $bus_pass_application->bus_pass_type) == 'daily_travel' ? 'selected' : '' }}>
                                                    Daily Travel</option>
                                                <option value="weekend_only"
                                                    {{ old('bus_pass_type', $bus_pass_application->bus_pass_type) == 'weekend_only' ? 'selected' : '' }}>
                                                    Weekend Only</option>
                                            </select>
                                            @error('bus_pass_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Branch Card Availability Section -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="branch_card_availability">Branch Card Availability <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('branch_card_availability') is-invalid @enderror"
                                                id="branch_card_availability" name="branch_card_availability" required>
                                                <option value="">Select</option>
                                                <option value="has_branch_card"
                                                    {{ old('branch_card_availability', $bus_pass_application->branch_card_availability) == 'has_branch_card' ? 'selected' : '' }}>
                                                    Yes</option>
                                                <option value="no_branch_card"
                                                    {{ old('branch_card_availability', $bus_pass_application->branch_card_availability) == 'no_branch_card' ? 'selected' : '' }}>
                                                    No</option>
                                            </select>
                                            @error('branch_card_availability')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="branch_card_id_section" style="display: none;">
                                        <div class="form-group">
                                            <label for="branch_card_id">Branch Card ID <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('branch_card_id') is-invalid @enderror"
                                                    id="branch_card_id" name="branch_card_id"
                                                    value="{{ old('branch_card_id', $bus_pass_application->branch_card_id) }}"
                                                    placeholder="Enter branch card ID">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="verify_branch_card">
                                                        <i class="fas fa-check-circle"></i> Verify
                                                    </button>
                                                </div>
                                            </div>
                                            @error('branch_card_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="verification_status_section" style="display: none;">
                                        <div class="form-group">
                                            <label>Verification Status</label>
                                            <div id="verification_status" class="mt-2">
                                                <!-- Status will be displayed here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional sections based on bus pass type -->
                                <div id="daily_travel_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Living out Person - Daily Traveling</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="requested_bus_name">Requested Bus Name</label>
                                                <select class="form-control" id="requested_bus_name"
                                                    name="requested_bus_name">
                                                    <option value="">Select Bus</option>
                                                    @if (isset($busRoutes))
                                                        @foreach ($busRoutes as $route)
                                                            <option value="{{ $route->name }}"
                                                                {{ old('requested_bus_name', $bus_pass_application->requested_bus_name) == $route->name ? 'selected' : '' }}>
                                                                {{ $route->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_from_ahq">Destination location from AHQ</label>
                                                <input type="text" class="form-control" id="destination_from_ahq"
                                                    name="destination_from_ahq"
                                                    value="{{ old('destination_from_ahq', $bus_pass_application->destination_from_ahq) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="weekend_only_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Weekend Only</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_bus_name_only">Weekend Bus Name</label>
                                                <select class="form-control" id="weekend_bus_name_only"
                                                    name="weekend_bus_name">
                                                    <option value="">Select Bus</option>
                                                    @if (isset($busRoutes))
                                                        @foreach ($busRoutes as $route)
                                                            <option value="{{ $route->name }}"
                                                                {{ old('weekend_bus_name', $bus_pass_application->weekend_bus_name) == $route->name ? 'selected' : '' }}>
                                                                {{ $route->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_destination_only">Destination Location from
                                                    AHQ</label>
                                                <input type="text" class="form-control" id="weekend_destination_only"
                                                    name="weekend_destination"
                                                    value="{{ old('weekend_destination', $bus_pass_application->weekend_destination) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Uploads Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Documents</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="grama_niladari_certificate">Grama Niladari Certificate</label>
                                            @if ($bus_pass_application->grama_niladari_certificate)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/' . $bus_pass_application->grama_niladari_certificate) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-file-pdf"></i> View Current Document
                                                    </a>
                                                </div>
                                            @endif
                                            <input type="file"
                                                class="form-control-file @error('grama_niladari_certificate') is-invalid @enderror"
                                                id="grama_niladari_certificate" name="grama_niladari_certificate"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max:
                                                10MB). Leave empty to keep current document.</small>
                                            @error('grama_niladari_certificate')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="person_image">Person Image</label>
                                            @if ($bus_pass_application->person_image)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/' . $bus_pass_application->person_image) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-image"></i> View Current Image
                                                    </a>
                                                </div>
                                            @endif
                                            <input type="file"
                                                class="form-control-file @error('person_image') is-invalid @enderror"
                                                id="person_image" name="person_image" accept=".jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: JPG, PNG (Max:
                                                5MB). Leave empty to keep current image.</small>
                                            @error('person_image')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Declarations Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Declarations</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('declaration_1') is-invalid @enderror"
                                                    id="declaration_1" name="declaration_1" value="yes" required
                                                    {{ old('declaration_1', $bus_pass_application->declaration_1) == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="declaration_1">
                                                    The applicant have declared that the information provided above is true
                                                    and correct to the
                                                    best of his/her knowledge. <span class="text-danger">*</span>
                                                </label>
                                                @error('declaration_1')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('declaration_2') is-invalid @enderror"
                                                    id="declaration_2" name="declaration_2" value="yes" required
                                                    {{ old('declaration_2', $bus_pass_application->declaration_2) == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="declaration_2">
                                                    The applicant understands that any false information may result in the
                                                    rejection of
                                                    this application and/or disciplinary action. <span
                                                        class="text-danger">*</span>
                                                </label>
                                                @error('declaration_2')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Application
                                </button>
                                <a href="{{ route('bus-pass-applications.show', $bus_pass_application) }}"
                                    class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to View
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        /* Select2 styling to match your form */
        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--bootstrap4 .select2-selection:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Center align text vertically in Select2 dropdowns */
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            display: flex;
            align-items: center;
            height: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 100%;
            display: flex;
            align-items: center;
        }

        /* Branch Card Verification Styling */
        #branch_card_id_section,
        #verification_status_section {
            transition: all 0.3s ease-in-out;
        }

        #verification_status .badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        #verification_status .badge-success {
            animation: pulse-success 2s infinite;
        }

        @keyframes pulse-success {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .input-group .btn {
            border-left: 0;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for all dropdowns
            $('#province_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Province',
                allowClear: true,
                width: '100%',
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text.localeCompare(b.text);
                    });
                }
            });

            $('#district_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select District',
                allowClear: true,
                width: '100%',
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text.localeCompare(b.text);
                    });
                }
            });

            $('#police_station_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Police Station',
                allowClear: true,
                width: '100%',
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text.localeCompare(b.text);
                    });
                }
            });

            $('#gs_division_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Grama Seva Division',
                allowClear: true,
                width: '100%',
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text.localeCompare(b.text);
                    });
                }
            });

            // Initialize Select2 for Establishment dropdown (only for non-branch users)
            @if (!auth()->user()->isBranchUser())
                $('#establishment_id').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select Establishment',
                    allowClear: true,
                    width: '100%',
                    sorter: function(data) {
                        return data.sort(function(a, b) {
                            return a.text.localeCompare(b.text);
                        });
                    }
                });
            @endif

            // Bus pass type change handler
            $('#bus_pass_type').change(function() {
                var type = $(this).val();
                $('#daily_travel_section').hide();
                $('#weekend_only_section').hide();

                if (type === 'daily_travel') {
                    $('#daily_travel_section').show();
                } else if (type === 'weekend_only') {
                    $('#weekend_only_section').show();
                }
            });

            // Trigger on page load if value exists
            if ($('#bus_pass_type').val()) {
                $('#bus_pass_type').trigger('change');
            }

            // Branch Card Availability functionality
            let branchCardVerified = {{ $bus_pass_application->branch_card_id ? 'true' : 'false' }};

            // Function to handle branch card availability state
            function handleBranchCardAvailability() {
                const selectedValue = $('#branch_card_availability').val();
                const branchCardSection = $('#branch_card_id_section');
                const statusSection = $('#verification_status_section');

                if (selectedValue === 'has_branch_card') {
                    branchCardSection.show();
                    statusSection.show();

                    // Show appropriate status message
                    const branchCardId = $('#branch_card_id').val().trim();
                    if (branchCardId && branchCardVerified) {
                        $('#verification_status').html(
                            '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Verified</span>'
                        );
                    } else if (branchCardId) {
                        $('#verification_status').html(
                            '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Requires Verification</span>'
                        );
                    } else {
                        $('#verification_status').html('<span class="badge badge-warning">Not Verified</span>');
                    }
                } else {
                    branchCardSection.hide();
                    statusSection.hide();
                    branchCardVerified = true; // No verification needed if no branch card
                    $('#verification_status').html('');
                }
            }

            // Check initial state on page load
            handleBranchCardAvailability();

            // Handle dropdown change
            $('#branch_card_availability').on('change', function() {
                handleBranchCardAvailability();
            });

            // Handle civil ID change - clear branch card ID
            $('#civil_id').on('input', function() {
                // Clear branch card ID when civil ID changes
                $('#branch_card_id').val('');
                branchCardVerified = false;
                $('#verification_status').html('<span class="badge badge-warning">Not Verified</span>');
            });

            // Handle branch card ID input change - reset verification status
            $('#branch_card_id').on('input', function() {
                if ($('#branch_card_availability').val() === 'has_branch_card') {
                    branchCardVerified = false;
                    const branchCardId = $(this).val().trim();
                    if (branchCardId) {
                        $('#verification_status').html(
                            '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Requires Verification</span>'
                        );
                    } else {
                        $('#verification_status').html(
                            '<span class="badge badge-warning">Not Verified</span>');
                    }
                }
            });

            // Branch Card Verification
            $('#verify_branch_card').on('click', function() {
                const civilId = $('#civil_id').val().trim();
                const branchCardId = $('#branch_card_id').val().trim();

                if (!civilId) {
                    alert('Please fill in Civil ID first.');
                    return;
                }

                if (civilId === '0') {
                    alert('Civil ID cannot be 0. Please enter a valid Civil ID.');
                    return;
                }

                if (!branchCardId) {
                    alert('Please enter Branch Card ID.');
                    return;
                }

                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                $('#verification_status').html('<span class="badge badge-info">Verifying...</span>');

                // API call to verify branch card through backend proxy (civil applications use civil_id)
                $.ajax({
                    url: '{{ route('bus-pass-applications.verify-branch-card') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        service_no: civilId, // Use civil_id as service_no for civil personnel
                        ser_no: branchCardId
                    },
                    success: function(response) {
                        // Check if person data exists and is verified
                        if (response.person && response.person.length > 0) {
                            const personData = response.person[0];

                            // Check if the person is active and approved
                            if (personData.active === 1 && personData.approved === 3 &&
                                personData.approval_card === 'Approved' && personData
                                .status_card === 'Active') {
                                $('#verification_status').html(
                                    '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Verified!</span>'
                                );
                                branchCardVerified = true;

                                // Show additional info for verification
                                console.log('Branch Card Verified:', {
                                    name: personData.name_with_initial,
                                    rank: personData.rank,
                                    unit: personData.unit,
                                    establishment: personData.establishments
                                });
                            } else {
                                $('#verification_status').html(
                                    '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Card Not Active/Approved</span>'
                                );
                                branchCardVerified = false;
                                alert(
                                    'Branch card is not active or approved. Please check with your unit.'
                                );
                            }
                        } else {
                            // No person data found
                            $('#verification_status').html(
                                '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Not Verified</span>'
                            );
                            branchCardVerified = false;
                            alert(
                                'Branch card not found. Please check your Civil ID and Branch Card ID.'
                            );
                        }
                    },
                    error: function(xhr) {
                        $('#verification_status').html(
                            '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Verification Failed</span>'
                        );
                        branchCardVerified = false;

                        let errorMsg = 'Branch card verification failed. Please try again.';
                        if (xhr.status === 0) {
                            errorMsg = 'Network error. Please check your internet connection.';
                        } else if (xhr.status >= 500) {
                            errorMsg = 'Server error. Please try again later.';
                        }
                        alert(errorMsg);
                    },
                    complete: function() {
                        $('#verify_branch_card').prop('disabled', false).html(
                            '<i class="fas fa-check-circle"></i> Verify');
                    }
                });
            });

            // Handle form submission - disable fields in hidden sections
            $('form').on('submit', function(e) {
                // Check branch card verification if required
                if ($('#branch_card_availability').val() === 'has_branch_card' && !branchCardVerified) {
                    e.preventDefault();
                    alert('Please verify your branch card before submitting the application.');
                    return false;
                }

                // Disable all form fields in hidden sections to prevent submission of empty values
                $('#daily_travel_section:hidden input, #daily_travel_section:hidden select').prop(
                    'disabled', true);
                $('#weekend_only_section:hidden input, #weekend_only_section:hidden select').prop(
                    'disabled', true);
            });
        });
    </script>
@endpush
