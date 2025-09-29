@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-user nav-icon"></i> {{ __('My Profile') }}
                            <a href="{{ route('profile.change-password') }}" class="btn btn-sm btn-warning float-right">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Name:</label>
                                        <p class="form-control-static">{{ $user->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Email:</label>
                                        <p class="form-control-static">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Regiment No:</label>
                                        <p class="form-control-static">{{ $user->regiment_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Rank:</label>
                                        <p class="form-control-static">{{ $user->rank ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Contact No:</label>
                                        <p class="form-control-static">{{ $user->contact_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Establishment:</label>
                                        <p class="form-control-static">{{ $user->establishment->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Roles:</label>
                                        <p class="form-control-static">
                                            @if ($user->roles->count() > 0)
                                                @foreach ($user->roles as $role)
                                                    <span class="badge badge-primary mr-1">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                No roles assigned
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Account Status:</label>
                                        <p class="form-control-static">
                                            @if ($user->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            If you need to update your profile information, please contact your system
                                            administrator.
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection
