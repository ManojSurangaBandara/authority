@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-heart nav-icon"></i> {{ __('View Marital Status') }}
                            <a href="{{ route('marital-statuses.index') }}" class="btn btn-sm btn-dark float-right">Back to
                                List</a>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This is a view-only record and cannot be modified.
                            </div>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $maritalStatus->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ $maritalStatus->status === 'married' ? 'success' : 'primary' }} p-2">
                                                {{ ucfirst($maritalStatus->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $maritalStatus->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $maritalStatus->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection
