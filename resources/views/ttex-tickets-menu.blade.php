<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/ttex.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <div>

            <x-common-header title="TTEX Tickets Menu">
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
                                <button type="button" class="js-input-required-btn" data-target="create-ttex-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                            </form>

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-good-part-tickets-btn" data-target = "pending-good-part-ttex-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets_good_part_pending->count()}}
                                </span>
                                <i class="ti-timer"></i> Show Pending Good Part Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-waiting-def-part-tickets-btn" data-target = "pending-def-part-ttex-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets_def_part_pending->flatten()->count()}}
                                </span>
                                <i class="ti-timer"></i> 
                                Show Pending Def Part Tickets
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-ttex-tickets-booked-today-btn" data-target = "ttex-tickets-booked-today-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets_booked_today->count()}}
                                </span>
                                <i class="ti-check"></i> 
                                Show Tickets Booked Today
                            </button>
                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-ttex-tickets-btn" data-target = "all-ttex-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$tickets->total()}}
                                </span><i class="ti-list-ol"></i> 
                                Show All Tickets
                            </button>

                        </div>

                        <div class="col-10 h-100 overflow-auto">
                            <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-good-part-ttex-tickets-container">
                                <h2>Pending Good Part Tickets</h2>
                                <table id="pending-ttex-tickets-table" class="common-table" width="100%" >
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="10%">Shipment Type</th>
                                        <th width="20%">Người gửi</th>
                                        <th width="20%">Người nhận</th>
                                        <th width="20%">Mô tả hàng hóa</th>
                                        <th width="15%">Note</th>
                                        <th width="10%">Status</th>

                                    </tr>
                                
                                    <tbody>
                                        @foreach ($tickets_good_part_pending as $ticket)
                                            
                                                <tr>
                                                    <td>

                                                        <button type="button" 
                                                            class="btn btn-primary" 
                                                            onclick="window.location.href='/ttex-tickets-menu-details/{{ $ticket->id }}'">
                                                            <i class="ti-arrow-right"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->shipment_type_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->shipment_type_data['text'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->sender_info  }}</td>
                                                    <td>{{ $ticket->receiver_info }}</td>
                                                    <td>{{ $ticket->shipment_description }}</td>
                                                    <td>{{ $ticket->note }}</td>
                                                    
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

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="ttex-tickets-booked-today-container">
                                <h2>Tickets đã book trong hôm nay</h2>
                                <table id="ttex-tickets-booked-today-table" class="common-table" width="100%" >
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="10%">Shipment Type</th>
                                        <th width="10%">Part Status</th>
                                        <th width="20%">Người gửi</th>
                                        <th width="20%">Người nhận</th>
                                        <th width="20%">Mô tả hàng hóa</th>
                                        <th width="15%">Note</th>

                                    </tr>
                                
                                    <tbody>
                                        @foreach ($tickets_booked_today as $ticket)
                                            
                                                <tr>
                                                    <td>

                                                        <button type="button" 
                                                            class="btn btn-primary" 
                                                            onclick="window.location.href='/ttex-tickets-menu-details/{{ $ticket->id }}'">
                                                            <i class="ti-arrow-right"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->shipment_type_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->shipment_type_data['text'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->part_status_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->part_status_data['text'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->sender_info  }}</td>
                                                    <td>{{ $ticket->receiver_info }}</td>
                                                    <td>{{ $ticket->shipment_description }}</td>
                                                    <td>{{ $ticket->note }}</td>
                                                    
                                                    
                                                </tr>
                                                
                                        @endforeach
                                    </tbody>

                                </table>

                            </div>


                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="pending-def-part-ttex-tickets-container">
                                <h2>Pending Def Part Tickets</h2>
                                    @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')))

                                        <form class="js-input-required-btn" data-target="booking-def-part" id="booking-def-part" action="{{ route('booking-def-part') }}" method="POST">
                                            @csrf
                                            <button type="submit"><i class="ti-check"></i></button>
                                        </form>
                                    @endif
                                    <table id="pending-ttex-tickets-table" class="common-table mh-100" width="100%" >
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="10%">Shipment Type</th>
                                            <th width="20%">Người gửi</th>
                                            <th width="20%">Người nhận</th>
                                            <th width="20%">Mô tả hàng hóa</th>
                                            <th width="15%">Note</th>
                                            <th width="10%">Hạn trả def cho kho</th>
                                            <th width="10%">Status</th>

                                        </tr>
                                    
                                        <tbody>
                                            

                                            @foreach($tickets_def_part_pending as $date => $group)

                                                <tr class="table-secondary">
                                                    <td colspan="10">
                                                        ▼ Hạn trả def cho kho:
                                                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                                        ({{ count($group) }})
                                                    </td>
                                                </tr>

                                                @foreach($group as $ticket)

                                                <tr>
                                                    <td>
                                                        <button type="button" 
                                                            class="btn btn-primary" 
                                                            onclick="window.location.href='/ttex-tickets-menu-details/{{ $ticket->id }}'">
                                                            <i class="ti-arrow-right"></i>
                                                        </button>
                                                        @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')))

                                                            <input type="checkbox" name="booking_def[]" value="{{ $ticket->id }}" form="booking-def-part">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->shipment_type_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->shipment_type_data['text'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->sender_info  }}</td>
                                                    <td>{{ $ticket->receiver_info }}</td>
                                                    <td>{{ $ticket->shipment_description }}</td>
                                                    <td>{{ $ticket->note }}</td>
                                                    <td>{{ $ticket->part_return_deadline }}</td>
                                                    
                                                    <td>
                                                        <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                            {{ $ticket->status_data['text'] }}
                                                        </span>
                                                    </td>
                                                </tr>

                                                @endforeach

                                            @endforeach
                                        </tbody>

                                    </table>
                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-ttex-tickets-container">
                                
                                    <div class="common-table-filter">
                                        <h2>All Tickets</h2>
                                        <div class="search-box">
                                            <i class="ti-search"></i>
                                            <input type="text" placeholder="Search Receipt, TTEX Bill" id="search-ttex-bill-input-all-tickets-table">
                                        </div>

                                        <h2>Status:</h2>
                                        <select id="all-ttex-tickets-status-filter">
                                            <option value="">All</option>
                                            <option value="1">Open - Chưa điều tin</option>
                                            <option value="2">Completed - Đã điều tin</option>
                                            <option value="3">Rejected</option>
                                            
                                        </select>

                                        <h2>Part Status:</h2>
                                        <select id="all-ttex-tickets-part-status-filter">
                                            <option value="">All</option>
                                            <option value="1">Good part</option>
                                            <option value="2">Def part</option>
                                            <option value="3">Good part - Unused</option>
                                            
                                        </select>

                                        


                                    </div>
                                    <div id="all-ttex-tickets-table-container">
                                        
                                        @include('tables.ttex-all-tickets-table')
                                    </div>
                            </div>




                        </div>
                    </div>
                    
                </div>
                
            </div>

                

                

                
                
                
            

            <x-common-ticket-form title="TTEX Ticket Form" action1="/create-ttex-ticket" id="create-ttex-ticket-form">
                <lable>Category</label>
                <select name="category" class="ticket-form-body-input">
                    <option value="1">ASRC</option>
                    <option value="2">HPS</option>
                    <option value="3">Onsite Geox</option>
                    <option value="4">Part NBD</option>
                    <option value="5">Văn phòng phẩm/Tài liệu</option>
                    <option value="6">Others</option>

                </select>

                <lable>Shipment Type</label>
                <select name="shipment_type" class="ticket-form-body-input">
                    <option value="1">Tài liệu</option>
                    <option value="2">Thiết bị điện/điện tử</option>
                    <option value="3">Văn phòng phẩm</option>

                </select>

                <lable>Part Status</label>
                <select name="part_status" class="ticket-form-body-input" id="def_unused_return_check">
                    <option value="1">Good part</option>
                    <option value="2">Def part</option>
                    <option value="3">Good part - Unused</option>

                </select>

                <div id="def_part_return_deadline">
                    <lable>Hạn trả def cho kho</label>
                    <input type="datetime-local" class="ticket-form-body-input" name="part_return_deadline" required>
                </div>

                <label>Thông tin người gửi</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin người gửi" name="sender_info" required>
                

                <label>Thông tin người nhận</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin người nhận" name="receiver_info" required>

                <label>Mô tả hàng hóa</label>
                <textarea class="ticket-form-body-input multiple-row" placeholder="Nhập mô tả hàng hóa. VD: SCHCM25-00001 (FAN)" name="shipment_description"  required></textarea>

                <label>Note</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập ghi chú" name="note">

                <label style="color: red; font-weight: 700;">Bạn có muốn những bill này được thu hồi (Chỉ dành cho onsite tỉnh) ?</label>
                <select name="part_returned_check" class="ticket-form-body-input" id="part_returned_check" required>
                    <option value="1">Có (Hệ thống sẽ tự tạo ticket điều tin def về)</option>
                    <option value="2">Không</option>
                </select>

                
                <label>Attach File:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Submit</button> 
                </x-slot:footer>

            </x-common-ticket-form>

        </div>

    </body>



    

</html>