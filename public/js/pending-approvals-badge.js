// Global pending approvals badge updater
window.updatePendingApprovalsBadge = function (count) {
    // Find the Bus Pass Approvals menu item by href
    const menuLinks = document.querySelectorAll('a[href*="bus-pass-approvals"]:not(.dropdown-item)');
    menuLinks.forEach(function (link) {
        const textElement = link.querySelector('p');
        if (textElement) {
            // Remove existing badge if it exists
            const existingBadge = textElement.querySelector('.badge');
            if (existingBadge) {
                existingBadge.remove();
            }

            // Add the badge if count > 0
            if (count > 0) {
                const badge = document.createElement('span');
                badge.className = 'right badge badge-danger';
                badge.textContent = count;
                textElement.appendChild(badge);
            }
        }
    });
};

// Function to get pending approvals count from various sources
function getPendingCount() {
    // Try global variable first
    if (typeof window.pendingApprovalsCount !== 'undefined') {
        return window.pendingApprovalsCount;
    }

    // Try to find it in a meta tag
    const metaTag = document.querySelector('meta[name="pending-approvals-count"]');
    if (metaTag) {
        return parseInt(metaTag.getAttribute('content')) || 0;
    }

    // Try to find it in a data attribute on body
    const bodyCount = document.body.getAttribute('data-pending-approvals');
    if (bodyCount) {
        return parseInt(bodyCount) || 0;
    }

    return 0;
}

// Auto-update badge on page load
document.addEventListener('DOMContentLoaded', function () {
    const count = getPendingCount();

    if (count >= 0) {
        window.updatePendingApprovalsBadge(count);
    }

    // Also try after a delay to ensure menu is fully rendered
    setTimeout(function () {
        const delayedCount = getPendingCount();
        if (delayedCount >= 0) {
            window.updatePendingApprovalsBadge(delayedCount);
        }
    }, 500);
});
