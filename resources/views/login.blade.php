
<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        <meta charset="utf-8">

        
        @vite([ 'resources/js/app.js', 'resources/css/icons/themify-icons.css', 'resources/css/app.css'])
    </head>

    <body style="background-image: url('/imgs/moon_festival_background.png');">

        {{-- <div class="d-flex flex-grow-1 overflow-hidden vh-100 align-items-center justify-content-center">
            <div class="container my-auto p-4" style="max-width: 1100px;">
                <div class="row align-items-stretch g-4">
                    
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

                    <div class="col-md-6 d-flex">
                        
                        <div class="bg-white p-4 rounded shadow-sm w-100 d-flex flex-column overflow-hidden" style="min-height: 380px;">
                            <h3 class="h5 border-bottom pb-2 mb-3 text-dark">📢 Update Notice</h3>
                            
                            <!-- Danh sách thông báo -->
                            <div class="updates-list flex-grow-1 overflow-auto pe-2">
                                <div class="update-item mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary mb-1">v1.2</span>
                                    <small class="text-muted float-end">23/09/2026</small>
                                    <h6 class="mb-1 fw-bold text-dark">Cập nhật hệ thống</h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li>Thêm vào 4 hạng mục: Kho Spectre - Crown, kho HPS, biên bản nghiệm thu cho partner onsite & hủy hàng</li>
                                        <li>Thay đổi logic tự cancel ticket từ 21 ngày xuống 5 ngày </li>
                                        <li>Đồng bộ lịch sử xuất máy/part của kho Crown & Spectre với hạng mục 4</li>
                                        <li>Thay đổi process Invoice Exceptional đối với người log thuộc team CC</li>
                                        <li>Thêm chức năng thông báo khi có ticket tạo đối với 1 số hạng mục</li>

                                        
                                    </ul>
                                    <h6 class="mb-1 fw-bold text-dark">Bug fixed</h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li>Fix lỗi khi user gửi xác thực training bị treo hàng chờ</li>
                                        <li>Fix lỗi khi tạo ticket xong form log ticket không bỏ animation loading</li>
                                        <li>Fix lỗi khi phiên làm việc của user vẫn còn hiệu lực nhưng hệ thống không direct vào trang chủ</li>
                                    </ul>
                                </div>

                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> --}}

        <div class="d-flex flex-grow-1 overflow-hidden vh-100 align-items-center justify-content-center">
            <div class="container my-auto p-4" style="max-width: 1100px;">
                <div class="row align-items-stretch g-4">
                    
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

                    <div class="col-md-6 d-flex position-relative">
    
                        <img src="/imgs/moon_festival_3.png" 
                            alt="Moon Festival Kids" 
                            class="position-absolute start-0 w-100" 
                            style="bottom: 100%; z-index: 10; pointer-events: none; margin-bottom: -24 px; object-fit: contain; transform: scaleX(-1);">

                        <!-- Box Update Notice (Nằm riêng biệt bên dưới) -->
                        <div class="bg-white p-4 rounded shadow-sm w-100 d-flex flex-column overflow-hidden" style="min-height: 380px;">
                            <h3 class="h5 border-bottom pb-2 mb-3 text-dark">📢 Update Notice</h3>
                            
                            <!-- Danh sách thông báo -->
                            <div class="updates-list flex-grow-1 overflow-auto pe-2">
                                <div class="update-item mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary mb-1">v1.2</span>
                                    <small class="text-muted float-end">23/09/2026</small>
                                    <h6 class="mb-1 fw-bold text-dark">Cập nhật hệ thống</h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li>Thêm vào 4 hạng mục: Kho Spectre - Crown, kho HPS, biên bản nghiệm thu cho partner onsite & hủy hàng</li>
                                        <li>Thay đổi logic tự cancel ticket mượn part từ 21 ngày xuống 5 ngày</li>
                                        <li>Đồng bộ lịch sử xuất máy/part của kho Crown & Spectre với hạng mục 4</li>
                                        <li>Thay đổi process Invoice Exceptional đối với người log thuộc team CC</li>
                                    </ul>
                                    <h6 class="mb-1 fw-bold text-dark mt-2">Bug fixed</h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li>Fix lỗi khi user gửi xác thực training bị treo hàng chờ</li>
                                        <li>Fix lỗi khi tạo ticket xong form log ticket không bỏ animation loading</li>
                                        <li>Fix lỗi khi phiên làm việc của user vẫn còn hiệu lực nhưng hệ thống không direct vào trang chủ</li>
                                    </ul>
                                </div>
                            </div>

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

