<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Spectre - Crown Item Details">
            <li>
                <form action="/spectre-crown-warehouse-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>

            <li>
                <form>
                    <button type="submit"><i class="ti-search test-js"></i>Search</button>
                </form>

            </li>
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-layout-grid2"></i>Quick Navigation</button>
                </form>
            </li>
        </x-common-header>

        <div class="common-table-container">
            <table class="common-table" width="100%">
                <th width="5%"></th>
                <th width="25%">User Owner</th>
                <th width="25%">Receipt</th>   
                <th width="25%">Loan Unit Asset Tag</th>
                <th width="25%">Loan Unit Serial Number</th> 

                @foreach($item_details->loan_unit_part_tickets as $ticket)
                <tr>
                    <td>
                        
                        <button type="submit"><i class="ti-na"></i></button>

                    </td>
                    
                    <td>{{$ticket->user_owner->fullname}}</td>
                    <td>{{$ticket->ticket_receipt}}</td>
                    <td>{{$ticket->loan_unit_asset_tag}}</td>
                    <td>{{$ticket->loan_unit_serial_number}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </body>

</html>