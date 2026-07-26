
<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">

        
        @vite([ 'resources/js/app.js', 'resources/css/icons/themify-icons.css'])
    </head>

    <body>
        <!-- <div class="d-flex flex-grow-1 overflow-hidden vh-100">
            <div class="container-fluid my-5 flex-grow-1 g-5">
                <div class="row flex-grow-1 h-100">
                    <div class="col-6">
                        <div class="sign-in-menu">
                            <div class="sign-in-box">
                                <h1>Welcome</h1>
                                <h2>Sign In</h2>

                                <div class="sign-in-input">
                                    <form action="{{ route('login') }}" method="post" class="sign-in-input">
                                        @csrf
                                        <input type="text" placeholder="Username" class="sign-in-input-field" name="Username" value="{{ old('Username') }}" required>
                                        <input type="password" placeholder="Password" class="sign-in-input-field" name="Password" required>
                                        <button id="sign-in-btn">
                                            Sign In
                                        </button>
                                        
                                    </form>
                                </div>
                    
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="bg-white p-3 rounded shadow-sm h-100">
                            

                        </div>
                    </div>

                </div>
            </div>
        </div> -->

        <div class="d-flex flex-grow-1 overflow-hidden vh-100 align-items-center justify-content-center">
    <!-- Thêm style max-width để thu gọn khoảng cách giữa 2 khung trên màn hình rộng -->
    <div class="container my-auto p-4" style="max-width: 1100px;">
        <div class="row align-items-stretch g-4">
            
            <!-- Cột bên trái: Form Đăng Nhập -->
            <div class="col-md-6 d-flex">
                <div class="bg-white p-4 rounded shadow-sm w-100 d-flex flex-column justify-content-center">
                    <h1 class="h3 mb-2 text-dark">Welcome</h1>
                    <h2 class="h5 text-muted mb-4">Sign In</h2>

                    <form action="{{ route('login') }}" method="post" class="d-flex flex-column gap-3">
                        @csrf
                        <div>
                            <input type="text" 
                                   placeholder="Username" 
                                   class="form-control" 
                                   style="background-color: #fff; color: #000; border: 1px solid #ced4da;"
                                   name="Username" 
                                   value="{{ old('Username') }}" 
                                   required>
                        </div>
                        <div>
                            <input type="password" 
                                   placeholder="Password" 
                                   class="form-control" 
                                   style="background-color: #fff; color: #000; border: 1px solid #ced4da;"
                                   name="Password" 
                                   required>
                        </div>
                        <button type="submit" id="sign-in-btn" class="btn btn-primary w-100 mt-2">
                            Sign In
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cột bên phải: Thông báo Updates -->
            <div class="col-md-6 d-flex">
                <div class="bg-white p-4 rounded shadow-sm w-100 d-flex flex-column overflow-hidden" style="min-height: 380px;">
                    <h3 class="h5 border-bottom pb-2 mb-3 text-dark">📢 Update Notice</h3>
                    
                    <!-- Danh sách thông báo -->
                    <div class="updates-list flex-grow-1 overflow-auto pe-2">
                        <div class="update-item mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary mb-1">v1.1.0</span>
                            <small class="text-muted float-end">26/07/2026</small>
                            <h6 class="mb-1 fw-bold text-dark">Cập nhật hệ thống</h6>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>Thay đổi giao diện của toàn bộ các hạng mục.</li>
                                <li>Thay đổi logic tạo ticket thu hồi part def từ 14 ngày xuống 10 ngày tính từ lúc tạo ticket chuyển part good.</li>
                                <li>Thêm tính năng "Request Sale Support" đối với các ticket hóa đơn xuất sau bị reject.</li>
                                <li>Thêm phân trang (Pagination) khi ticket vượt 1 số lượng nhất định.</li>
                                <li>Tăng thời gian phiên làm việc từ 3 tiếng > 9 tiếng.</li>
                                
                            </ul>
                            <h6 class="mb-1 fw-bold text-dark">Bug fixed</h6>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>Fix lỗi khi user treo ở trang login lâu khi đăng nhập bị báo lỗi 419.</li>
                                <li>Fix lỗi khi close ticket TTEX bị mất ngày điều tin.</li>
                                <li>Fix lỗi khi Microsoft Power Automate trả kết quả approve hoặc reject về local bị Cloudflare chặn lại.</li>
                            </ul>
                        </div>

                        <!-- <div class="update-item mb-3 pb-2 border-bottom">
                            <span class="badge bg-secondary mb-1">v1.1.5</span>
                            <small class="text-muted float-end">15/07/2026</small>
                            <h6 class="mb-1 fw-bold text-dark">Bảo trì hệ thống</h6>
                            <p class="text-muted small mb-0">Tối ưu hóa hiệu năng cơ sở dữ liệu và nâng cao tốc độ phản hồi API.</p>
                        </div> -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

        

        
        @if(session('login_error'))
            <x-common-dialog title="Đăng nhập thất bại">
                <p>Sai tài khoản hoặc mật khẩu</p>
            </x-common-dialog>

            <script>
                document.addEventListener("DOMContentLoaded", function() 
                {
                    Swal.fire({
                    title: 'Đăng nhập thất bại',
                    text: 'Sai tài khoản hoặc mật khẩu',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                })
                
            </script>
                

        @endif
    </body>



</html>

