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
                        <i class="fas fa-fw fa-landmark"></i> Add New Gs Devision
                    </div>
                    <div class="card-body">
                        <form action="{{ route('gs-devision.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name">Gs Devision Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Gs Devision Name" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                <a href="{{ route('gs-devision.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
