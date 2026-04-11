<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        $user = $request->user();

        // 1. Kiểm tra xem user đã đăng nhập chưa
        if (!$user) {
            return redirect('login');
        }

        // 2. Lấy mảng roles từ database (đã được cast thành array)
        $userRoles = $user->roles ?? [];

        // 3. Kiểm tra xem user có ít nhất một trong các quyền được yêu cầu không
        // array_intersect trả về các phần tử chung giữa 2 mảng
        if (!empty(array_intersect($userRoles, $allowedRoles))) {
            return $next($request);
        }

        // Nếu không có quyền, trả về lỗi 403 hoặc chuyển hướng
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}
