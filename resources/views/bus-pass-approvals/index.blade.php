@extends('adminlte::page')

@section('title', 'Bus Pass Approvals')

@section('content_header')
    <h1>
        Bus Pass Approvals
        <small>
            Pending applications for your review
            @if (auth()->user()->isBranchUser() && auth()->user()->establishment)
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
                    @if ($pendingApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="approvals-table">
                                <thead>
                                    <tr>
                                        <th>App ID</th>
                                        <th>Person Details</th>
                                        <th>Service Details</th>
                                        <th>Bus Pass Type</th>
                                        <th>Branch/Directorate</th>
                                        {{-- <th>Status</th> --}}
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingApplications as $application)
                                        <tr class="{{ $application->wasRecentlyNotRecommended() ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>#{{ $application->id }}</strong>
                                                @if ($application->wasRecentlyNotRecommended())
                                                    <br><span class="badge badge-warning"><i
                                                            class="fas fa-exclamation-triangle"></i> Not Recommended</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $application->person->name }}</strong><br>
                                                <small class="text-muted">{{ $application->person->nic }}</small><br>
                                                <small class="text-muted">{{ $application->person->telephone_no }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $application->person->rank ?: 'Not specified' }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $application->person->regiment_no }}</small><br>
                                                <small class="text-muted">{{ $application->person->unit }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $application->bus_pass_type === 'daily_travel' ? 'primary' : 'secondary' }}">
                                                    {{ $application->type_label }}
                                                </span>
                                                @if ($application->obtain_sltb_season == 'yes')
                                                    <br><span class="badge badge-warning mt-1">
                                                        <i class="fas fa-bus"></i> SLTB Season Available
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($application->establishment)
                                                    <span
                                                        class="badge badge-info">{{ $application->establishment->name }}</span>
                                                    @if ($application->establishment->location)
                                                        <br><small
                                                            class="text-muted">{{ $application->establishment->location }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ $application->branch_directorate }}</span>
                                                @endif
                                            </td>
                                            {{-- <td>{!! $application->status_badge !!}</td> --}}
                                            <td>
                                                {{ $application->created_at->format('d M Y') }}<br>
                                                <small
                                                    class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#viewModal{{ $application->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>

                                                    @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                                        <a href="{{ route('bus-pass-applications.edit', $application->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    @endif

                                                    @can('approve_bus_pass')
                                                        @if (auth()->user()->hasRole('Staff Officer (Branch)'))
                                                            {{-- Staff Officer Branch: Recommend/Not Recommend --}}
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-toggle="modal"
                                                                data-target="#recommendModal{{ $application->id }}">
                                                                <i class="fas fa-thumbs-up"></i> Recommend
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-warning"
                                                                data-toggle="modal"
                                                                data-target="#notRecommendModal{{ $application->id }}">
                                                                <i class="fas fa-thumbs-down"></i> Not Recommend
                                                            </button>
                                                        @else
                                                            {{-- All other roles: Approve/Reject or Forward --}}
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-toggle="modal"
                                                                data-target="#approveModal{{ $application->id }}">
                                                                @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                                                                    <i class="fas fa-arrow-right"></i> Forward
                                                                @else
                                                                    <i class="fas fa-check"></i> Approve
                                                                @endif
                                                            </button>

                                                            @unless (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    data-toggle="modal"
                                                                    data-target="#rejectModal{{ $application->id }}">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                            @endunless
                                                        @endif
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
    @foreach ($pendingApplications as $application)
        @include('bus-pass-approvals.modals.view', ['application' => $application])
        @include('bus-pass-approvals.modals.approve', ['application' => $application])
        @include('bus-pass-approvals.modals.reject', ['application' => $application])
        @include('bus-pass-approvals.modals.recommend', ['application' => $application])
        @include('bus-pass-approvals.modals.not-recommend', ['application' => $application])
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

        .bg-warning-light {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }

        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .sltb-confirmation-alert {
            border-left: 4px solid #ffc107;
            background-color: #fff3cd;
        }

        .sltb-confirmation-alert .form-check-label {
            font-weight: 500;
            color: #856404;
        }

        .btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .sltb-confirmation-alert .form-check-input {
            transform: scale(1.2);
            margin-right: 8px;
        }

        .sltb-confirmation-alert .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
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
                "order": [
                    [6, "desc"]
                ], // Sort by submitted date
                "columnDefs": [{
                        "orderable": false,
                        "targets": 7
                    } // Disable sorting on actions column
                ]
            });
        });
    </script>
@stop
