<!-- Recommend Application Modal -->
<div class="modal fade" id="recommendModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.recommend', $application) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-thumbs-up"></i>
                        Recommend Application #{{ $application->id }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-info-circle"></i> Recommendation Confirmation</h5>
                        You are about to recommend the bus pass application for:
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    @if ($application->obtain_sltb_season == 'yes')
                        <div class="alert alert-warning">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    id="sltb_season_awareness{{ $application->id }}" name="sltb_season_awareness"
                                    required>
                                <label class="form-check-label" for="sltb_season_awareness{{ $application->id }}">
                                    <i class="fas fa-bus text-warning"></i>
                                    <strong>SLTB Season Awareness:</strong>
                                    This person has obtained SLTB Bus Season. Please confirm your approval.
                                </label>
                            </div>

                        </div>
                    @endif

                    {{-- Route Statistics Section - Only for DMOV users --}}
                    @if (auth()->user()->hasAnyRole(['Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)']))
                        @php
                            $dmovStatuses = ['forwarded_to_movement', 'pending_staff_officer_2_mov', 'pending_col_mov'];
                        @endphp
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
                                        $dailyStats[
                                            'dmov_pending_count'
                                        ] = $application->getPendingCountForRouteByStatuses(
                                            $dmovStatuses,
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
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $dailyStats['dmov_pending_count'] }}
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
                                        $livingInStats[
                                            'dmov_pending_count'
                                        ] = $application->getPendingCountForRouteByStatuses(
                                            $dmovStatuses,
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
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $livingInStats['dmov_pending_count'] }}
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
                                        $weekendStats[
                                            'dmov_pending_count'
                                        ] = $application->getPendingCountForRouteByStatuses(
                                            $dmovStatuses,
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
                                                    <i class="fas fa-clock"></i> DMOV Pending:
                                                    {{ $weekendStats['dmov_pending_count'] }}
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
                        <label for="recommend_remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i> Recommendation Remarks (Optional)
                        </label>
                        <textarea class="form-control" id="recommend_remarks{{ $application->id }}" name="remarks" rows="3"
                            placeholder="Enter your recommendation remarks..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            This recommendation will forward the application to the Subject Clerk (DMOV) for movement
                            approval.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="recommendBtn{{ $application->id }}"
                        @if ($application->obtain_sltb_season == 'yes') disabled @endif>
                        <i class="fas fa-thumbs-up"></i> Confirm Recommendation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($application->obtain_sltb_season == 'yes')
    <script>
        // Execute immediately when modal is loaded, not on DOMContentLoaded
        (function() {
            const checkbox = document.getElementById('sltb_season_awareness{{ $application->id }}');
            const recommendBtn = document.getElementById('recommendBtn{{ $application->id }}');

            if (checkbox && recommendBtn) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        recommendBtn.disabled = false;
                        recommendBtn.classList.remove('disabled');
                    } else {
                        recommendBtn.disabled = true;
                        recommendBtn.classList.add('disabled');
                    }
                });
            }
        })();
    </script>
@endif
