@extends('adminlte::page')

@section('title', 'Route Groups')

@section('content_header')
    <h1>Route Groups</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-layer-group"></i> Create / Update Route Group</h3>
                    </div>
                    <div class="card-body">
                        <form id="routeGroupForm" method="POST" action="{{ route('route-groups.store') }}">
                            @csrf

                            <input type="hidden" id="group_id" name="group_id" value="">


                            <div class="form-group">
                                <label>Select Routes</label>
                                <div class="card card-outline card-secondary mb-2">
                                    <div class="card-header">Living Out Routes</div>
                                    <div class="card-body" style="max-height: 170px; overflow-y: auto;">
                                        @foreach ($livingOutRoutes as $route)
                                            <div class="form-check">
                                                <input class="form-check-input route-member-checkbox" type="checkbox"
                                                    name="members[]" value="living_out:{{ $route->id }}"
                                                    id="route_out_{{ $route->id }}">
                                                <label class="form-check-label" for="route_out_{{ $route->id }}">
                                                    {{ $route->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="card card-outline card-secondary mb-2">
                                    <div class="card-header">Living In Routes</div>
                                    <div class="card-body" style="max-height: 170px; overflow-y: auto;">
                                        @foreach ($livingInRoutes as $route)
                                            <div class="form-check">
                                                <input class="form-check-input route-member-checkbox" type="checkbox"
                                                    name="members[]" value="living_in:{{ $route->id }}"
                                                    id="route_in_{{ $route->id }}">
                                                <label class="form-check-label" for="route_in_{{ $route->id }}">
                                                    {{ $route->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <small class="form-text text-muted">Select as many routes as required.</small>
                            </div>


                            <button type="submit" class="btn btn-primary btn-block" id="submitButton">
                                <i class="fas fa-save"></i> Save Route Group
                            </button>
                            <button type="button" class="btn btn-secondary btn-block" id="clearFormButton"
                                style="display:none;">
                                <i class="fas fa-times"></i> Cancel Edit
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Existing Route Groups</h3>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped" id="route-groups-table">
                            <thead>
                                <tr>
                                    <th>Routes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $group)
                                    <tr data-group-id="{{ $group->id }}" data-group-name="{{ $group->name }}">
                                        <td>
                                            @foreach ($group->members as $member)
                                                @if ($member->route_type === 'living_out')
                                                    @php $route = $livingOutRoutes->firstWhere('id', $member->route_id); @endphp
                                                    <span class="badge badge-info">{{ $route?->name ?? 'Unknown' }}
                                                        (Out)
                                                    </span>
                                                @else
                                                    @php $route = $livingInRoutes->firstWhere('id', $member->route_id); @endphp
                                                    <span class="badge badge-warning">{{ $route?->name ?? 'Unknown' }}
                                                        (In)</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <form action="{{ route('route-groups.destroy', $group->id) }}" method="POST"
                                                style="display:inline-block;" class="delete-group-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this group?');"><i
                                                        class="fas fa-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')
@stop

@section('js')
    <script>
        $(function() {
            document.querySelectorAll('.edit-group').forEach(function(el) {
                el.remove();
            });

            $('#clearFormButton').hide();


            // Disable edit path; clear button is not used.
            $('#clearFormButton').hide();

            $('#routeGroupForm').on('submit', function() {
                var selectedCount = $('.route-member-checkbox:checked').length;
                if (selectedCount === 0) {
                    alert('Please select at least one route');
                    return false;
                }
                return true;
            });

        });
    </script>
@stop
