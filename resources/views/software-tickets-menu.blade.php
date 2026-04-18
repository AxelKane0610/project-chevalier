<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/new-ticket.js'])
        <link rel="stylesheet" href="{{ asset('resources/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    </head>

    <body>
        <div id="software-tickets-menu">

            <x-common-header title="EEG Software Support">
                <li>
                    <form action="/main-menu">
                        @csrf
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="" class="js-input-required-btn" >
                        @csrf
                        <button type="button"><i class="ti-search"></i>Search</button>
                    </form>
                    
                </li>
                
            </x-common-header>


            <div class="software-tickets-menu-content">

                <form action="" class="js-input-required-btn" id="create-sw-ticket-btn" data-target="create-sw-ticket-form">
                    <button type="button"><i class="ti-plus"></i> Create Ticket</button>
                </form>
                

                <div class="common-table-container">
                    <h2>Pending Tickets</h2>

                    <table id="pending-software-tickets-table" class="common-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="14%">Reciept</th>
                            <th width="14%">Type of request</th>
                            <th width="39%">Issue Description</th>
                            <th width="14%">Status</th>
                            <th width="14%">Latest comment</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                @switch($ticket->status)
                                    @case(1)
                                    @case(2)
                                    @case(3)
                                    <tr>
                                        <td>
                                            <a href="/software-tickets-menu-details/{{ $ticket->id }}">
                                                <i class="ti-arrow-right" ></i>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->ticket_reciept }}</td>
                                        <td>
                                            @switch($ticket->support_type)
                                                @case(1) Thêm mã part/product @break
                                                @case(2) Rollback @break
                                                @case(3) Hủy số phiếu/Ẩn lịch sử bảo hành @break
                                                @case(4) Điều chỉnh thông tin @break
                                                @case(5) Unmark Re-Repair @break
                                                @case(6) Lỗi hệ thống @break
                                                @case(7) Cấp quyền export data @break
                                                @case(8) Đề xuất thay đổi/cải tiến @break
                                                @case(9) Vấn đề khác @break
                                            @endswitch
                                        </td>
                                        <td>{{ $ticket->description }}</td>
                                        <td>
                                            @switch($ticket->priority)
                                                @case(1) Normal @break
                                                @case(2) Critical @break
                                                @case(3) High @break
                                                @case(4) Low @break
                                            @endswitch
                                        </td>
                                        <td></td>
                                    </tr>
                                    @break
                                @endswitch
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <div id="rejected-software-tickets-table-container">
                    <h2>Recent Rejected Tickets</h2>

                    <table class="rejected-software-tickets-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="14%">Reciept</th>
                            <th width="14%">Type of request</th>
                            <th width="39%">Issue Description</th>
                            <th width="14%">Status</th>
                            <th width="14%">Latest comment</th>
                        </tr>
                        <tr>
                            <td><i class="ti-arrow-right" ></i></td>
                            <td>SCHCM26-001234</td>
                            <td>Rollback</td>
                            <td>Nhờ team SW rollback do cần báo giá bổ sung thêm part.</td>
                            <td>Rejected</td>
                            <td></td>
                        </tr>
                        
                    </table>

                </div>

            </div>

        </div>


        <x-common-ticket-form title="EEG Software Support" action1="/create-software-ticket" id="create-sw-ticket-form"> <!-- action="/create-software-ticket" method="POST"> -->

            <label>Reciept</label>
            <input type="text" class="Ticket-Form-Body-Input" placeholder="Nhập số phiếu tại đây" name="ticket_reciept" required>

            <label>Support Type</label>
            <select name="support_type" class="Ticket-Form-Body-Input">
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

            <label>Priority</label>
            <select name="priority" class="Ticket-Form-Body-Input">
                <option value="1">Normal</option>
                <option value="2">Critical</option>
                <option value="3">High</option>
                <option value="4">Low</option>
            </select>

            <label>Description</label>
            <textarea name="description" class="Ticket-Form-Body-Input" placeholder="Nhập mô tả vấn đề bạn cần hỗ trợ" required></textarea>
            
            <label>Attach File:</label>
            <input type="file" name="attachments[]" multiple>

            <button type="submit" class="Ticket-Form-Body-Input" id="software-ticket-submit-btn">Submit</button> 
            <!-- class="Ticket-Form-Body-Input" id="software-ticket-submit-btn"-->

        </x-common-ticket-form>
        

    
        
    </body>

    


</html>