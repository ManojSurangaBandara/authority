<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Models\BusPassApplication;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Gates for AdminLTE menu system compatibility
        Gate::define('system_admin_access', function ($user) {
            return $user->hasRole('System Administrator (DMOV)');
        });

        Gate::define('operational_user_access', function ($user) {
            return $user->hasAnyRole([
                'Bus Pass Subject Clerk (Branch)',
                'Staff Officer (Branch)',
                'Director (Branch)',
                'Director (DMOV)',
                'Subject Clerk (DMOV)',
                'Staff Officer 1 (DMOV)',
                'Staff Officer 2 (DMOV)',
                'Col Mov (DMOV)'
            ]);
        });

        // Gate for Bus Pass Approvals menu - excludes Staff Officer 1 (DMOV)
        Gate::define('access_bus_pass_approvals', function ($user) {
            return $user->hasAnyRole([
                'Bus Pass Subject Clerk (Branch)',
                'Staff Officer (Branch)',
                'Subject Clerk (DMOV)',
                'Staff Officer 2 (DMOV)',
                'Col Mov (DMOV)',
                'Director (DMOV)'
            ]) && !$user->hasRole('Staff Officer 1 (DMOV)');
        });

        // Gate for Bus Pass Integration - DMOV users only
        Gate::define('access_bus_pass_integration', function ($user) {
            return $user->hasAnyRole([
                'Subject Clerk (DMOV)',
                'Staff Officer 2 (DMOV)',
                'Col Mov (DMOV)',
                'Director (DMOV)'
            ]);
        });

        // Gate for QR Download - Branch Clerk and DMOV Clerk only
        Gate::define('access_qr_download', function ($user) {
            return $user->hasAnyRole([
                'Bus Pass Subject Clerk (Branch)',
                'Subject Clerk (DMOV)'
            ]);
        });

        // Gate for Reports - DMOV users only
        Gate::define('access_reports', function ($user) {
            return $user->hasAnyRole([
                'Subject Clerk (DMOV)',
                'Staff Officer 2 (DMOV)',
                'Col Mov (DMOV)',
                'Director (DMOV)',
                'System Administrator (DMOV)'
            ]);
        });

        // Individual role gates for more granular control if needed
        Gate::define('bus_pass_subject_clerk_branch', function ($user) {
            return $user->hasRole('Bus Pass Subject Clerk (Branch)');
        });

        Gate::define('staff_officer_branch', function ($user) {
            return $user->hasRole('Staff Officer (Branch)');
        });

        Gate::define('director_branch', function ($user) {
            return $user->hasRole('Director (Branch)');
        });

        Gate::define('branch_user_access', function ($user) {
            return $user->hasAnyRole([
                'Bus Pass Subject Clerk (Branch)',
                'Staff Officer (Branch)',
                'Director (Branch)'
            ]);
        });

        Gate::define('manage_user_accounts', function ($user) {
            return $user->hasRole('System Administrator (DMOV)');
        });

        // Register global helper function for pending approvals count
        if (!function_exists('getPendingApprovalsCount')) {
            function getPendingApprovalsCount()
            {
                if (!Auth::check()) {
                    return 0;
                }

                /** @var User $user */
                $user = Auth::user();
                $pendingCount = 0;

                // Calculate pending approvals based on user role
                if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_subject_clerk')
                        ->where('establishment_id', $user->establishment_id)
                        ->count();
                } elseif ($user->hasRole('Staff Officer (Branch)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_branch')
                        ->where('establishment_id', $user->establishment_id)
                        ->count();
                } elseif ($user->hasRole('Subject Clerk (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'forwarded_to_movement')->count();
                } elseif ($user->hasRole('Staff Officer 2 (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_2_mov')->count();
                } elseif ($user->hasRole('Col Mov (DMOV)') || $user->hasRole('Director (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_col_mov')->count();
                }

                return $pendingCount;
            }
        }

        // Register Blade directive for approval count badge
        Blade::directive('approvalCount', function () {
            return '<?php
                $count = getPendingApprovalsCount();
                echo $count > 0 ? "<span class=\"badge badge-danger right\">$count</span>" : "";
            ?>';
        });

        // Add view composer to inject the pending approvals count into all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $pendingCount = 0;

                if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_subject_clerk')
                        ->where('establishment_id', $user->establishment_id)
                        ->count();
                } elseif ($user->hasRole('Staff Officer (Branch)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_branch')
                        ->where('establishment_id', $user->establishment_id)
                        ->count();
                } elseif ($user->hasRole('Subject Clerk (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'forwarded_to_movement')->count();
                } elseif ($user->hasRole('Staff Officer 2 (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_staff_officer_2_mov')->count();
                } elseif ($user->hasRole('Col Mov (DMOV)') || $user->hasRole('Director (DMOV)')) {
                    $pendingCount = BusPassApplication::where('status', 'pending_col_mov')->count();
                }

                $view->with('pendingApprovalsCount', $pendingCount);
            } else {
                $view->with('pendingApprovalsCount', 0);
            }
        });
    }
}
