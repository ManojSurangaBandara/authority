<!-- Reject Application Modal -->
<div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('bus-pass-approvals.reject', $application) }}">
                @csrf
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-times"></i>
                        Reject Application #{{ $application->id }}
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Rejection Confirmation</h5>
                        You are about to reject the bus pass application for:
                        <br><strong>{{ $application->person->name }}</strong> ({{ $application->person->regiment_no }})
                    </div>

                    <div class="form-group">
                        <label for="reject_remarks{{ $application->id }}">
                            <i class="fas fa-comment"></i> Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                  id="reject_remarks{{ $application->id }}" 
                                  name="remarks" 
                                  rows="4" 
                                  placeholder="Please provide a detailed reason for rejection..."
                                  required></textarea>
                        <small class="form-text text-muted">
                            Please provide a clear reason for rejection to help the applicant understand the decision.
                        </small>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> This action will permanently reject the application. 
                            The applicant will need to submit a new application if required.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
