<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/loan-unit-part.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Loan Units & Parts Ticket Details">
            <li>
                <form action="/loan-unit-part-menu">
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
                <th width="50px">Part Request</th>
                <th width="50px">Status</th>
                <th width="50px">Loan Unit Asset Tag</th> 
                <th width="50px">Loan Unit Serial Number</th> 
                <th width="50px">CT Loaned</th>
                <th width="50px">New CT Return</th>
                <th width="50px">Original</th>
                <th width="50px">Start Date</th>
                <th width="50px">End Date</th>

                


                @foreach($ticket->parts_details as $parts)
                <tr>
                    <td>
                        <!-- <form class="js-input-required-btn" data-target="edit-thermal-event-part-details" action="" method="PATCH">
                            <button type="button" 
                            class="btn-edit-part"
                            data-id="{{ $parts->id }}"
                            data-mo="{{ $parts->part_mo_number }}"
                            data-number="{{ $parts->part_number }}"
                            data-description="{{ $parts->part_description }}"
                            data-ct="{{ $parts->part_ct_number }}"><i class="ti-pencil"></i></button>
                        </form>

                        <form class="js-input-required-btn" data-target="delete-thermal-event-part-details" id="delete-thermal-event-part-details" action="{{ route('delete-thermal-event-part-details', $parts->id) }}" method="PATCH">
                            <button type="submit"><i class="ti-na"></i></button>
                        </form> -->

                        
                    </td>
                    <td>{{$parts->part_request}}</td>
                    <td>{{$parts->status}}</td>
                    <td>{{$parts->loan_unit_asset_tag}}</td>
                    <td>{{$parts->loan_unit_serial_number}}</td>
                    <td>{{$parts->ct_loaned}}</td>
                    <td>{{$parts->new_ct_return}}</td>
                    <td>{{$parts->original}}</td>
                    <td>{{$parts->start_date}}</td>
                    <td>{{$parts->end_date}}</td>


                </tr>
                @endforeach
            </table>
        </div>

        <div class="loan-unit-part-ticket-content">
            <x-common-ticket-detail-form>
                <li>
                    <lable>Receipt</lable>
                    <h2>{{ $ticket->ticket_receipt }}</h2>
                    
                </li>

                <li>
                    <lable>User Owner</lable>
                    <h2>{{ $ticket->user_owner->fullname }}</h2>
                    
                </li>

                <li>
                    <lable>Status</lable>
                    <h2>{{ $ticket->status }}</h2>
                    
                </li>

                <li>
                    <lable>Customer Unit Info</lable>
                    <h2>{{ $ticket->customer_unit_info }}</h2>
                    
                </li>

                <li>
                    <lable>Ngày request</lable>
                    <h2>{{ $ticket->created_at }}</h2>
                    
                </li>
            </x-common-ticket-detail-form>
        </div>
    </body>

</html>