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
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Returning to Branch Clerk:</strong>
                        This application was not recommended by the DMOV Clerk and is being forwarded back to the Branch
                        Clerk for review.
                    </div>

                    
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
