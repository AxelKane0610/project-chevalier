<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/user-profile.js', 'resources/css/app.css', 'resources/css/icons/themify-icons.css'])
        
    </head>


    <body class="background-enable">
        <x-common-header title="User Profile">   
            <li>
                <a href="{{ url('/main-menu') }}" class="button">
                    <button><i class="ti-home"></i>
                    Home
                    </button>
                </a>
            </li>
            <li>
                <div class="search-container">
                    <form action="">
                        <button type="button" id="btn-toggle-search" class="nav-btn search-btn">
                            <i class="ti-search"></i> Search
                        </button>

                        <div id="search-dropdown" class="search-dropdown-box hidden">
                            <div class="search-input-group">
                                
                                @livewire('quick-search-dropdown')
                            </div>
                        </div>
                    </form>
                </div>
                
            </li>
        </x-common-header>

        

        <div class="container-fluid py-4">
            <div class="row g-4 justify-content-center">

                {{-- Profile --}}
                <div class="col-12 col-lg-6">
                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-header bg-white border-0 text-center py-3">
                            <h2 class="mb-0">Profile Information</h2>
                        </div>

                        <div class="card-body p-4">

                            <div class="info-item">
                                <label>Fullname</label>
                                <div class="info-value">
                                    {{ $user->fullname }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Username</label>
                                <div class="info-value">
                                    {{ $user->name }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Email</label>
                                <div class="info-value">
                                    {{ $user->email }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Số điện thoại</label>
                                <div class="info-value">
                                    {{ $user->phone_number }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Learner ID</label>
                                <div class="info-value">
                                    {{ $user->learner_id }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Site</label>
                                <div class="info-value">
                                    <span class="ticket-status {{ $user->site_id_data['class'] }}">
                                        {{ $user->site_id_data['text'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Team</label>
                                <div class="info-value">
                                    <span class="ticket-status {{ $user->team_data['class'] }}">
                                        {{ $user->team_data['text'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Leader</label>
                                <div class="info-value">
                                    {{ $user->leader?->fullname ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="info-item">
                                <label>Các role đang có trên hệ thống</label>
                                <div class="info-value">
                                    {{ implode(', ', $user->roles) }}
                                </div>
                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="card-footer bg-white border-0 pb-4">
                            <div class="d-flex justify-content-center align-items-center gap-3">

                                <form action="" class="js-input-required-btn">
                                    <button type="button">
                                        <i class="ti-pencil"></i>
                                        Edit
                                    </button>
                                </form>

                                <form action="" class="js-input-required-btn"
                                    data-target="change-password">
                                    <button type="button">
                                        <i class="ti-reload"></i>
                                        Đổi mật khẩu
                                    </button>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>


                {{-- Training Certificate --}}
                
                <div class="col-12 col-lg-6" >
                    <div id="all-certificates-table-container" data-url="/user-profile/filter-all-certificates/{{ $user->id }}">
                        <div class="card h-100 shadow-sm border-0">
                        
                            <div class="card-header bg-white border-0 text-center py-3">
                                <h2 class="mb-0">Training Certificate</h2>
                            </div>

                            <div class="card-body p-4">
                                <div class="common-table-filter">
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input class="ajax-search" type="text" placeholder="Search course ID of certificates" id="search-all-certificates-table">
                                    </div>
                                </div>

                                {{-- Training certificate content --}}
                                <div class="certificates-data-container">
                                    @include('tables.all-certificates-tables')
                                </div>
                                
                            </div>

                            <div class="card-footer bg-white border-0 pb-4">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    
                                    <form action="" class="js-input-required-btn" data-target="edit-upload-certificates">
                                        <button type="button" >
                                            <i class="ti-upload"></i>
                                            Upload Certificates
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                

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

        <x-common-ticket-form title="Edit/Upload Certificates" action1="{{route('user.edit-upload-certificates', auth()->user()->id)}}" id="edit-upload-certificates">
            <label><b>Attachments</b></label>
            
            @if($certificates->count() > 0) 
                <x-common-attachments-table>
                    @foreach($certificates as $certificate)
                        <tr>
                            <td>
                                {{ $certificate->name ?? 'File đính kèm' }}
                            </td>
                            <td>

                                <div>
                                    
                                    <a href="{{ asset('attachments/' . $certificate->file_path) }}" target="_blank" class="btn btn-info">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <input type="checkbox" name="delete_files[]" value="{{ $certificate->id }}" id="del_{{ $certificate->id }}">
                                    <label for="del_{{ $certificate->id }}">
                                        Xóa
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-common-attachments-table>
                        
                <small class="text-muted">Tích vào ô "Xóa" nếu muốn gỡ bỏ file đính kèm trước đó.</small>
            @else
                <p class="text-muted">Không có file nào được đính kèm</p>
            @endif
            
            <label class="ticket-form-body-input">Đính kèm thêm files:</label>
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>
            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" >Edit/Upload Certificates</button> 
            </x-slot:footer>
        </x-common-ticket-form>
    </body>

</html>