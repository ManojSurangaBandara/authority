@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fa fa fa-bus nav-icon"></i> {{ __('Edit Bus') }}</div>
                        <div class="card-body">
                            @if ($isUsed ?? false)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Note:</strong> This bus is currently {{ implode(' and ', $usageReasons) }}.
                                    The bus number cannot be changed while it's in use.
                                </div>
                            @endif

                            <form action="{{ route('buses.update', $bus->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="">Bus Number:</label>
                                    <input type="text" name="no" required class="form-control"
                                        value="{{ $bus->no }}" {{ $isUsed ?? false ? 'readonly' : '' }} id="bus_number"
                                        {{ $isUsed ?? false ? '' : 'pattern="[A-Za-z].*"' }}
                                        {{ $isUsed ?? false ? '' : 'title="Bus number must start with a letter"' }} />
                                    @error('no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @if ($isUsed ?? false)
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock"></i> Bus number is locked because this bus is in use.
                                        </small>
                                    @else
                                        <small class="form-text text-muted">
                                            <strong>Examples:</strong> UHA-xxxx, යුහ-xxxx, ABC-xxxx
                                        </small>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="">Bus Name:</label>
                                    <input type="text" name="name" required class="form-control"
                                        value="{{ $bus->name }}" />
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="">Bus Type: </label>
                                    <select name="type_id" id="type_id" class="form-control" required>
                                        <option value="">Select Bus Type</option>
                                        @foreach ($busTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $bus->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="no_of_seats">Number of Seats:</label>
                                    <input type="number" name="no_of_seats" required class="form-control" min="1"
                                        value="{{ $bus->no_of_seats }}" />
                                    @error('no_of_seats')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="total_capacity">Total Capacity:</label>
                                    <input type="number" name="total_capacity" required class="form-control" min="1"
                                        value="{{ $bus->total_capacity }}" />
                                    @error('total_capacity')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    <a href="{{ route('buses.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
