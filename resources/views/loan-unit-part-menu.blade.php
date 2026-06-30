<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/loan-unit-part.js', 'resources/css/app.css',  'resources/css/icons/themify-icons.css', ])
    
    </head>

    <body>
        <div id="loan-unit-part-menu">
            <x-common-header title="Loan Unit & Part Menu">
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

            <div class="loan-unit-part-menu-content">

                <form action=""  >
                    <button type="button" class="js-input-required-btn" data-target="create-loan-unit-part-ticket"><i class="ti-plus"></i> Create Ticket</button>
                </form>

                <div class="common-table-container">
                    <h2>Pending Tickets</h2>
                    <table id="pending-loan-unit-part-tickets-table" class="common-table" width="100%" >
                        <thead>
                            <th width="5%"></th>
                            <th width="15%">Receipt</th>
                            <th width="15%">Status</th>
                            <th width="15%">Customer Unit Info</th>
                            <th width="15%">Part Request</th>
                            <th width="15%">Loan Unit Asset Tag</th>
                            <th width="15%">Loan Unit Serial Number</th>
                            

                        </thead>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="/loan-unit-part-ticket-details/{{ $ticket->id }}">
                                            <button><i class="ti-arrow-right" ></i></button>
                                        </a>
                                    </td>
                                    <td>{{ $ticket->ticket_receipt }}</td>
                                    <td>
                                        <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                            {{ $ticket->status_data['text'] }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->customer_unit_info }}</td>
                                    <td>{{ $ticket->part_request }}</td>
                                    <td>{{ $ticket->loan_unit_asset_tag }}</td>
                                    <td>{{ $ticket->loan_unit_serial_number }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <x-common-ticket-form title="Mượn máy & part form" action1="/create-loan-unit-part-ticket" id="create-loan-unit-part-ticket">
            <lable>Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu" name="ticket_receipt" required>

            <label>Customer's Unit Info</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin máy của khách hàng" name="customer_unit_info" required>
 
            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin part cần mượn (Mã part & Tên part)" name="part_request" required>

            
            <label>Attach File:</label>
            <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
            <ul id="fileList"></ul>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Submit</button> 
            </x-slot:footer>

        </x-common-ticket-form>
    </body>

</html>