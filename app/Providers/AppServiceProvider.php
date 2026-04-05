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
        Gate::define('check_role', function ($user, $role) { // Định nghĩa một Gate có tên 'check_role' để kiểm tra vai trò của người dùng
            // dd($role);
            return $user->hasRole($role);
        });

        
    }

    
}
