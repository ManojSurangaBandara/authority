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
                        <div class="card-header">
                            <i class="nav-icon fas fa-road"></i> {{ __('View Bus Route Details') }}
                            <a href="{{ route('bus-routes.index') }}" class="btn btn-sm btn-dark float-right">Back to List</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Route Name</th>
                                        <td>{{ $busRoute->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Assigned Bus</th>
                                        <td>
                                            @if ($busRoute->bus)
                                                {{ $busRoute->bus->name }} ({{ $busRoute->bus->no }})
                                                <span class="badge badge-success ml-2">Assigned</span>
                                            @else
                                                <span class="text-muted">No bus assigned</span>
                                                <span class="badge badge-warning ml-2">Unassigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($busRoute->bus)
                                        <tr>
                                            <th>Bus Details</th>
                                            <td>
                                                <small>
                                                    <strong>Type:</strong> {{ $busRoute->bus->type->name ?? 'N/A' }}<br>
                                                    <strong>Seats:</strong> {{ $busRoute->bus->no_of_seats ?? 'N/A' }}<br>
                                                    <strong>Capacity:</strong> {{ $busRoute->bus->total_capacity ?? 'N/A' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            @if (!$busRoute->bus)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    This route doesn't have a bus assigned. You can assign a bus using the
                                    <a href="{{ route('bus-assignments.index') }}" class="alert-link">Bus Assignments</a>
                                    page.
                                </div>
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('bus-routes.edit', $busRoute->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('bus-routes.destroy', $busRoute->id) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this route?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection
