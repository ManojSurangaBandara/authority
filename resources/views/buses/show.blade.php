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
                            <i class="nav-icon fa fa fa-bus nav-icon"></i> {{ __('View Bus Details') }}
                            <a href="{{ route('buses.index') }}" class="btn btn-sm btn-dark float-right">Back to List</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Bus Number</th>
                                        <td>{{ $bus->no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bus Name</th>
                                        <td>{{ $bus->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bus Type</th>
                                        <td>
                                            {{ $bus->type ? $bus->type->name : 'Unknown' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Number of Seats</th>
                                        <td>{{ $bus->no_of_seats }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Capacity</th>
                                        <td>{{ $bus->total_capacity ?? 'Not specified' }}</td>
                                    </tr>

                                </tbody>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('buses.edit', $bus->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('buses.destroy', $bus->id) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this bus?')">
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
