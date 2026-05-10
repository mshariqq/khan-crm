<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Staff;

class PermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Define a global check for permissions
        Gate::before(function ($user, $ability) {
            if ($user instanceof Staff) {
                return $user->can($ability) ? true : null;
            }
        });
    }
}
