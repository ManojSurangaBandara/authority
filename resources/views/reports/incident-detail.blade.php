@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="fas fa-fw fa-exclamation-triangle"></i> Incident Details
                            <a href="{{ route('incident-reports.index') }}" class="btn btn-sm btn-dark float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%">Incident Type</th>
                                                <td>{{ $incident->incidentType->name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $incident->description }}</td>
                                            </tr>
                                            <tr>
                                                <th>Reported At</th>
                                                <td>{{ $incident->created_at ? $incident->created_at->format('d M Y H:i') : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Escort</th>
                                                <td>{{ $incident->escort_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Route Type</th>
                                                <td>{{ ucfirst(str_replace('_', ' ', $incident->route_type)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Route</th>
                                                <td>{{ $incident->route_details ? $incident->route_details['name'] : '' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%">Driver</th>
                                                <td>{{ $incident->driver_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Bus</th>
                                                <td>{{ $incident->bus_details ? $incident->bus_details['no'] : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>SLCMP In-charge</th>
                                                <td>{{ $incident->slcmp_incharge_name ?? '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Images</th>
                                                <td>
                                                    @if ($incident->image1 || $incident->image2 || $incident->image3)
                                                        <div class="row">
                                                            @if ($incident->image1)
                                                                <div class="col-4">
                                                                    <a href="{{ asset('storage/' . $incident->image1) }}"
                                                                        target="_blank">
                                                                        <img src="{{ asset('storage/' . $incident->image1) }}"
                                                                            class="img-thumbnail"
                                                                            style="width: 100px; height: 100px; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            @if ($incident->image2)
                                                                <div class="col-4">
                                                                    <a href="{{ asset('storage/' . $incident->image2) }}"
                                                                        target="_blank">
                                                                        <img src="{{ asset('storage/' . $incident->image2) }}"
                                                                            class="img-thumbnail"
                                                                            style="width: 100px; height: 100px; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            @if ($incident->image3)
                                                                <div class="col-4">
                                                                    <a href="{{ asset('storage/' . $incident->image3) }}"
                                                                        target="_blank">
                                                                        <img src="{{ asset('storage/' . $incident->image3) }}"
                                                                            class="img-thumbnail"
                                                                            style="width: 100px; height: 100px; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        No images
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
