<!-- Approve Application Modal -->
<div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.approve', $application) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-check"></i>
                        Approve Application #{{ $application->id }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-info-circle"></i> Approval Confirmation</h5>
                        You are about to approve the bus pass application for:
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    <div class="form-group">
                        <label for="remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i> Approval Remarks (Optional)
                        </label>
                        <textarea class="form-control" 
                                  id="remarks{{ $application->id }}" 
                                  name="remarks" 
                                  rows="3" 
                                  placeholder="Enter any remarks for this approval..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            This approval will move the application to the next stage in the workflow.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
