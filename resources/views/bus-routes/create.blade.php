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
                        <div class="card-header"><i class="nav-icon fas fa-road"></i> {{ __('Add New Bus Route') }}</div>
                        <div class="card-body">
                            <form action="{{ route('bus-routes.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="bus_id">Bus:</label>
                                    <select name="bus_id" id="bus_id" class="form-control" required>
                                        <option value="">Select Bus</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">{{ $bus->name }} ({{ $bus->no }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bus_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name">Route Name:</label>
                                    <input type="text" name="name" required class="form-control"
                                        value="{{ old('name') }}" />
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    <a href="{{ route('bus-routes.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
