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
                                                id="rank" name="rank" value="{{ old('rank') }}" required>
                                            @error('rank')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit">Unit <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                                id="unit" name="unit" value="{{ old('unit') }}" required>
                                            @error('unit')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nic">NIC <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nic') is-invalid @enderror"
                                                id="nic" name="nic" value="{{ old('nic') }}" required>
                                            @error('nic')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                            <label for="grama_seva_division">Grama Seva Division <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('grama_seva_division') is-invalid @enderror"
                                                id="grama_seva_division" name="grama_seva_division"
                                                value="{{ old('grama_seva_division') }}" required>
                                            @error('grama_seva_division')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nearest_police_station">Nearest Police Station <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nearest_police_station') is-invalid @enderror"
                                                id="nearest_police_station" name="nearest_police_station"
                                                value="{{ old('nearest_police_station') }}" required>
                                            @error('nearest_police_station')
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
                                                    <option value="Kinnadeniya 1"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 1' ? 'selected' : '' }}>
                                                        Kinnadeniya 1</option>
                                                    <option value="Kinnadeniya 2"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 2' ? 'selected' : '' }}>
                                                        Kinnadeniya 2</option>
                                                    <option value="Kinnadeniya 3"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 3' ? 'selected' : '' }}>
                                                        Kinnadeniya 3</option>
                                                    <option value="Panagoda - Officers"
                                                        {{ old('living_in_bus') == 'Panagoda - Officers' ? 'selected' : '' }}>
                                                        Panagoda - Officers</option>
                                                    <option value="Panagoda - Other Ranks"
                                                        {{ old('living_in_bus') == 'Panagoda - Other Ranks' ? 'selected' : '' }}>
                                                        Panagoda - Other Ranks</option>
                                                    <option value="Kandalanda"
                                                        {{ old('living_in_bus') == 'Kandalanda' ? 'selected' : '' }}>
                                                        Kandalanda</option>
                                                    <option value="Pamankada"
                                                        {{ old('living_in_bus') == 'Pamankada' ? 'selected' : '' }}>
                                                        Pamankada</option>
                                                    <option value="Maharagama"
                                                        {{ old('living_in_bus') == 'Maharagama' ? 'selected' : '' }}>
                                                        Maharagama</option>
                                                    <option value="Mathegoda"
                                                        {{ old('living_in_bus') == 'Mathegoda' ? 'selected' : '' }}>
                                                        Mathegoda</option>
                                                    <option value="SLEME - Kompanyaweediya"
                                                        {{ old('living_in_bus') == 'SLEME - Kompanyaweediya' ? 'selected' : '' }}>
                                                        SLEME - Kompanyaweediya</option>
                                                    <option value="Rathmalaana"
                                                        {{ old('living_in_bus') == 'Rathmalaana' ? 'selected' : '' }}>
                                                        Rathmalaana</option>
                                                    <option value="Other"
                                                        {{ old('living_in_bus') == 'Other' ? 'selected' : '' }}>Other
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_location_ahq">Destination Location from AHQ</label>
                                                <select class="form-control" id="destination_location_ahq"
                                                    name="destination_location_ahq">
                                                    <option value="">Select Destination Location</option>
                                                    <option value="Panagoda"
                                                        {{ old('destination_location_ahq') == 'Panagoda' ? 'selected' : '' }}>
                                                        Panagoda</option>
                                                    <option value="Kandalanda"
                                                        {{ old('destination_location_ahq') == 'Kandalanda' ? 'selected' : '' }}>
                                                        Kandalanda</option>
                                                    <option value="Maharagama"
                                                        {{ old('destination_location_ahq') == 'Maharagama' ? 'selected' : '' }}>
                                                        Maharagama</option>
                                                    <option value="Kinnadeniya"
                                                        {{ old('destination_location_ahq') == 'Kinnadeniya' ? 'selected' : '' }}>
                                                        Kinnadeniya</option>
                                                    <option value="Pamankada"
                                                        {{ old('destination_location_ahq') == 'Pamankada' ? 'selected' : '' }}>
                                                        Pamankada</option>
                                                    <option value="Mathegoda"
                                                        {{ old('destination_location_ahq') == 'Mathegoda' ? 'selected' : '' }}>
                                                        Mathegoda</option>
                                                    <option value="SLEME Workshop"
                                                        {{ old('destination_location_ahq') == 'SLEME Workshop' ? 'selected' : '' }}>
                                                        SLEME Workshop</option>
                                                    <option value="Rathmalaana"
                                                        {{ old('destination_location_ahq') == 'Rathmalaana' ? 'selected' : '' }}>
                                                        Rathmalaana</option>
                                                    <option value="Other"
                                                        {{ old('destination_location_ahq') == 'Other' ? 'selected' : '' }}>
                                                        Other</option>
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
                                                    <option value="Kinnadeniya 1"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 1' ? 'selected' : '' }}>
                                                        Kinnadeniya 1</option>
                                                    <option value="Kinnadeniya 2"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 2' ? 'selected' : '' }}>
                                                        Kinnadeniya 2</option>
                                                    <option value="Kinnadeniya 3"
                                                        {{ old('living_in_bus') == 'Kinnadeniya 3' ? 'selected' : '' }}>
                                                        Kinnadeniya 3</option>
                                                    <option value="Panagoda - Officers"
                                                        {{ old('living_in_bus') == 'Panagoda - Officers' ? 'selected' : '' }}>
                                                        Panagoda - Officers</option>
                                                    <option value="Panagoda - Other Ranks"
                                                        {{ old('living_in_bus') == 'Panagoda - Other Ranks' ? 'selected' : '' }}>
                                                        Panagoda - Other Ranks</option>
                                                    <option value="Kandalanda"
                                                        {{ old('living_in_bus') == 'Kandalanda' ? 'selected' : '' }}>
                                                        Kandalanda</option>
                                                    <option value="Pamankada"
                                                        {{ old('living_in_bus') == 'Pamankada' ? 'selected' : '' }}>
                                                        Pamankada</option>
                                                    <option value="Maharagama"
                                                        {{ old('living_in_bus') == 'Maharagama' ? 'selected' : '' }}>
                                                        Maharagama</option>
                                                    <option value="Mathegoda"
                                                        {{ old('living_in_bus') == 'Mathegoda' ? 'selected' : '' }}>
                                                        Mathegoda</option>
                                                    <option value="SLEME - Kompanyaweediya"
                                                        {{ old('living_in_bus') == 'SLEME - Kompanyaweediya' ? 'selected' : '' }}>
                                                        SLEME - Kompanyaweediya</option>
                                                    <option value="Rathmalaana"
                                                        {{ old('living_in_bus') == 'Rathmalaana' ? 'selected' : '' }}>
                                                        Rathmalaana</option>
                                                    <option value="Other"
                                                        {{ old('living_in_bus') == 'Other' ? 'selected' : '' }}>Other
                                                    </option>
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
                                            <label for="rent_allowance_order_daily">Rent Allowance Part II Order
                                                <span class="text-info">(For Married Personnel - Not applicable for Living
                                                    in Bus only)</span></label>
                                            <input type="file"
                                                class="form-control-file @error('rent_allowance_order') is-invalid @enderror"
                                                id="rent_allowance_order_daily" name="rent_allowance_order"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max:
                                                2MB)</small>
                                            @error('rent_allowance_order')
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
                                                2MB)</small>
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
                                                2MB)</small>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
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
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Establishment dropdown (only for non-branch users)
            @if (!auth()->user()->isBranchUser())
                $('#establishment_id').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select Establishment',
                    allowClear: true,
                    width: '100%'
                });
            @endif

            // Function to update bus pass type options based on marital status
            function updateBusPassTypeOptions() {
                var maritalStatus = $('#marital_status').val();
                var busPassTypeSelect = $('#bus_pass_type');
                var currentValue = busPassTypeSelect.val();

                // Clear current options except the placeholder
                busPassTypeSelect.find('option:not(:first)').remove();

                if (maritalStatus === 'single') {
                    // Only show "Living in Bus only" for single personnel
                    busPassTypeSelect.append('<option value="living_in_only">Living in Bus only</option>');
                    // If current selection is not valid for single, clear it
                    if (currentValue && currentValue !== 'living_in_only') {
                        busPassTypeSelect.val('');
                        // Hide all sections when clearing
                        $('#daily_travel_section').hide();
                        $('#weekend_monthly_section').hide();
                        $('#living_in_only_section').hide();
                        $('#weekend_only_section').hide();
                    }
                } else if (maritalStatus === 'married') {
                    // Show all bus pass types for married personnel
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

                // Show rent allowance if married AND bus pass type is NOT "living_in_only"
                if (maritalStatus === 'married' && busPassType && busPassType !== 'living_in_only') {
                    $('#rent_allowance_section').show();
                } else {
                    $('#rent_allowance_section').hide();
                    // Clear the file input when hidden
                    $('#rent_allowance_order_daily').val('');
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

            // Marital status change handler
            $('#marital_status').change(function() {
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
                $('#weekend_monthly_section').hide();
                $('#living_in_only_section').hide();
                $('#weekend_only_section').hide();

                if (type === 'daily_travel') {
                    $('#daily_travel_section').show();
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

                            // Auto-populate fields
                            $('#rank').val(data.rank || '').prop('readonly', !!data.rank);
                            $('#name').val(data.name || '').prop('readonly', !!data.name);
                            $('#unit').val(data.unit || '').prop('readonly', !!data.unit);
                            $('#nic').val(data.nic || '').prop('readonly', !!data.nic);
                            $('#army_id').val(data.army_id || '').prop('readonly', !!data
                                .army_id);
                            $('#permanent_address').val(data.permanent_address || '').prop(
                                'readonly', !!data.permanent_address);
                            $('#telephone_no').val(data.telephone_no || '').prop('readonly', !!
                                data.telephone_no);
                            $('#grama_seva_division').val(data.grama_seva_division || '').prop(
                                'readonly', !!data.grama_seva_division);
                            $('#nearest_police_station').val(data.nearest_police_station || '')
                                .prop('readonly', !!data.nearest_police_station);

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
        });
    </script>
@endpush
