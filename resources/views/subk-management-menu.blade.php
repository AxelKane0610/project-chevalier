<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/subk-management.js', 'resources/css/app.css',  'resources/css/icons/themify-icons.css', ])
    
    </head>

    <body class="background-enable">

        <div class="" id="subk-management-menu">

            <x-common-header title="SubK Management">
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>
            <li>
                <form action="#">
                    <button type="submit"><i class="ti-search"></i>Search</button>
                </form>
            </li>
            </x-common-header>

            <div class="subk-management-menu-content">

                <form>
                    <button type="button" class="js-input-required-btn" data-target="create-user-form"><i class="ti-plus"></i>Create User</button>
                </form>

                <div class="common-table-container">
                    <h2>Users </h2>

                    <table id = "subk-management-users-table" class="common-table" width="100%" >
                        <tr>
                            <th width="10%">Actions</th>
                            <th width="14%">Username</th>
                            <th width="14%">Fullname</th>
                            <th width="39%">Roles</th>
                            <th width="14%">Email</th>
                            <th width="9%">Site</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        
                                        <form class="js-input-required-btn" data-target="edit-user-info" action="" method="PATCH">
                                            <button type="button" 
                                            class="btn-edit-user"
                                            data-id="{{ $user->id }}"
                                            data-leaderid="{{ $user->leader?->fullname  }}"
                                            data-siteid="{{ $user->site_id }}"
                                            data-fullname="{{ $user->fullname }}"
                                            data-roles='@json($user->roles ?? [])'
                                            data-email="{{ $user->email }}"
                                            data-phonenumber="{{ $user->phone_number }}"
                                            data-learnerid="{{ $user->learner_id }}"
                                            data-team="{{ $user->team }}"><i class="ti-pencil"></i></button>
                                        </form>
                                        <button><i class="ti-na"></i></button>
                                        
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->fullname }}</td>
                                    <td>{{ implode(', ', $user->roles) }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->site_id }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                
                
                </div>

                

            </div>

        </div>
        
        <x-common-ticket-form title="Tạo user mới" id="create-user-form" action1="create-new-user">
            <label class="ticket-form-body-input">Username</label>
            <input name="name" class="ticket-form-body-input" required></input>

            <label class="ticket-form-body-input">Password</label>
            <input name="password" class="ticket-form-body-input" type="password" required></input>

            <label class="ticket-form-body-input">Họ và Tên</label>
            <input name="fullname" class="ticket-form-body-input"></input>
            
            <label class="ticket-form-body-input">Team</label>
            <select name="team" class="ticket-form-body-input">
                <option value="">--Select option--</option>
                <option value="1">ASRC</option>
                <option value="2">Call Center</option>
                <option value="3">Customer Engineer</option>
                <option value="4">Front Counter</option>
                <option value="5">Call Admin</option>
                <option value="6">Operation</option>

            </select>

            <label class="ticket-form-body-input">Leader</label>
            <select name="leader_id" class="ticket-form-body-input">
                <option value="">--Select option--</option>
                <option value="lecuong@hp.com">Lê Thế Cường</option>
                <option value="ha.nguyen3@hp.com">Nguyễn Tấn Hạ</option>
                <option value="phuong.phan-quoc@hp.com">Phan Quốc Phương</option>
                <option value="huong.dang@hp.com">Đặng Thanh Hương</option>
                <option value="leduy@hp.com">Lê Quang Khoa Duy</option>
                <option value="le.ngoc@hp.com">Lê Thị Thu Ngọc</option>
                <option value="trinh.huong@hp.com">Trịnh Minh Hướng</option>
                <option value="phamhieu@hp.com">Phạm Trung Hiếu</option>
            </select>

            <label class="ticket-form-body-input">Site</label>
            <select name="site_id" class="ticket-form-body-input" required>
                <option value="1">Hồ Chí Minh</option>
                <option value="2">Hà Nội</option>
                <option value="3">Đà Nẵng</option>
                <option value="4">Cần Thơ</option>
                <option value="5">Call Center</option>
            </select>

            <label class="ticket-form-body-input">Email</label>
            <input name="email" class="ticket-form-body-input" required></input>

            <label class="ticket-form-body-input">Learner ID</label>
            <input name="learner_id" class="ticket-form-body-input" required></input>

            <label class="ticket-form-body-input">Số điện thoại</label>
            <input name="phone_number" class="ticket-form-body-input"></input>

            

            <label class="ticket-form-body-input">Roles</label>
            <select multiple="multiple" name="roles[]" id="role-select" required >
                <option value="ROLE_SUPER_ADMIN">ROLE_SUPER_ADMIN</option>

                <option value="ROLE_SW_TICKET_ADMIN">ROLE_SW_TICKET_ADMIN</option>
                <option value="ROLE_SW_TICKET_USER">ROLE_SW_TICKET_USER</option>
                <option value="ROLE_APPROVE_ROLLBACK">ROLE_APPROVE_ROLLBACK</option>
                <option value="ROLE_APPROVE_EXPORT_DATA">ROLE_APPROVE_EXPORT_DATA</option>

                <option value="ROLE_TTEX_TICKET_USER">ROLE_TTEX_TICKET_USER</option>
                <option value="ROLE_TTEX_TICKET_ADMIN">ROLE_TTEX_TICKET_ADMIN</option>

                <option value="ROLE_LASER_ENGRAVING_USER">ROLE_LASER_ENGRAVING_USER</option>
                <option value="ROLE_LASER_ENGRAVING_ADMIN">ROLE_LASER_ENGRAVING_ADMIN</option>

                <option value="ROLE_LOAN_UNIT_USER">ROLE_LOAN_UNIT_USER</option>
                <option value="ROLE_LOAN_UNIT_ADMIN">ROLE_LOAN_UNIT_ADMIN</option>

                <option value="ROLE_SUBMIT_TRAINING_USER">ROLE_SUBMIT_TRAINING_USER</option>
                <option value="ROLE_TRAINING_ADMIN">ROLE_TRAINING_ADMIN</option>

                <option value="ROLE_SUBK_MANAGEMENT_USER">ROLE_SUBK_MANAGEMENT_USER</option>
                <option value="ROLE_SUBK_MANAGEMENT_ADMIN">ROLE_SUBK_MANAGEMENT_ADMIN</option>

                <option value="ROLE_INVOICE_EXCEPTIONAL_USER">ROLE_INVOICE_EXCEPTIONAL_USER</option>
                <option value="ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER">ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER</option>
                <option value="ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER">ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER</option>

                <option value="ROLE_ONBOARD_OFFBOARD_USER">ROLE_ONBOARD_OFFBOARD_USER</option>
                <option value="ROLE_ONBOARD_OFFBOARD_ADMIN">ROLE_ONBOARD_OFFBOARD_ADMIN</option>

                <option value="ROLE_OUT_OF_OFFICE_USER">ROLE_OUT_OF_OFFICE_USER</option>
                <option value="ROLE_OUT_OF_OFFICE_ADMIN">ROLE_OUT_OF_OFFICE_ADMIN</option>

                <option value="ROLE_THERMAL_EVENT_USER">ROLE_THERMAL_EVENT_USER</option>
                <option value="ROLE_THERMAL_EVENT_LV1_APPROVER">ROLE_THERMAL_EVENT_LV1_APPROVER</option>
                <option value="ROLE_THERMAL_EVENT_LV2_APPROVER">ROLE_THERMAL_EVENT_LV2_APPROVER</option>


            </select>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Create</button>
            </x-slot:footer>
            

        </x-common-ticket-form>

        <x-common-ticket-form title="Edit User Info" id="edit-user-info" action1="">
            @method('PATCH')
            <label>Fullname</label>
            <input type="text" class="ticket-form-body-input" id="edit-fullname" placeholder="Edit họ và tên" name="fullname" required>

            <label>Email</label>
            <input type="text" class="ticket-form-body-input" id="edit-email" placeholder="Edit email" name="email" required>

            <label>Số điện thoại</label>
            <input type="text" class="ticket-form-body-input" id="edit-phone-number" placeholder="Edit số điện thoại" name="phone_number" required>

            <label>Learner ID</label>
            <input type="text" class="ticket-form-body-input" id="edit-learner-id" placeholder="Edit learner ID" name="learner_id" required>

            <label>Site</label>
            <select name="site_id" class="ticket-form-body-input" id="site-change" required>
                <option value="1">Hồ Chí Minh</option>
                <option value="2">Hà Nội</option>
                <option value="3">Đà Nẵng</option>
                <option value="4">Cần Thơ</option>
                <option value="5">Call Center</option>
            </select>

            <label>Team</label>
            <select name="team" class="ticket-form-body-input" id="team-change" required>
                <option value="">--Select option--</option>
                <option value="1">ASRC</option>
                <option value="2">Call Center</option>
                <option value="3">Customer Engineer</option>
                <option value="4">Front Counter</option>
                <option value="5">Call Admin</option>
                <option value="6">Operation</option>
            </select>

            <label>Leader</label>
            <select name="leader_id" class="ticket-form-body-input" id="leader-change" required>
                <option value="">--Select option--</option>
                <option value="lecuong@hp.com">Lê Thế Cường</option>
                <option value="ha.nguyen3@hp.com">Nguyễn Tấn Hạ</option>
                <option value="phuong.phan-quoc@hp.com">Phan Quốc Phương</option>
                <option value="huong.dang@hp.com">Đặng Thanh Hương</option>
                <option value="leduy@hp.com">Lê Quang Khoa Duy</option>
                <option value="le.ngoc@hp.com">Lê Thị Thu Ngọc</option>
                <option value="trinh.huong@hp.com">Trịnh Minh Hướng</option>
                <option value="phamhieu@hp.com">Phạm Trung Hiếu</option>
            </select>

            <label class="ticket-form-body-input">Roles</label>
            <select multiple="multiple" name="roles[]" id="role-select-change" required >
                <option value="ROLE_SUPER_ADMIN">ROLE_SUPER_ADMIN</option>

                <option value="ROLE_SW_TICKET_ADMIN">ROLE_SW_TICKET_ADMIN</option>
                <option value="ROLE_SW_TICKET_USER">ROLE_SW_TICKET_USER</option>
                <option value="ROLE_APPROVE_ROLLBACK">ROLE_APPROVE_ROLLBACK</option>
                <option value="ROLE_APPROVE_EXPORT_DATA">ROLE_APPROVE_EXPORT_DATA</option>

                <option value="ROLE_TTEX_TICKET_USER">ROLE_TTEX_TICKET_USER</option>
                <option value="ROLE_TTEX_TICKET_ADMIN">ROLE_TTEX_TICKET_ADMIN</option>

                <option value="ROLE_LASER_ENGRAVING_USER">ROLE_LASER_ENGRAVING_USER</option>
                <option value="ROLE_LASER_ENGRAVING_ADMIN">ROLE_LASER_ENGRAVING_ADMIN</option>

                <option value="ROLE_LOAN_UNIT_USER">ROLE_LOAN_UNIT_USER</option>
                <option value="ROLE_LOAN_UNIT_ADMIN">ROLE_LOAN_UNIT_ADMIN</option>

                <option value="ROLE_SUBMIT_TRAINING_USER">ROLE_SUBMIT_TRAINING_USER</option>
                <option value="ROLE_TRAINING_ADMIN">ROLE_TRAINING_ADMIN</option>

                <option value="ROLE_SUBK_MANAGEMENT_USER">ROLE_SUBK_MANAGEMENT_USER</option>
                <option value="ROLE_SUBK_MANAGEMENT_ADMIN">ROLE_SUBK_MANAGEMENT_ADMIN</option>

                <option value="ROLE_INVOICE_EXCEPTIONAL_USER">ROLE_INVOICE_EXCEPTIONAL_USER</option>
                <option value="ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER">ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER</option>
                <option value="ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER">ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER</option>

                <option value="ROLE_ONBOARD_OFFBOARD_USER">ROLE_ONBOARD_OFFBOARD_USER</option>
                <option value="ROLE_ONBOARD_OFFBOARD_ADMIN">ROLE_ONBOARD_OFFBOARD_ADMIN</option>

                <option value="ROLE_OUT_OF_OFFICE_USER">ROLE_OUT_OF_OFFICE_USER</option>
                <option value="ROLE_OUT_OF_OFFICE_ADMIN">ROLE_OUT_OF_OFFICE_ADMIN</option>

                <option value="ROLE_THERMAL_EVENT_USER">ROLE_THERMAL_EVENT_USER</option>
                <option value="ROLE_THERMAL_EVENT_LV1_APPROVER">ROLE_THERMAL_EVENT_LV1_APPROVER</option>
                <option value="ROLE_THERMAL_EVENT_LV2_APPROVER">ROLE_THERMAL_EVENT_LV2_APPROVER</option>

            </select>


            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Edit</button> 
            </x-slot:footer>
        </x-common-ticket-form>

    </body>

</html>