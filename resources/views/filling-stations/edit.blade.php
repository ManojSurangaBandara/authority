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
                        <div class="card-header"><i class="nav-icon fas fa-gas-pump nav-icon"></i> {{ __('Edit Filling Station') }}</div>
                        <div class="card-body">
                            <form action="{{ route('filling-stations.update', $fillingStation->id) }}" method="POST" id="fillingStationForm">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        value="{{ $fillingStation->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    <a href="{{ route('filling-stations.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
