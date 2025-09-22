@extends('adminlte::page')

@section('title', 'Bus Pass Approvals')

@section('content_header')
    <h1>
        Bus Pass Approvals
        <small>
            Pending applications for your review
            @if(auth()->user()->isBranchUser() && auth()->user()->establishment)
                - {{ auth()->user()->establishment->name }}
            @endif
        </small>
    </h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check"></i>
                        Pending Approvals ({{ $pendingApplications->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($pendingApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="approvals-table">
                                <thead>
                                    <tr>
                                        <th>App ID</th>
                                        <th>Person Details</th>
                                        <th>Service Details</th>
                                        <th>Bus Pass Type</th>
                                        <th>Branch/Directorate</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApplications as $application)
                                        <tr>
                                            <td>
                                                <strong>#{{ $application->id }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $application->person->name }}</strong><br>
                                                <small class="text-muted">{{ $application->person->nic }}</small><br>
                                                <small class="text-muted">{{ $application->person->telephone_no }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $application->person->rank }}</strong><br>
                                                <small class="text-muted">{{ $application->person->regiment_no }}</small><br>
                                                <small class="text-muted">{{ $application->person->unit }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $application->bus_pass_type === 'daily_travel' ? 'primary' : 'secondary' }}">
                                                    {{ $application->type_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($application->establishment)
                                                    <span class="badge badge-info">{{ $application->establishment->name }}</span>
                                                    @if($application->establishment->location)
                                                        <br><small class="text-muted">{{ $application->establishment->location }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ $application->branch_directorate }}</span>
                                                @endif
                                            </td>
                                            <td>{!! $application->status_badge !!}</td>
                                            <td>
                                                {{ $application->created_at->format('d M Y') }}<br>
                                                <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            data-toggle="modal" 
                                                            data-target="#viewModal{{ $application->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    
                                                    @can('approve_bus_pass')
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            data-toggle="modal" 
                                                            data-target="#approveModal{{ $application->id }}">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-toggle="modal" 
                                                            data-target="#rejectModal{{ $application->id }}">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Pending Approvals</h4>
                            <p class="text-muted">There are no bus pass applications pending your approval at this time.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for each application -->
    @foreach($pendingApplications as $application)
        @include('bus-pass-approvals.modals.view', ['application' => $application])
        @include('bus-pass-approvals.modals.approve', ['application' => $application])
        @include('bus-pass-approvals.modals.reject', ['application' => $application])
    @endforeach
@stop

@section('css')
    <style>
        .table th {
            white-space: nowrap;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#approvals-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 25,
                "order": [[ 6, "desc" ]], // Sort by submitted date
                "columnDefs": [
                    { "orderable": false, "targets": 7 } // Disable sorting on actions column
                ]
            });
        });
    </script>
@stop
