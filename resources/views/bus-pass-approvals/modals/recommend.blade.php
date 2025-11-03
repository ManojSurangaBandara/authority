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
                <div class="modal-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-info-circle"></i> Recommendation Confirmation</h5>
                        You are about to recommend the bus pass application for:
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    {{-- Display current branch card information --}}
                    @if ($application->branch_card_availability)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-id-card"></i> Branch Card Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Availability:</strong><br>
                                    <span
                                        class="badge badge-{{ $application->branch_card_availability === 'has_branch_card' ? 'success' : 'warning' }}">
                                        {{ $application->branch_card_availability === 'has_branch_card' ? 'Has Branch Card (Integration)' : 'No Branch Card (Temporary)' }}
                                    </span>
                                </div>
                                @if ($application->branch_card_availability === 'has_branch_card' && $application->branch_card_id)
                                    <div class="col-md-6">
                                        <strong>Branch Card ID:</strong><br>
                                        <span class="badge badge-info">{{ $application->branch_card_id }}</span>
                                        <br><small class="text-muted">
                                            <i class="fas fa-check-circle text-success"></i> Verified via API
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

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
                            This recommendation will forward the application to the Director (Branch) for final
                            decision.
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
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endif
