<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <div>

            <x-common-header title="Thermal Event Exceptional">
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

            <div class="d-flex flex-grow-1 overflow-hidden vh-100">
                <div class="container-fluid my-5 flex-grow-1">
                    <div class="row flex-grow-1 h-100">
                        <div class="col-2 d-flex justify-content-center align-items-center flex-column gap-3">
                            <button type="button" class="js-input-required-btn" data-target="create-thermal-event-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                            

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-thermal-event-tickets-btn" data-target = "pending-thermal-event-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets->count()}}
                                </span>
                                <i class="ti-timer"></i> 
                                Show Pending Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-approval-thermal-event-tickets-btn" data-target = "pending-approval-thermal-event-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets_waiting_approval->count()}}
                                </span>
                                <i class="ti-check"></i> 
                                Show Waiting Approval Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-thermal-event-tickets-btn" data-target = "all-thermal-event-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$all_tickets->count()}}
                                </span>
                                <i class="ti-list-ol"></i> 
                                Show All Tickets
                            </button>

                        </div>

                        <div class="col-10 h-100 overflow-auto">
                            <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-thermal-event-tickets-container">
                                <h2>Pending Tickets</h2>
                                <table id="pending-thermal-event-tickets-table" class="common-table" width="100%" >
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="14%">Receipt</th>
                                        <th width="15%">User Owner</th>
                                        <th width="39%">Issue Description</th>
                                        <th width="11%">Status</th>
                                    </tr>
                                
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            
                                                <tr>
                                                    <td>
                                                        <a href="/thermal-event-tickets-menu-details/{{ $ticket->id }}">
                                                            <button><i class="ti-arrow-right" ></i></button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $ticket->ticket_receipt }}</td>
                                                    <td>{{ $ticket->user_owner->fullname ?? 'N/A' }}</td>
                                                    <td>{{ $ticket->description }}</td>
                                                    
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->status_data['text'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="pending-approval-thermal-event-tickets-container">
                                <h2>Pending Approval Tickets</h2>
                                <table id="pending-thermal-event-tickets-table" class="common-table" width="100%" >
                                    <thead>
                                        <th width="5%"></th>
                                        <th width="14%">Receipt</th>
                                        <th width="15%">User Owner</th>
                                        <th width="39%">Issue Description</th>
                                        <th width="11%">Status</th>
                                    </thead>
                                
                                    <tbody>
                                        @foreach ($tickets_waiting_approval as $ticket)
                                            
                                                <tr>
                                                    <td>
                                                        <a href="/thermal-event-tickets-menu-details/{{ $ticket->id }}">
                                                            <button><i class="ti-arrow-right" ></i></button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $ticket->ticket_receipt }}</td>
                                                    <td>{{ $ticket->user_owner->fullname ?? 'N/A' }}</td>
                                                    <td>{{ $ticket->description }}</td>
                                                    
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->status_data['text'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                        @endforeach
                                    </tbody>    

                                </table>
                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-thermal-event-tickets-container">
                                <h2>All Tickets</h2>
                                <div class="common-table-filter">
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input type="text" placeholder="Search Receipt" id="search-thermal-event-receipt-input">
                                    </div>

                                </div>
                                <table id="all-thermal-event-tickets-table" class="common-table" width="100%" >
                                    <thead>
                                        <th width="5%"></th>
                                        <th width="14%">Receipt</th>
                                        <th width="15%">User Owner</th>
                                        <th width="39%">Issue Description</th>
                                        <th width="11%">Status</th>
                                    </thead>
                                
                                    <tbody>
                                        @foreach ($all_tickets as $ticket)
                                            
                                                <tr>
                                                    <td>
                                                        <a href="/thermal-event-tickets-menu-details/{{ $ticket->id }}">
                                                            <button><i class="ti-arrow-right" ></i></button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $ticket->ticket_receipt }}</td>
                                                    <td>{{ $ticket->user_owner->fullname ?? 'N/A' }}</td>
                                                    <td>{{ $ticket->description }}</td>
                                                    
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->status_data['text'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                        </div>

                        
                    </div>
                </div>
            </div>


            <x-common-ticket-form title="Thermal Event Exceptional Form" action1="/create-thermal-event-ticket" id="create-thermal-event-ticket-form">
                <lable>Receipt</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu" name="ticket_receipt" required>

                <label>Serial Number</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="serial_number" required>

                <label>Product Number</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số product máy" name="product_number" required>

                <label>Product Model</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="product_model" required>

                <label>Description</label>
                <input type="text" class="ticket-form-body-input" placeholder="Mô tả issue của máy" name="description" required>

                <label>CDAX ID</label>
                <input type="number" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="cdax_id" required>

                <label>Customer Type</label>
                <select name="customer_type" class="ticket-form-body-input">
                    <option value="1">Khách hàng lẻ</option>
                    <option value="2">Khách hàng công ty/doanh nghiệp</option>
                    <option value="3">T1/Đại lý bán lẻ</option>
                </select>

                <label>Company/Customer Name</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập tên khách hàng/công ty" name="company_customer_name" required>

                <label>Quan sát thực tế</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập quan sát thực tế (không nước, không côn trùng, ...)" name="user_observations" required>

                <label style="color: red; font-weight: bold">Nhiều part bị ảnh hưởng ?</label>
                <select name="multipart_affected_check" class="ticket-form-body-input" id="multipart_affected_check">
                    <option value="1">Chỉ có 1 part bị ảnh hưởng</option>
                    <option value="2">Có nhiều hơn 1 part bị ảnh hưởng</option>
                </select>

                <div id="thermal_event_parts_details">
                    <label>Part MO Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập số MO của part" name="part_mo_number" required>

                    <label>Part Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập mã part" name="part_number" required>

                    <label>Part Description</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập tên part" name="part_description" required>

                    <label>Part CT Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập CT của part, nếu không có để N/A" name="part_ct_number" required>
                </div>
                
                <label>Attach File:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple required>
                    <ul class="file-list"></ul>
                </div>

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" id="thermal-event-ticket-submit-btn">Submit</button> 
                </x-slot:footer>

            </x-common-ticket-form>

        </div>
    </body>

</html>