Bus Pass Approvals @if (getPendingApprovalsCount() > 0)
    <span class="right badge badge-danger">{{ getPendingApprovalsCount() }}</span>
@endif
