<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
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
        

        Gate::define('hasRole', function ($user, $roles) {
            // ép về array nếu truyền 1 role
            $roles = is_array($roles) ? $roles : [$roles];

            return collect($user->roles)->intersect($roles)->isNotEmpty();
        });

        
            

    }

    
}
