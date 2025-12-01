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
                <div class="modal-body">
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
