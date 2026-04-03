
<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">

        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    </head>

    <body>
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

        @if(session('login_error'))
        <x-common-dialog title="Đăng nhập thất bại">
            <p>Sai tài khoản hoặc mật khẩu</p>
        </x-common-dialog>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showDialog()
            })
        </script>

        @endif
    </body>



</html>

