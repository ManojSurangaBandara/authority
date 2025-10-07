@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card mt-3">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-map-marker-alt"></i> View Destination Location
                        <a href="{{ route('destination-locations.index') }}" class="btn btn-sm btn-dark float-right">Back</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 40%">Destination Location</th>
                                    <td>{{ $destinationLocation->destination_location }}</td>
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
