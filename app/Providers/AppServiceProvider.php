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

        // Gate::define('access-software-tickets-menu', function ($user) {
        //     return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPPORT'); // Cho phép người dùng có role 'admin' hoặc 'support' truy cập menu quản lý ticket
        // });
    }

    
}
