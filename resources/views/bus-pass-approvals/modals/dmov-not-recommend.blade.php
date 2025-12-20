<!-- DMOV Not Recommend Application Modal -->
<div class="modal fade" id="dmovNotRecommendModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.dmov-not-recommend', $application) }}">
                @csrf
                <div class="modal-header bg-warning">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-thumbs-down"></i>
                        Not Recommend Application #{{ $application->id }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Not Recommendation Confirmation</h5>
                        You are about to mark this bus pass application as "not recommended" for:
                        <br><strong>{{ $application->person->name }}</strong>
                        @if ($application->person->regiment_no)
                            ({{ $application->person->regiment_no }})
                        @else
                            (Civil ID: {{ $application->person->civil_id }})
                        @endif
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

                    <div class="form-group">
                        <label for="dmov_not_recommend_remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i> Reason for Not Recommending <span
                                class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror"
                            id="dmov_not_recommend_remarks{{ $application->id }}" name="remarks" rows="4"
                            placeholder="Please provide detailed reasons why you are not recommending this application..." required></textarea>
                        <small class="form-text text-muted">
                            Please provide clear reasoning to help the Branch Staff Officer understand your decision.
                        </small>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This action will return the application to the Branch Staff Officer
                            for
                            review and decision. The application can be resubmitted to DMOV after necessary corrections
                            or reconsideration.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-thumbs-down"></i> Confirm Not Recommend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
