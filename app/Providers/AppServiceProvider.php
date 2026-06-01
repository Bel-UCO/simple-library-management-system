<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;
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
        Gate::define('is-admin', function(User $user) {
            return $user->is_admin == true ? Response::allow() : Response::deny('You must be an administrator or higher.');

        });

        Gate::define('is-member', function(User $user) {
            return $user->is_admin == false ? Response::allow() : Response::deny('Please Login to Continue.');

        });
    }
}
