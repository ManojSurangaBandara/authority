@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

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
                            <i class="nav-icon fas fa-user-md nav-icon"></i> {{ __('Fill Emergency Details') }}
                            <div class="float-right">
                                <span class="badge badge-info">Application ID: {{ $application->id }}</span>
                            </div>
                        </div>

                        <form action="{{ route('emergency-details.update', $application) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <!-- Application Info -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="text-primary">Application Information</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Application ID:</strong><br>
                                        {{ $application->id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Type:</strong><br>
                                        {{ $application->type_label }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Name:</strong><br>
                                        {{ $application->person->name }}
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Regiment No:</strong><br>
                                        {{ $application->person->regiment_no ?: 'Civil' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Establishment:</strong><br>
                                        {{ $application->establishment->name ?? 'Not Assigned' }}
                                    </div>
                                </div>

                                <!-- Emergency Details Form -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-primary">Emergency Contact Details</h5>
                                        <p class="text-muted">Please fill in all the emergency contact information below.
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="blood_group">Blood Group <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('blood_group') is-invalid @enderror"
                                                id="blood_group" name="blood_group"
                                                value="{{ old('blood_group', $application->person->blood_group ?? '') }}"
                                                placeholder="e.g., A+, B-, O+" required>
                                            @error('blood_group')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nok_name">NOK Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nok_name') is-invalid @enderror" id="nok_name"
                                                name="nok_name"
                                                value="{{ old('nok_name', $application->person->nok_name ?? '') }}"
                                                placeholder="Full name of NOK" required>
                                            @error('nok_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nok_telephone_no">NOK Telephone No <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" pattern="[0-9]{10}" maxlength="10" inputmode="numeric"
                                                oninvalid="this.setCustomValidity('Please enter a valid 10-digit mobile number')"
                                                oninput="this.setCustomValidity('')"
                                                class="form-control @error('nok_telephone_no') is-invalid @enderror"
                                                id="nok_telephone_no" name="nok_telephone_no"
                                                value="{{ old('nok_telephone_no', $application->person->nok_telephone_no ?? '') }}"
                                                placeholder="10-digit mobile number" required>
                                            @error('nok_telephone_no')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Emergency Details
                                </button>
                                <a href="{{ route('emergency-details.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
