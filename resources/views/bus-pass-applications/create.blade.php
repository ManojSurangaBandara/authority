@extends('adminlte::page')

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
                            <i class="nav-icon fas fa-id-card nav-icon"></i> {{ __('New Bus Pass Application') }}
                        </div>

                        <form action="{{ route('bus-pass-applications.store') }}" method="POST"
                            enctype="multipart/form-data" id="busPassForm">
                            @csrf
                            <div class="card-body">
                                <!-- Personal Information Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Personal Information</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="regiment_no">Regiment No <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('regiment_no') is-invalid @enderror"
                                                    id="regiment_no" name="regiment_no" value="{{ old('regiment_no') }}"
                                                    required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="fetch-details">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('regiment_no')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rank">Rank <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('rank') is-invalid @enderror"
                                                id="rank" name="rank" value="{{ old('rank') }}" required readonly
                                                style="background-color: #f8f9fa;"
                                                placeholder="Auto-filled from Regiment No search">
                                            @error('rank')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Auto-filled from Regiment No search
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required readonly
                                                style="background-color: #f8f9fa;"
                                                placeholder="Auto-filled from Regiment No search">
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Auto-filled from Regiment No search
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit">Unit <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                                id="unit" name="unit" value="{{ old('unit') }}" required readonly
                                                style="background-color: #f8f9fa;"
                                                placeholder="Auto-filled from Regiment No search">
                                            @error('unit')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Auto-filled from Regiment No search
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nic">NIC <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nic') is-invalid @enderror"
                                                id="nic" name="nic" value="{{ old('nic') }}" required readonly
                                                style="background-color: #f8f9fa;"
                                                placeholder="Auto-filled from Regiment No search">
                                            @error('nic')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Auto-filled from Regiment No search
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="army_id">Army ID <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('army_id') is-invalid @enderror" id="army_id"
                                                name="army_id" value="{{ old('army_id') }}" required>
                                            @error('army_id')
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
                                                name="permanent_address" rows="3" required>{{ old('permanent_address') }}</textarea>
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
                                                        {{ old('province_id') == $province->id ? 'selected' : '' }}>
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
                                                        {{ old('district_id') == $district->id ? 'selected' : '' }}>
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
                                                id="telephone_no" name="telephone_no" value="{{ old('telephone_no') }}"
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
                                                        {{ old('gs_division_id') == $gsDivision->id ? 'selected' : '' }}>
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
                                                        {{ old('police_station_id') == $policeStation->id ? 'selected' : '' }}>
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
                                                            {{ old('establishment_id') == $est->id ? 'selected' : '' }}>
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
                                            <label for="marital_status">Marital Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('marital_status') is-invalid @enderror"
                                                id="marital_status" name="marital_status" required>
                                                <option value="">Select Status</option>
                                                <option value="single"
                                                    {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single
                                                </option>
                                                <option value="married"
                                                    {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married
                                                </option>
                                            </select>
                                            @error('marital_status')
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
                                                value="{{ old('date_arrival_ahq') }}" required>
                                            @error('date_arrival_ahq')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Application Specific Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="text-primary mb-3">Application Information</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="approval_living_out">Approval for Living Out <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('approval_living_out') is-invalid @enderror"
                                                id="approval_living_out" name="approval_living_out" required>
                                                <option value="">Select</option>
                                                <option value="yes"
                                                    {{ old('approval_living_out') == 'yes' ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="no"
                                                    {{ old('approval_living_out') == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                            @error('approval_living_out')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="obtain_sltb_season">Obtained SLTB Season <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('obtain_sltb_season') is-invalid @enderror"
                                                id="obtain_sltb_season" name="obtain_sltb_season" required>
                                                <option value="">Select</option>
                                                <option value="yes"
                                                    {{ old('obtain_sltb_season') == 'yes' ? 'selected' : '' }}>Yes</option>
                                                <option value="no"
                                                    {{ old('obtain_sltb_season') == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                            @error('obtain_sltb_season')
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
                                                    {{ old('branch_card_availability') == 'has_branch_card' ? 'selected' : '' }}>
                                                    Yes</option>
                                                <option value="no_branch_card"
                                                    {{ old('branch_card_availability') == 'no_branch_card' ? 'selected' : '' }}>
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
                                                    value="{{ old('branch_card_id') }}"
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

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bus_pass_type">Bus Pass Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('bus_pass_type') is-invalid @enderror"
                                                id="bus_pass_type" name="bus_pass_type" required>
                                                <option value="">Select Type</option>
                                                <option value="daily_travel"
                                                    {{ old('bus_pass_type') == 'daily_travel' ? 'selected' : '' }}>Daily
                                                    Travel (Living out)</option>
                                                <option value="weekend_monthly_travel"
                                                    {{ old('bus_pass_type') == 'weekend_monthly_travel' ? 'selected' : '' }}>
                                                    Weekend and Living in Bus</option>
                                                <option value="living_in_only"
                                                    {{ old('bus_pass_type') == 'living_in_only' ? 'selected' : '' }}>Living
                                                    in Bus only</option>
                                                <option value="weekend_only"
                                                    {{ old('bus_pass_type') == 'weekend_only' ? 'selected' : '' }}>Weekend
                                                    only</option>
                                                <option value="unmarried_daily_travel"
                                                    {{ old('bus_pass_type') == 'unmarried_daily_travel' ? 'selected' : '' }}>
                                                    Unmarried
                                                    Daily Travel</option>
                                            </select>
                                            @error('bus_pass_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                                                {{ old('requested_bus_name') == $route->name ? 'selected' : '' }}>
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
                                                    value="{{ old('destination_from_ahq') }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div id="unmarried_daily_travel_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Unmarried Daily Travel</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="requested_bus_name_unmarried">Requested Bus Name</label>
                                                <select class="form-control" id="requested_bus_name_unmarried"
                                                    name="requested_bus_name">
                                                    <option value="">Select Bus</option>
                                                    @if (isset($busRoutes))
                                                        @foreach ($busRoutes as $route)
                                                            <option value="{{ $route->name }}"
                                                                {{ old('requested_bus_name') == $route->name ? 'selected' : '' }}>
                                                                {{ $route->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_from_ahq_unmarried">Destination location from
                                                    AHQ</label>
                                                <input type="text" class="form-control"
                                                    id="destination_from_ahq_unmarried" name="destination_from_ahq"
                                                    value="{{ old('destination_from_ahq') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="weekend_monthly_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Living in Person - Weekend/Monthly Traveling</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="living_in_bus">Living in bus</label>
                                                <select class="form-control" id="living_in_bus" name="living_in_bus">
                                                    <option value="">Select Living in bus</option>
                                                    @if (isset($livingInBuses))
                                                        @foreach ($livingInBuses as $bus)
                                                            <option value="{{ $bus->name }}"
                                                                {{ old('living_in_bus') == $bus->name ? 'selected' : '' }}>
                                                                {{ $bus->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_location_ahq">Destination Location from AHQ</label>
                                                <select class="form-control" id="destination_location_ahq"
                                                    name="destination_location_ahq">
                                                    <option value="">Select Destination Location</option>
                                                    @foreach ($destinationLocations as $location)
                                                        <option value="{{ $location->destination_location }}"
                                                            {{ old('destination_location_ahq') == $location->destination_location ? 'selected' : '' }}>
                                                            {{ $location->destination_location }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_bus_name">Weekend Bus Name</label>
                                                <select class="form-control" id="weekend_bus_name"
                                                    name="weekend_bus_name">
                                                    <option value="">Select Bus</option>
                                                    @if (isset($busRoutes))
                                                        @foreach ($busRoutes as $route)
                                                            <option value="{{ $route->name }}"
                                                                {{ old('weekend_bus_name') == $route->name ? 'selected' : '' }}>
                                                                {{ $route->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_destination">Destination</label>
                                                <input type="text" class="form-control" id="weekend_destination"
                                                    name="weekend_destination" value="{{ old('weekend_destination') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="living_in_only_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Living in Bus only</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="living_in_bus_only">Living in bus</label>
                                                <select class="form-control" id="living_in_bus_only"
                                                    name="living_in_bus">
                                                    <option value="">Select Living in bus</option>
                                                    @if (isset($livingInBuses))
                                                        @foreach ($livingInBuses as $bus)
                                                            <option value="{{ $bus->name }}"
                                                                {{ old('living_in_bus') == $bus->name ? 'selected' : '' }}>
                                                                {{ $bus->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_location_living_in">Destination Location from AHQ
                                                    (Living in)</label>
                                                <select class="form-control" id="destination_location_living_in"
                                                    name="destination_location_ahq">
                                                    <option value="">Select Destination Location</option>
                                                    <!-- Options will be populated from destination_locations table -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="weekend_only_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-info mb-3">Weekend only</h5>
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
                                                                {{ old('weekend_bus_name') == $route->name ? 'selected' : '' }}>
                                                                {{ $route->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_destination_only">Destination Location from AHQ</label>
                                                <input type="text" class="form-control" id="weekend_destination_only"
                                                    name="weekend_destination" value="{{ old('weekend_destination') }}">
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

                                <!-- Rent Allowance Document (Conditional) -->
                                <div class="row" id="rent_allowance_section" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="marriage_part_ii_order_daily">Marriage Part II Order
                                                <span class="text-info">(For Married Personnel - Not applicable for Living
                                                    in Bus only)</span></label>
                                            <input type="file"
                                                class="form-control-file @error('marriage_part_ii_order') is-invalid @enderror"
                                                id="marriage_part_ii_order_daily" name="marriage_part_ii_order"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max:
                                                10MB)</small>
                                            @error('marriage_part_ii_order')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Permission Letter Document (Conditional) -->
                                <div class="row" id="permission_letter_section" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="permission_letter">Letter of Permission from the Head of
                                                Establishment
                                                <span class="text-info">(For Unmarried Daily Travel only)</span></label>
                                            <input type="file"
                                                class="form-control-file @error('permission_letter') is-invalid @enderror"
                                                id="permission_letter" name="permission_letter"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max:
                                                10MB)</small>
                                            @error('permission_letter')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6" id="grama_niladari_section">
                                        <div class="form-group">
                                            <label for="grama_niladari_certificate">Grama Niladari Certificate</label>
                                            <input type="file"
                                                class="form-control-file @error('grama_niladari_certificate') is-invalid @enderror"
                                                id="grama_niladari_certificate" name="grama_niladari_certificate"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max:
                                                10MB)</small>
                                            @error('grama_niladari_certificate')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="person_image">Person Image</label>
                                            <input type="file"
                                                class="form-control-file @error('person_image') is-invalid @enderror"
                                                id="person_image" name="person_image" accept=".jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: JPG, PNG (Max:
                                                5MB)</small>
                                            @error('person_image')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- Declarations Section -->
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
                                                    {{ old('declaration_1') == 'yes' ? 'checked' : '' }}>
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
                                                    {{ old('declaration_2') == 'yes' ? 'checked' : '' }}>
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
                                    <i class="fas fa-save"></i> Submit Application
                                </button>
                                <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
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
            $('#rank_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Rank',
                allowClear: true,
                width: '100%'
            });

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

            // Function to update bus pass type options based on marital status and approval for living out
            function updateBusPassTypeOptions() {
                var maritalStatus = $('#marital_status').val();
                var approvalLivingOut = $('#approval_living_out').val();
                var busPassTypeSelect = $('#bus_pass_type');
                var currentValue = busPassTypeSelect.val();

                // Clear current options except the placeholder
                busPassTypeSelect.find('option:not(:first)').remove();

                if (maritalStatus === 'single') {
                    // If approval for living out is "yes", hide "Living in Bus only" and show other options
                    if (approvalLivingOut === 'yes') {
                        busPassTypeSelect.append(
                            '<option value="unmarried_daily_travel">Unmarried Daily Travel</option>');
                        busPassTypeSelect.append(
                            '<option value="weekend_monthly_travel">Weekend and Living in Bus</option>');
                    } else {
                        // For single personnel without living out approval, show "Living in Bus only"
                        busPassTypeSelect.append('<option value="living_in_only">Living in Bus only</option>');
                    }

                    // If current selection is not valid for single, clear it
                    var validSingleTypes = [];
                    if (approvalLivingOut === 'yes') {
                        validSingleTypes = ['unmarried_daily_travel', 'weekend_monthly_travel'];
                    } else {
                        validSingleTypes = ['living_in_only'];
                    }

                    if (currentValue && !validSingleTypes.includes(currentValue)) {
                        busPassTypeSelect.val('');
                        // Hide all sections when clearing
                        $('#daily_travel_section').hide();
                        $('#unmarried_daily_travel_section').hide();
                        $('#weekend_monthly_section').hide();
                        $('#living_in_only_section').hide();
                        $('#weekend_only_section').hide();
                    }
                } else if (maritalStatus === 'married') {
                    // Show all bus pass types for married personnel except unmarried daily travel
                    busPassTypeSelect.append('<option value="daily_travel">Daily Travel (Living out)</option>');
                    busPassTypeSelect.append(
                        '<option value="weekend_monthly_travel">Weekend and Living in Bus</option>');
                    busPassTypeSelect.append('<option value="living_in_only">Living in Bus only</option>');
                    busPassTypeSelect.append('<option value="weekend_only">Weekend only</option>');
                } else {
                    // No marital status selected, show all options
                    busPassTypeSelect.append('<option value="daily_travel">Daily Travel (Living out)</option>');
                    busPassTypeSelect.append(
                        '<option value="weekend_monthly_travel">Weekend and Living in Bus</option>');
                    busPassTypeSelect.append('<option value="living_in_only">Living in Bus only</option>');
                    busPassTypeSelect.append('<option value="weekend_only">Weekend only</option>');
                }

                // Restore the previous selection if it's still valid
                if (currentValue && busPassTypeSelect.find('option[value="' + currentValue + '"]').length > 0) {
                    busPassTypeSelect.val(currentValue);
                }

                // Trigger bus pass type change to update sections
                busPassTypeSelect.trigger('change');
            }

            // Function to check and show/hide rent allowance section
            function checkRentAllowanceVisibility() {
                var maritalStatus = $('#marital_status').val();
                var busPassType = $('#bus_pass_type').val();

                // Show rent allowance if married AND bus pass type is NOT "living_in_only" and NOT "unmarried_daily_travel"
                if (maritalStatus === 'married' && busPassType && busPassType !== 'living_in_only' &&
                    busPassType !== 'unmarried_daily_travel') {
                    $('#rent_allowance_section').show();
                } else {
                    $('#rent_allowance_section').hide();
                    // Clear the file input when hidden
                    $('#marriage_part_ii_order_daily').val('');
                }
            }

            // Function to check and show/hide grama niladari certificate section
            function checkGramaNiladariVisibility() {
                var busPassType = $('#bus_pass_type').val();

                // Hide Grama Niladari Certificate for "Living in Bus only"
                if (busPassType === 'living_in_only') {
                    $('#grama_niladari_section').hide();
                    // Clear the file input when hidden
                    $('#grama_niladari_certificate').val('');
                } else {
                    $('#grama_niladari_section').show();
                }
            }

            // Function to check and show/hide permission letter section
            function checkPermissionLetterVisibility() {
                var approvalLivingOut = $('#approval_living_out').val();
                var maritalStatus = $('#marital_status').val();

                // Hide Permission Letter for married persons
                if (maritalStatus === 'married') {
                    $('#permission_letter_section').hide();
                    $('#permission_letter').val('');
                } else if (approvalLivingOut === 'yes') {
                    // Show Permission Letter only if approval for living out is "yes" and person is not married
                    $('#permission_letter_section').show();
                } else {
                    $('#permission_letter_section').hide();
                    // Clear the file input when hidden
                    $('#permission_letter').val('');
                }
            }

            // Marital status change handler
            $('#marital_status').change(function() {
                updateBusPassTypeOptions();
                checkRentAllowanceVisibility();
                checkPermissionLetterVisibility();
            });

            // Approval for living out change handler
            $('#approval_living_out').change(function() {
                updateBusPassTypeOptions();
                checkRentAllowanceVisibility();
            });

            // Trigger on page load if old value exists
            if ($('#marital_status').val()) {
                updateBusPassTypeOptions();
                checkRentAllowanceVisibility();
            }

            // Bus pass type change handler
            $('#bus_pass_type').change(function() {
                var type = $(this).val();
                $('#daily_travel_section').hide();
                $('#unmarried_daily_travel_section').hide();
                $('#weekend_monthly_section').hide();
                $('#living_in_only_section').hide();
                $('#weekend_only_section').hide();

                // Clear all dependent fields when bus pass type changes
                $('#requested_bus_name').val('');
                $('#destination_from_ahq').val('');
                $('#requested_bus_name_unmarried').val('');
                $('#destination_from_ahq_unmarried').val('');
                $('#living_in_bus').val('');
                $('#destination_location_ahq').val('');
                $('#weekend_bus_name').val('');
                $('#weekend_destination').val('');
                $('#living_in_bus_only').val('');
                $('#destination_location_living_in').val('');
                $('#weekend_bus_name_only').val('');
                $('#weekend_destination_only').val('');

                if (type === 'daily_travel') {
                    $('#daily_travel_section').show();
                } else if (type === 'unmarried_daily_travel') {
                    $('#unmarried_daily_travel_section').show();
                } else if (type === 'weekend_monthly_travel') {
                    $('#weekend_monthly_section').show();
                } else if (type === 'living_in_only') {
                    $('#living_in_only_section').show();
                    // Load destination locations from database
                    loadDestinationLocations();
                } else if (type === 'weekend_only') {
                    $('#weekend_only_section').show();
                }

                // Check rent allowance visibility when bus pass type changes
                checkRentAllowanceVisibility();
                // Check grama niladari certificate visibility when bus pass type changes
                checkGramaNiladariVisibility();
                // Check permission letter visibility when bus pass type changes
                checkPermissionLetterVisibility();
            });

            // Function to load destination locations from database
            function loadDestinationLocations() {
                $.ajax({
                    url: '{{ route('destination-locations.api') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            var options = '<option value="">Select Destination Location</option>';
                            $.each(response.data, function(index, location) {
                                options += '<option value="' + location.id + '">' + location
                                    .name + '</option>';
                            });
                            $('#destination_location_living_in').html(options);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Failed to load destination locations:', error);
                    }
                });
            }

            // Trigger on page load if old value exists
            if ($('#bus_pass_type').val()) {
                $('#bus_pass_type').trigger('change');
            }

            // Check visibility on page load
            checkGramaNiladariVisibility();
            checkPermissionLetterVisibility();

            // Fetch person details from API
            $('#fetch-details').click(function() {
                var regimentNo = $('#regiment_no').val();

                if (!regimentNo) {
                    alert('Please enter a regiment number first.');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ route('bus-pass-applications.get-details') }}',
                    method: 'GET',
                    data: {
                        regiment_no: regimentNo
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;

                            // Enable and populate fields from API response
                            // Enable fields temporarily to populate them, then disable again
                            $('#rank').prop('readonly', false).val(data.rank || '').prop(
                                'readonly', true);
                            $('#name').prop('readonly', false).val(data.name || '').prop(
                                'readonly', true);
                            $('#unit').prop('readonly', false).val(data.unit || '').prop(
                                'readonly', true);
                            $('#nic').prop('readonly', false).val(data.nic || '').prop(
                                'readonly', true);

                            $('#army_id').val(data.army_id || '').prop('readonly', !!data
                                .army_id);
                            $('#permanent_address').val(data.permanent_address || '').prop(
                                'readonly', !!data.permanent_address);
                            $('#telephone_no').val(data.telephone_no || '').prop('readonly', !!
                                data.telephone_no);

                            // Handle dropdown selections for new fields
                            if (data.gs_division_id) {
                                $('#gs_division_id').val(data.gs_division_id).trigger('change')
                                    .prop('disabled', true);
                            }
                            if (data.police_station_id) {
                                $('#police_station_id').val(data.police_station_id).trigger(
                                    'change').prop('disabled', true);
                            }

                            // Handle dropdown selections for new fields
                            if (data.gs_division_id) {
                                $('#gs_division_id').val(data.gs_division_id).trigger('change')
                                    .prop('disabled', true);
                            }
                            if (data.police_station_id) {
                                $('#police_station_id').val(data.police_station_id).trigger(
                                    'change').prop('disabled', true);
                            }

                            // alert('Person details loaded successfully!');
                        } else {
                            alert('No data found for this regiment number: ' + response
                                .message);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to fetch person details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        $('#fetch-details').prop('disabled', false).html(
                            '<i class="fas fa-search"></i>');
                    }
                });
            });

            // Branch Card Availability functionality
            let branchCardVerified = false;

            // Function to handle branch card availability state
            function handleBranchCardAvailability() {
                const selectedValue = $('#branch_card_availability').val();
                const branchCardSection = $('#branch_card_id_section');
                const statusSection = $('#verification_status_section');

                if (selectedValue === 'has_branch_card') {
                    branchCardSection.show();
                    statusSection.show();
                    branchCardVerified = false; // Always require re-verification for security

                    // Show appropriate status message
                    const branchCardId = $('#branch_card_id').val().trim();
                    if (branchCardId) {
                        // If there's a branch card ID value (from form reload), show needs verification
                        $('#verification_status').html(
                            '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Requires Verification</span>'
                        );
                    } else {
                        // If no value, show not verified
                        $('#verification_status').html('<span class="badge badge-warning">Not Verified</span>');
                    }
                } else {
                    branchCardSection.hide();
                    statusSection.hide();
                    branchCardVerified = true; // No verification needed if no branch card
                    $('#branch_card_id').val('');
                    $('#verification_status').html('');
                }
            }

            // Check initial state on page load
            handleBranchCardAvailability();

            // Handle dropdown change
            $('#branch_card_availability').on('change', function() {
                handleBranchCardAvailability();
            });

            // Handle regiment number change - clear branch card ID
            $('#regiment_no').on('input', function() {
                // Clear branch card ID when regiment number changes
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
                const regimentNo = $('#regiment_no').val().trim();
                const branchCardId = $('#branch_card_id').val().trim();

                if (!regimentNo) {
                    alert('Please fill in Regiment No first.');
                    return;
                }

                if (!branchCardId) {
                    alert('Please enter Branch Card ID.');
                    return;
                }

                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                $('#verification_status').html('<span class="badge badge-info">Verifying...</span>');

                // API call to verify branch card through backend proxy
                $.ajax({
                    url: '{{ route('bus-pass-applications.verify-branch-card') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        service_no: regimentNo,
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
                                alert('Branch card is not active or approved.');
                            }
                        } else {
                            // No person data found
                            $('#verification_status').html(
                                '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Not Verified</span>'
                            );
                            branchCardVerified = false;
                            alert(
                                'Branch card not found. Please check your Regiment No and Branch Card ID.'
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

                // Enable disabled fields temporarily for form submission
                $('#rank').prop('readonly', false);
                $('#name').prop('readonly', false);
                $('#unit').prop('readonly', false);
                $('#nic').prop('readonly', false);

                // Disable all form fields in hidden sections to prevent submission of empty values
                $('#daily_travel_section:hidden input, #daily_travel_section:hidden select').prop(
                    'disabled', true);
                $('#unmarried_daily_travel_section:hidden input, #unmarried_daily_travel_section:hidden select')
                    .prop('disabled', true);
                $('#weekend_monthly_section:hidden input, #weekend_monthly_section:hidden select').prop(
                    'disabled', true);
                $('#living_in_only_section:hidden input, #living_in_only_section:hidden select').prop(
                    'disabled', true);
                $('#weekend_only_section:hidden input, #weekend_only_section:hidden select').prop(
                    'disabled', true);
            });
        });
    </script>
@endpush
