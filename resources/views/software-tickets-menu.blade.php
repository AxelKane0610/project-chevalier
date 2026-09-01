<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/software-ticket-details.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>
        <div id="software-tickets-menu">

            <x-common-header title="EEG Software Support">
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
                            
                            <button type="button" class="js-input-required-btn" id="create-sw-ticket-btn" data-target="create-sw-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                            

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-tickets-btn" data-target = "pending-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets->count()}}
                                </span>
                                <i class="ti-timer"></i> 
                                Show Pending Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-waiting-approval-tickets-btn" data-target = "pending-approval-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets_waiting_approval->count()}}
                                </span>
                                <i class="ti-check"></i> 
                                Show Waiting Approval Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-tickets-btn" data-target = "all-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$all_tickets->count()}}
                                </span>
                                <i class="ti-list-ol"></i> 
                                Show All Tickets
                            </button>

                        </div>
                        <div class="col-10 h-100 overflow-auto">
                            <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-tickets-container">
                                <h2>Pending Tickets</h2>

                                <table id="pending-software-tickets-table" class="common-table" width="100%" >
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="14%">Receipt</th>
                                            <th width="20%">Type of request</th>
                                            <th width="39%">Issue Description</th>
                                            <th width="11%">Priority</th>
                                            <th width="11%">Status</th>
                                        </tr>
                                    </thead>
                                
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            
                                            <tr>
                                                <td>
                                                    <a href="/software-tickets-menu-details/{{ $ticket->id }}">
                                                        <button>
                                                            <i class="ti-arrow-right"></i>
                                                        </button>
                                                    </a>
                                                </td>
                                                <td>{{ $ticket->ticket_receipt }}</td>
                                                <td>
                                                    
                                                    <span class="badge rounded-pill bg-{{ $ticket->support_type_data['color'] ?? 'primary' }} px-3 py-2">
                                                        {{ $ticket->support_type_data['text'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->description }}</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $ticket->priority_data['color'] ?? 'primary' }} px-3 py-2">
                                                        {{ $ticket->priority_data['text'] }}
                                                    </span>
                                                </td>
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
                            @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TICKET_SW_ADMIN') || auth()->user()->hasRole('ROLE_APPROVE_ROLLBACK'))
                                @if($tickets_waiting_approval->count() > 0)
                                    <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="pending-approval-tickets-container">
                                        <h2>Waiting Approval</h2>
                                        <table class="common-table" id="pending-approval-software-tickets-table" width="100%" >
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="14%">Receipt</th>
                                                    <th width="20%">Type of request</th>
                                                    <th width="39%">Issue Description</th>
                                                    <th width="11%">Priority</th>
                                                    <th width="11%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @foreach ($tickets_waiting_approval as $ticket)
                                                    
                                                        <tr>
                                                            <td>
                                                                <a href="/software-tickets-menu-details/{{ $ticket->id }}">
                                                                    <button>
                                                                        <i class="ti-arrow-right"></i>
                                                                    </button>
                                                                </a>
                                                            </td>
                                                            <td>{{ $ticket->ticket_receipt }}</td>
                                                            <td>

                                                                <span class="badge rounded-pill bg-{{ $ticket->support_type_data['color'] ?? 'primary' }} px-3 py-2">
                                                                    {{ $ticket->support_type_data['text'] }}
                                                                </span>

                                                            </td>
                                                            <td>{{ $ticket->description }}</td>
                                                            <td>
                                                                <span class="badge rounded-pill bg-{{ $ticket->priority_data['color'] ?? 'primary' }} px-3 py-2">
                                                                    {{ $ticket->priority_data['text'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                                    {{ $ticket->status_data['text'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        
                                                    @endforeach
                                                    
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                @endif
                            @endif

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-tickets-container">
                                
                                <div class="common-table-filter">
                                    <h2>All Tickets</h2>
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input class="ajax-search" type="text" placeholder="Search Your Receipt or issue" id="search-software-ticket-input">
                                    </div>

                                    <h2>Support Type</h2>
                                    <select class="ajax-filter" name="support_type">
                                        <option value="">All</option>
                                        <option value="1">Thêm mã part/product</option>
                                        <option value="2">Rollback</option>
                                        <option value="3">Hủy số phiếu/Ẩn lịch sử bảo hành</option>
                                        <option value="4">Điều chỉnh thông tin</option>
                                        <option value="5">Unmark Re-Repair</option>
                                        <option value="6">Lỗi hệ thống</option>
                                        <option value="7">Cấp quyền export data</option>
                                        <option value="8">Đề xuất thay đổi/cải tiến</option>
                                        <option value="9">Vấn đề khác</option>


                                        
                                    </select>

                                    <h2>Ticket Status</h2>
                                    <select class="ajax-filter" name="status">
                                        <option value="">All</option>
                                        <option value="1">Open</option>
                                        <option value="2">In Progress</option>
                                        <option value="3">Waiting Approval</option>
                                        <option value="4">Completed</option>
                                        <option value="5">Rejected</option>
                                        <option value="6">Canceled</option>

                                        
                                    </select>

                                </div>

                                <div id="all-software-tickets-table-container">
                                    @include('tables.all-software-tickets-table')
                                </div>
                                

                            </div>

                        </div>
                    </div>
                </div>
            </div>
                

                

            </div>

        </div>


        <x-common-ticket-form title="EEG Software Support" action1="/create-software-ticket" id="create-sw-ticket-form"> <!-- action="/create-software-ticket" method="POST"> -->

            <label class="ticket-form-body-input">Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu tại đây" name="ticket_receipt" required>

            <label class="ticket-form-body-input">Support Type</label>
            <select name="support_type" class="ticket-form-body-input">
                <option value="1">Thêm mã part/product</option>
                <option value="2">Rollback</option>
                <option value="3">Hủy số phiếu/Ẩn lịch sử bảo hành</option>
                <option value="4">Điều chỉnh thông tin</option>
                <option value="5">Unmark Re-Repair</option>
                <option value="6">Lỗi hệ thống</option>
                <option value="7">Cấp quyền export data</option>
                <option value="8">Đề xuất thay đổi/cải tiến</option>
                <option value="9">Vấn đề khác</option>
            </select>

            <label class="ticket-form-body-input">Priority</label>
            <select name="priority" class="ticket-form-body-input">
                <option value="1">Normal</option>
                <option value="2">Critical</option>
                <option value="3">High</option>
                <option value="4">Low</option>
            </select>

            <label class="ticket-form-body-input">Description</label>
            <textarea name="description" class="ticket-form-body-input multiple-row" placeholder="Nhập mô tả vấn đề bạn cần hỗ trợ" required></textarea>
            
            <label class="ticket-form-body-input">Attach File:</label>
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>
            
            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" id="software-ticket-submit-btn">Submit</button> 
            </x-slot:footer>
            
            <!-- class="ticket-form-body-input" id="software-ticket-submit-btn"-->

        </x-common-ticket-form>
        

    
        
    </body>

    


</html>