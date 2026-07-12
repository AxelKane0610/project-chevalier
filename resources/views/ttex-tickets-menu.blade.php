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
                    <form action="" class="js-input-required-btn" >
                        <button type="button"><i class="ti-search"></i>Search</button>
                    </form>
                    
                </li>
                
            </x-common-header>

            <div class="thermal-event-tickets-menu-content">

                <form action=""  >
                    <button type="button" class="js-input-required-btn" data-target="create-ttex-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                </form>

                <div class="common-table-container">
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
                                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->shipment_type }}</td>
                                        <td>{{ $ticket->sender_info  }}</td>
                                        <td>{{ $ticket->receiver_info }}</td>
                                        <td>{{ $ticket->shipment_description }}</td>
                                        <td>{{ $ticket->note }}</td>
                                        
                                        <td>
                                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                {{ $ticket->status_data['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="common-table-container">
                    <h2>Pending Def Part Tickets</h2>
                    <table id="pending-ttex-tickets-table" class="common-table" width="100%" >
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
                            @foreach ($tickets_def_part_pending as $ticket)
                                
                                    <tr>
                                        <td>
                                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->shipment_type }}</td>
                                        <td>{{ $ticket->sender_info  }}</td>
                                        <td>{{ $ticket->receiver_info }}</td>
                                        <td>{{ $ticket->shipment_description }}</td>
                                        <td>{{ $ticket->note }}</td>
                                        <td>{{ $ticket->part_return_deadline }}</td>
                                        
                                        <td>
                                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                {{ $ticket->status_data['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="common-table-container">
                    <h2>All Tickets</h2>
                    <div class="common-table-filter">
                        <div class="search-box">
                            <i class="ti-search"></i>
                            <input type="text" placeholder="Search Receipt, TTEX Bill" id="search-spectre-crown-warehouse-input">
                        </div>

                        


                    </div>

                    <table id="pending-ttex-tickets-table" class="common-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="10%">TTEX Bill</th>
                            <th width="10%">User Owner</th>
                            <th width="20%">Người gửi</th>
                            <th width="20%">Người nhận</th>
                            <th width="20%">Mô tả hàng hóa</th>
                            <th width="10%">Status</th>

                        </tr>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                
                                    <tr>
                                        <td>
                                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->ttex_bill }}</td>
                                        <td>{{ $ticket->user_owner->fullname  }}</td>
                                        <td>{{ $ticket->sender_info }}</td>
                                        <td>{{ $ticket->receiver_info }}</td>
                                        <td>{{ $ticket->shipment_description }}</td>
                                        
                                        <td>
                                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                {{ $ticket->status_data['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    
                            @endforeach
                        </tbody>

                    </table>
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
                <select name="part_status" class="ticket-form-body-input">
                    <option value="1">Good part</option>
                    <option value="2">Def part</option>
                    <option value="3">Good part - Unused</option>

                </select>

                <label>Thông tin người gửi</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin người gửi" name="sender_info" required>
                @livewire('ttex-address-dropdown')

                <label>Thông tin người nhận</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin người nhận" name="receiver_info" required>

                <label>Mô tả hàng hóa</label>
                <input type="text" class="ticket-form-body-input multiple-row" placeholder="Nhập mô tả hàng hóa. VD: SCHCM25-00001 (FAN)" name="shipment_description" required>

                <label>Note</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập ghi chú" name="note">

                <label style="color: red">Bạn có muốn những bill này được thu hồi (Chỉ dành cho onsite tỉnh) ?</label>
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