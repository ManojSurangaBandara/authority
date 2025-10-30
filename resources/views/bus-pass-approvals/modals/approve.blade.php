<!-- Approve Application Modal -->
<div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.approve', $application) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white">
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            <i class="fas fa-arrow-right"></i>
                            Forward Application #{{ $application->id }}
                        @else
                            <i class="fas fa-check"></i>
                            Approve Application #{{ $application->id }}
                        @endif
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-info-circle"></i>
                            @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                                Forward Confirmation
                            @else
                                Approval Confirmation
                            @endif
                        </h5>
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            You are about to forward the bus pass application for:
                        @else
                            You are about to approve the bus pass application for:
                        @endif
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    @if (auth()->user()->hasRole('Subject Clerk (DMOV)'))
                        <div class="form-group">
                            <label for="obtain_sltb_season{{ $application->id }}">
                                <i class="fas fa-bus"></i> SLTB Season Availability <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="obtain_sltb_season{{ $application->id }}"
                                name="obtain_sltb_season" required>
                                <option value="">-- Select SLTB Season Status --</option>
                                <option value="yes"
                                    {{ $application->obtain_sltb_season == 'yes' ? 'selected' : '' }}>Yes - Season
                                    Available</option>
                                <option value="no" {{ $application->obtain_sltb_season == 'no' ? 'selected' : '' }}>
                                    No - Season Not Available</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Current Status:
                                <strong>{{ $application->obtain_sltb_season == 'yes' ? 'Season Available' : 'Season Not Available' }}</strong>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="branch_card_availability{{ $application->id }}">
                                <i class="fas fa-id-card"></i> Branch Card Availability <span
                                    class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="branch_card_availability{{ $application->id }}"
                                name="branch_card_availability" required>
                                <option value="">-- Select Branch Card Status --</option>
                                <option value="has_branch_card"
                                    {{ $application->branch_card_availability == 'has_branch_card' ? 'selected' : '' }}>
                                    Has Branch Card - Integrate Bus Pass</option>
                                <option value="no_branch_card"
                                    {{ $application->branch_card_availability == 'no_branch_card' ? 'selected' : '' }}>
                                    No Branch Card - Print Temporary Card</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                @if ($application->branch_card_availability)
                                    Current Status:
                                    <strong>{{ $application->branch_card_availability == 'has_branch_card' ? 'Has Branch Card (Integration)' : 'No Branch Card (Temporary Print)' }}</strong>
                                @else
                                    This determines whether the bus pass will be integrated into the person's branch
                                    card or printed as a temporary card.
                                @endif
                            </small>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i>
                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                Forward Remarks (Optional)
                            @elseif(auth()->user()->hasRole('Subject Clerk (DMOV)'))
                                Forward Remarks (Optional)
                            @else
                                Approval Remarks (Optional)
                            @endif
                        </label>
                        <textarea class="form-control" id="remarks{{ $application->id }}" name="remarks" rows="3"
                            placeholder="@if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)'])) Enter any remarks for forwarding this application...@else Enter any remarks for this approval... @endif"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                This forward action will send the application to the next approval level (Staff
                                Officer).
                            @elseif(auth()->user()->hasRole('Subject Clerk (DMOV)'))
                                This forward action will send the application to the next approval level (Staff Officer
                                2 DMOV).
                                <br><strong>Note:</strong> You must specify both SLTB Season availability and Branch
                                Card availability before forwarding.
                            @else
                                This approval will move the application to the next stage in the workflow.
                            @endif
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        @if (auth()->user()->hasRole(['Bus Pass Subject Clerk (Branch)', 'Subject Clerk (DMOV)']))
                            <i class="fas fa-arrow-right"></i> Confirm Forward
                        @else
                            <i class="fas fa-check"></i> Confirm Approval
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
