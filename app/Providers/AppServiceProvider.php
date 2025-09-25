<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        Gate::define('manage_user_accounts', function ($user) {
            return $user->hasRole('System Administrator (DMOV)');
        });
    }
}
