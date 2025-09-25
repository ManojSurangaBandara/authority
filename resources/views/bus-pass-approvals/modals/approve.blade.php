<!-- Approve Application Modal -->
<div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.approve', $application) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white">
                        @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
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
                            @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                Forward Confirmation
                            @else
                                Approval Confirmation
                            @endif
                        </h5>
                        @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                            You are about to forward the bus pass application for:
                        @else
                            You are about to approve the bus pass application for:
                        @endif
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    <div class="form-group">
                        <label for="remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i> 
                            @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                Forward Remarks (Optional)
                            @else
                                Approval Remarks (Optional)
                            @endif
                        </label>
                        <textarea class="form-control" 
                                  id="remarks{{ $application->id }}" 
                                  name="remarks" 
                                  rows="3" 
                                  placeholder="@if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))Enter any remarks for forwarding this application...@else Enter any remarks for this approval...@endif"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                This forward action will send the application to the next approval level (Staff Officer).
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
                        @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
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
