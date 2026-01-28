<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BusPassApplication;
use App\Models\User;

class InjectPendingApprovalsCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $pendingCount = 0;

            // Calculate pending approvals based on user role
            if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
                $pendingCount = BusPassApplication::where('status', 'pending_subject_clerk')
                    ->where('establishment_id', $user->establishment_id)
                    ->count();
            } elseif ($user->hasRole('Staff Officer (Branch)')) {
                $pendingCount = BusPassApplication::whereIn('status', ['pending_staff_officer_branch', 'rejected_for_integration'])
                    ->where('establishment_id', $user->establishment_id)
                    ->count();
            } elseif ($user->hasRole('Subject Clerk (DMOV)')) {
                $pendingCount = BusPassApplication::where('status', 'forwarded_to_movement')->count();
            } elseif ($user->hasRole('Staff Officer 2 (DMOV)')) {
                $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_2_mov')->count();
            } elseif ($user->hasAnyRole(['Col Mov (DMOV)', 'Director (DMOV)'])) {
                $pendingCount = BusPassApplication::where('status', 'pending_col_mov')->count();
            }

            // Share the count with all views
            View::share('pendingApprovalsCount', $pendingCount);
        }

        $response = $next($request);

        // If this is an HTML response and user is authenticated, inject the meta tag and script
        if (Auth::check() && $response instanceof \Illuminate\Http\Response) {
            $content = $response->getContent();

            // Only modify HTML responses
            if (strpos($content, '<html') !== false) {
                // Inject meta tag in head
                $metaTag = '<meta name="pending-approvals-count" content="' . $pendingCount . '">';
                $content = str_replace('<head>', '<head>' . $metaTag, $content);

                // Inject script before closing body tag
                $script = '<script>window.pendingApprovalsCount = ' . $pendingCount . '; document.body.setAttribute("data-pending-approvals", "' . $pendingCount . '");</script>';
                $content = str_replace('</body>', $script . '</body>', $content);

                $response->setContent($content);
            }
        }

        return $response;
    }
}
