<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/user-profile.js', 'resources/css/icons/themify-icons.css'])
        
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
                <h2>{{ $user->fullname }}</h2>
                <div class="card-button-container">
                    <form action="" class="js-input-required-btn" >
                        <button type="button"><i class="ti-pencil"></i>Edit</button>
                    </form>
                    <form action="" class="js-input-required-btn" data-target="change-password" >
                        <button type="button"><i class="ti-reload"></i>Đổi mật khẩu</button>
                    </form>
                </div>

            </div>

            <div class="card detail-section">
                <h2>Profile Information</h2>

                <div class="info-grid">

                    <div class="info-item">
                        <label>Username</label>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>

                    <div class="info-item">
                        <label>Email</label>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>

                    <div class="info-item">
                        <label>Số điện thoại</label>
                        <div class="info-value">{{ $user->phone_number }}</div>
                    </div>

                    <div class="info-item">
                        <label>Learner ID</label>
                        <div class="info-value">{{ $user->learner_id }}</div>
                    </div>

                    <div class="info-item">
                        <label>Site</label>
                        {{-- <div class="info-value">{{ $user->site_id }}</div> --}}
                        <div class="info-value">
                            <span class="ticket-status {{ $user->site_id_data['class'] }}">
                                {{ $user->site_id_data['text'] }}
                            </span>
                        </div>
                    </div>

                    

                    <div class="info-item">
                        <label>Team</label>
                        {{-- <div class="info-value">{{ $user->team }}</div> --}}
                        <div class="info-value">
                            
                            <span class="ticket-status {{ $user->team_data['class'] }}">
                                {{ $user->team_data['text'] }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Leader</label>
                        <div class="info-value">{{ $user->leader?->fullname }}</div>
                    </div>

                    <div class="info-item">
                        <label>Các role đang có trên hệ thống</label>
                        <div class="info-value">{{ implode(', ', $user->roles) }}</div>
                    </div>
                </div>
            </div>

            <div class="card training-complete-section">
                <h2>Training Certificate</h2>
                
            </div>
            
        </div>

        <x-common-ticket-form id="change-password" title="Đổi mật khẩu" action1="{{route('user.change-password')}}" method="POST">
            @csrf
            <label>New password</label>
            <input type="password" placeholder="Nhập Password muốn đổi"  name="new_password" required>
            <label>Confirm New password</label>
            <input type="password" placeholder="Confirm new password"  name="confirm_new_password" required>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Đổi mật khẩu</button> 
            </x-slot:footer>
        </x-common-ticket-form>
    </body>

</html>