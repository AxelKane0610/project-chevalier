<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/software-ticket-details.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="User Profile">   
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>
            <li>
                <form action="" class="js-input-required-btn" >
                    <button type="button"><i class="ti-search"></i>Search</button>
                </form>
                
            </li>
        </x-common-header>

        <div class="user-info_content">

            <div class="card avatar-section">
                <img src="{{ asset('imgs/user-default-image.png') }}" alt="" width="200px" height="200px">
                <h2>Nguyễn Thanh Hải</h2>
                <div class="card-button-container">
                    <form action="" class="js-input-required-btn" >
                        <button type="button"><i class="ti-pencil"></i>Edit</button>
                    </form>
                    <form action="" class="js-input-required-btn" >
                        <button type="button"><i class="ti-reload"></i>Đổi mật khẩu</button>
                    </form>
                </div>

            </div>

            <div class="card detail-section">
                
            </div>

            <div class="card training-complete-section">
                
            </div>
            
        </div>
    </body>

</html>