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
        
        // Gate::define('check_role', function ($user, $role) {
        //     // $roles lúc này sẽ luôn là một mảng, ví dụ: ["ROLE_SUPER_ADMIN", "ROLE_SW_ADMIN"]
        //     dd([
        //         'user_roles' => $user->roles,
        //         'required_role' => $role,
        //     ]);
        //     return $user->hasRole($role);
        // });

        Gate::define('hasRole', function ($user, $role) { // Định nghĩa một Gate có tên 'check_role' để kiểm tra vai trò của người dùng
            // dd($role);
            return $user->hasRole($role);
        });

        
            

    }

    
}
