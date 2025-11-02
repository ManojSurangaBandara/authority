@if (isset($pendingApprovalsCount))
    <meta name="pending-approvals-count" content="{{ $pendingApprovalsCount }}">
    <script>
        // Set global variable for pending approvals count
        window.pendingApprovalsCount = {{ $pendingApprovalsCount }};
        // Also set as data attribute on body for backup
        document.addEventListener('DOMContentLoaded', function() {
            document.body.setAttribute('data-pending-approvals', '{{ $pendingApprovalsCount }}');
        });
    </script>
@endif
