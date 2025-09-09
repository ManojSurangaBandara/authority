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
                        <div class="card-header"><i class="nav-icon fa fa fa-bus nav-icon"></i> {{ __('Add Bus') }}</div>
                        <div class="card-body">
                            <form action="{{ route('buses.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="">Bus Number:</label>
                                    <input type="text" name="no" required class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label for="">Bus Name:</label>
                                    <input type="text" name="name" required class="form-control" />
                                </div>

                                <div class="mb-3">
                                    <label for="">Bus Type: </label>
                                    <select name="type_id" id="type_id" class="form-control" required>
                                        <option value="">Select Bus Type</option>
                                        @foreach ($busTypes as $busType)
                                            <option value="{{ $busType->id }}">{{ $busType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="no_of_seats">Number of Seats:</label>
                                    <input type="number" name="no_of_seats" required class="form-control" min="1" />
                                    @error('no_of_seats')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
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
