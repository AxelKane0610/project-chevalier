<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/loan-unit-part.js', 'resources/css/app.css',  'resources/css/icons/themify-icons.css', ])
    
    </head>

    <body>
        <x-common-header title="Loan Unit & Part Menu">
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
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

        <div class="d-flex flex-grow-1 overflow-hidden vh-100">
            <div class="container-fluid my-5 flex-grow-1">
                <div class="row flex-grow-1 h-100">
                    <div class="col-2 d-flex justify-content-center align-items-center flex-column gap-3">
                        <form action=""  >
                            <button type="button" class="js-input-required-btn" data-target="create-loan-unit-part-ticket"><i class="ti-plus"></i> Create Ticket</button>
                        </form>

                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-loan-unit-part-tickets-btn" data-target = "pending-loan-unit-part-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$tickets->count()}}
                            </span>
                            <i class="ti-timer"></i> 
                            Show Pending Loan Unit Part Tickets
                        </button>
                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-loan-unit-part-tickets-btn" data-target = "all-loan-unit-part-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$all_tickets->count()}}
                            </span>
                            <i class="ti-list-ol"></i> 
                            Show All Tickets
                        </button>
                    </div>



                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-loan-unit-part-tickets-container">
                            <h2>Pending Tickets</h2>
                            <table id="pending-loan-unit-part-tickets-table" class="common-table" width="100%" >
                                <thead>
                                    <th width="5%"></th>
                                    <th width="15%">Receipt</th>
                                    <th width="15%">User Owner</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Customer Unit Info</th>
                                    

                                </thead>
                            
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>
                                                <button type="button" 
                                                    class="btn btn-primary" 
                                                    onclick="window.location.href='/loan-unit-part-ticket-details/{{ $ticket->id }}'">
                                                    <i class="ti-arrow-right"></i>
                                                </button>
                                                
                                            </td>
                                            <td>{{ $ticket->ticket_receipt }}</td>
                                            <td>{{ $ticket->user_owner->fullname }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                    {{ $ticket->status_data['text'] }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->customer_unit_info }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-loan-unit-part-tickets-container">
                            <h2>All Tickets</h2>
                            <div class="common-table-filter">
                                <div class="search-box">
                                    <i class="ti-search"></i>
                                    <input type="text" placeholder="Search Receipt" id="search-loan-unit-part-receipt-input">
                                </div>

                            </div>
                            <table id="all-loan-unit-part-tickets-table" class="common-table" width="100%" >
                                <thead>
                                    <th width="5%"></th>
                                    <th width="15%">Receipt</th>
                                    <th width="15%">User Owner</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Customer Unit Info</th>
                                    

                                </thead>
                            
                                <tbody>
                                    @foreach ($all_tickets as $ticket)
                                        <tr>
                                            <td>
                                                <button type="button" 
                                                    class="btn btn-primary" 
                                                    onclick="window.location.href='/loan-unit-part-ticket-details/{{ $ticket->id }}'">
                                                    <i class="ti-arrow-right"></i>
                                                </button>
                                                
                                            </td>
                                            <td>{{ $ticket->ticket_receipt }}</td>
                                            <td>{{ $ticket->user_owner->fullname }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                    {{ $ticket->status_data['text'] }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->customer_unit_info }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
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
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Submit</button> 
            </x-slot:footer>

        </x-common-ticket-form>
    </body>

</html>