{{-- Forward to Branch Clerk Modal --}}
<div class="modal fade" id="forwardToBranchClerkModal{{ $application->id }}" tabindex="-1" role="dialog"
    aria-labelledby="forwardToBranchClerkModalLabel{{ $application->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="forwardToBranchClerkModalLabel{{ $application->id }}">
                    <i class="fas fa-arrow-left"></i>
                    Forward Application to Branch Clerk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('bus-pass-approvals.forward-to-branch-clerk', $application->id) }}">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Returning to Branch Clerk:</strong>
                        This application was not recommended by the DMOV Clerk and is being forwarded back to the Branch
                        Clerk for review.
                    </div>

                    {{-- Route Statistics Section - Only for DMOV users --}}
                    @if (auth()->user()->hasAnyRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)']))
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Route Statistics</h6>
                            </div>
                            <div class="card-body">
                                @if ($application->requested_bus_name)
                                    @php
                                        $dailyStats = $application->getRouteStatistics(
                                            $application->requested_bus_name,
                                            'living_out',
                                        );
                                    @endphp
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <strong><i class="fas fa-bus"></i> Daily Travel Route:
                                                {{ $application->requested_bus_name }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Approved:
                                                    {{ $dailyStats['approved_count'] }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pending:
                                                    {{ $dailyStats['pending_count'] }}
                                                </span>
                                                @if ($dailyStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $dailyStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($application->living_in_bus)
                                    @php
                                        $livingInStats = $application->getRouteStatistics(
                                            $application->living_in_bus,
                                            'living_in',
                                        );
                                    @endphp
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <strong><i class="fas fa-home"></i> Living In Route:
                                                {{ $application->living_in_bus }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Approved:
                                                    {{ $livingInStats['approved_count'] }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pending:
                                                    {{ $livingInStats['pending_count'] }}
                                                </span>
                                                @if ($livingInStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $livingInStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($application->weekend_bus_name)
                                    @php
                                        $weekendStats = $application->getRouteStatistics(
                                            $application->weekend_bus_name,
                                            'weekend',
                                        );
                                    @endphp
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <strong><i class="fas fa-calendar-weekend"></i> Weekend Route:
                                                {{ $application->weekend_bus_name }}</strong>
                                            <div class="mt-1">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Approved:
                                                    {{ $weekendStats['approved_count'] }}
                                                </span>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pending:
                                                    {{ $weekendStats['pending_count'] }}
                                                </span>
                                                @if ($weekendStats['capacity_info'])
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-chair"></i> Seats:
                                                        {{ $weekendStats['capacity_info']['seats'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> No bus assigned to
                                                        this route
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!$application->requested_bus_name && !$application->living_in_bus && !$application->weekend_bus_name)
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle"></i> No route information available for this
                                        application.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Previous DMOV Action --}}
                    @if ($application->wasRecentlyDmovNotRecommended())
                        @php
                            $dmovAction = $application->getLatestDmovNotRecommendedAction();
                        @endphp
                        <div class="card mb-3">
                            <div class="card-header bg-warning">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Previous DMOV Action</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Action:</strong> <span class="badge badge-warning">Not
                                            Recommended</span><br>
                                        <strong>By:</strong> {{ $dmovAction->user->name ?? 'N/A' }}<br>
                                        <strong>Role:</strong> {{ $dmovAction->user->roles->first()->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Date:</strong> {{ $dmovAction->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Status:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $dmovAction->action)) }}
                                    </div>
                                </div>
                                @if ($dmovAction->remarks)
                                    <div class="mt-2">
                                        <strong>DMOV Remarks:</strong>
                                        <div class="bg-light p-2 rounded mt-1">{{ $dmovAction->remarks }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Remarks Field --}}
                    <div class="form-group">
                        <label for="remarks{{ $application->id }}" class="font-weight-bold">
                            <i class="fas fa-comment"></i> Remarks <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks{{ $application->id }}" name="remarks"
                            rows="4" placeholder="Enter your remarks for forwarding this application back to Branch Clerk..." required>{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Please provide detailed remarks explaining why the application is being returned to Branch
                            Clerk.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Forward to Branch Clerk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
