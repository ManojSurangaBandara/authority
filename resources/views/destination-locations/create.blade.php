@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-3">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-map-marker-alt"></i> Add New Destination Location
                    </div>
                    <div class="card-body">
                        <form action="{{ route('destination-locations.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="destination_location">Destination Location <span class="text-danger">*</span></label>
                                <input type="text" name="destination_location" class="form-control" placeholder="Enter Destination Location" required>
                                @error('destination_location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                <a href="{{ route('destination-locations.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@include('footer')
@endsection
