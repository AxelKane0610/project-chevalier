<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    
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
                                        
                                        <button><i class="ti-pencil"></i></button>
                                        <button><i class="ti-na"></i></button>
                                        
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->fullname }}</td>
                                    <td>{{ implode(', ', $user->roles) }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->site }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                
                
                </div>

                

            </div>

        </div>
        
        <x-common-ticket-form title="Tạo user mới" id="create-user-form" action1="create-new-user">
            <label class="ticket-form-body-input">Username</label>
            <input name="Username" class="ticket-form-body-input"></input>
            <label class="ticket-form-body-input">Password</label>
            <input name="Password" class="ticket-form-body-input" type="password"></input>
            <label class="ticket-form-body-input">Fullname</label>
            <input name="Fullname" class="ticket-form-body-input"></input>
            
            <label class="ticket-form-body-input">Leader</label>
            <select name="Leader" class="ticket-form-body-input">
                <option value="1">Lê Thế Cường</option>
                <option value="2">Nguyễn Tấn Hạ</option>
                <option value="3">Phan Quốc Phương</option>
                <option value="4">Đặng Thanh Hương</option>
                <option value="5">Lê Quang Khoa Duy</option>
            </select>

            <label class="ticket-form-body-input">Site</label>
            <select name="Site" class="ticket-form-body-input">
                <option value="1">Hồ Chí Minh</option>
                <option value="2">Hà Nội</option>
                <option value="3">Đà Nẵng</option>
                <option value="4">Cần Thơ</option>
                <option value="5">Call Center</option>
            </select>

            <label class="ticket-form-body-input">Email</label>
            <input name="Email" class="ticket-form-body-input"></input>

            <label class="ticket-form-body-input">Learner ID</label>
            <input name="Learner_Id" class="ticket-form-body-input"></input>

            <label class="ticket-form-body-input">Roles</label>
            <select multiple name="roles[]" class="ticket-form-body-input">
                <option value="ROLE_SUPER_ADMIN">ROLE_SUPER_ADMIN</option>
                <option value="ROLE_SW_TICKET_ADMIN">ROLE_SW_TICKET_ADMIN</option>
                <option value="ROLE_SW_TICKET_USER">ROLE_SW_TICKET_USER</option>
                <option value="ROLE_APPROVE_ROLLBACK">ROLE_APPROVE_ROLLBACK</option>
                <option value="ROLE_APPROVE_EXPORT_DATA">ROLE_APPROVE_EXPORT_DATA</option>
            </select>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Create</button>
            </x-slot:footer>
            

        </x-common-ticket-form>

    </body>

</html>