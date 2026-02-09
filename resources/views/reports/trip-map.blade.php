@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map"></i> Trip Map - {{ $trip->bus->no ?? 'N/A' }}
                            @if ($isOngoing)
                                <span class="badge badge-warning">Ongoing</span>
                            @else
                                <span class="badge badge-success">Completed</span>
                            @endif
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('trips.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Trips
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div id="map" style="height: 500px; width: 100%;"></div>
                            </div>
                            <div class="col-md-4">
                                <h5>Trip Details</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Escort:</th>
                                        <td>{{ $trip->escort->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Driver:</th>
                                        <td>{{ $trip->driver->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bus:</th>
                                        <td>{{ $trip->bus->no ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Route:</th>
                                        <td>
                                            @if ($trip->route_type === 'living_out')
                                                {{ $trip->bus_route_name ?? 'N/A' }} (Living Out)
                                            @else
                                                {{ $trip->living_in_bus_name ?? 'N/A' }} (Living In)
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Start Time:</th>
                                        <td>{{ $trip->trip_start_time ? $trip->trip_start_time->format('d M Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>End Time:</th>
                                        <td>{{ $trip->trip_end_time ? $trip->trip_end_time->format('d M Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Passengers:</th>
                                        <td>{{ $trip->onboardings->count() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')
@endsection

@push('js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    <script>
        $(document).ready(function() {
            var startLat = {{ $trip->start_latitude ?? 'null' }};
            var startLng = {{ $trip->start_longitude ?? 'null' }};
            var endLat = {{ $trip->end_latitude ?? 'null' }};
            var endLng = {{ $trip->end_longitude ?? 'null' }};
            var isOngoing = {{ $isOngoing ? 'true' : 'false' }};
            var tripId = {{ $trip->id }};
            var map;
            var routePath;
            var currentMarker;
            var pollingInterval;

            function initMap() {
                if (startLat && startLng) {
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 12,
                        center: {
                            lat: startLat,
                            lng: startLng
                        }
                    });

                    // Add start marker
                    var startMarker = new google.maps.Marker({
                        position: {
                            lat: startLat,
                            lng: startLng
                        },
                        map: map,
                        title: 'Start Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        }
                    });

                    if (!isOngoing && endLat && endLng) {
                        // For completed trips, load all locations and show final route
                        loadTripLocations();
                    } else {
                        // For ongoing trips, start polling for updates
                        loadTripLocations();
                        pollingInterval = setInterval(loadTripLocations, 10000); // Poll every 10 seconds
                    }
                } else {
                    // No location data
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 8,
                        center: {
                            lat: 7.8731,
                            lng: 80.7718
                        } // Center on Sri Lanka
                    });

                    var noDataMarker = new google.maps.Marker({
                        position: {
                            lat: 7.8731,
                            lng: 80.7718
                        },
                        map: map,
                        title: 'No location data available'
                    });
                }
            }

            function loadTripLocations() {
                $.ajax({
                    url: '{{ route('trips.locations', $trip->id) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            updateRoute(response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading trip locations:', error);
                    }
                });
            }

            function updateRoute(locations) {
                var path = [{
                    lat: startLat,
                    lng: startLng
                }]; // Start with start location

                // Add all recorded locations
                locations.forEach(function(location) {
                    path.push({
                        lat: parseFloat(location.latitude),
                        lng: parseFloat(location.longitude)
                    });
                });

                // If trip is completed, add end location
                if (!isOngoing && endLat && endLng) {
                    path.push({
                        lat: endLat,
                        lng: endLng
                    });

                    // Add end marker for completed trips
                    if (!currentMarker) { // Only add once
                        currentMarker = new google.maps.Marker({
                            position: {
                                lat: endLat,
                                lng: endLng
                            },
                            map: map,
                            title: 'End Location',
                            icon: {
                                url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                            }
                        });
                    }
                }

                // Remove existing route
                if (routePath) {
                    routePath.setMap(null);
                }

                // Draw new route
                routePath = new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: isOngoing ? '#0000FF' : '#FF0000', // Blue for ongoing, red for completed
                    strokeOpacity: 1.0,
                    strokeWeight: 3
                });
                routePath.setMap(map);

                // Add current position marker for ongoing trips
                if (isOngoing && locations.length > 0) {
                    var lastLocation = locations[locations.length - 1];
                    if (currentMarker) {
                        currentMarker.setMap(null);
                    }
                    currentMarker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(lastLocation.latitude),
                            lng: parseFloat(lastLocation.longitude)
                        },
                        map: map,
                        title: 'Current Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                        }
                    });
                }

                // Adjust map bounds to show entire route
                if (path.length > 1) {
                    var bounds = new google.maps.LatLngBounds();
                    path.forEach(function(point) {
                        bounds.extend(point);
                    });
                    map.fitBounds(bounds);
                }
            }

            // Initialize map
            initMap();

            // Clean up polling when page unloads
            $(window).on('beforeunload', function() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });
        });
    </script>
@endpush
