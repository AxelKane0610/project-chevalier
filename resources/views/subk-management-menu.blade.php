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
                    @csrf
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>
            <li>
                <form action="#">
                    @csrf
                    <button type="submit"><i class="ti-search"></i>Search</button>
                </form>
            </li>
            </x-common-header>

            <div class="subk-management-menu-content">

                <div class="common-table-container">
                    <h2>Users </h2>

                    <table id = "subk-management-users-table" class="common-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="14%">Username</th>
                            <th width="14%">Fullname</th>
                            <th width="39%">Roles</th>
                            <th width="14%">Email</th>
                            <th width="14%">Site</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <a href="/subk-management/{{ $user->id }}">
                                            <i class="ti-arrow-right" ></i>
                                        </a>
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
        

    </body>

</html>