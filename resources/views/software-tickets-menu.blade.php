<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @vite(['resources/js/test.js'])
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    </head>

    <body class="background-enable">
        <div id="software-tickets-menu">

            <x-common-header title="EEG Software Support">
                <li><i class="ti-home"></i><a href="/main-menu">Home</a></li>
                <li class="js-search-btn"><i class="ti-search"></i><a href="#">Search</a></li>
                <li><i class="ti-user"></i><a href="#">Profile</a></li>
            </x-common-header>


            <div class="software-tickets-menu-content">
                <button id="software-tickets-create-btn" class="js-create-software-tickets"><i class = "ti-plus"></i>Create Ticket</button>

                <div id="pending-software-tickets-table-container">
                    <h2>Pending Tickets</h2>

                    <table id="pending-software-tickets-table" width="100%" >
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
                                <tr>
                                    <td>
                                        <a href="/software-tickets-menu-details/{{ $ticket->id }}">
                                            <i class="ti-arrow-right" ></i>
                                        </a>
                                    </td>
                                    <td>{{ $ticket->ticket_reciept }}</td>
                                    <td>
                                        @if ($ticket->support_type == 1)
                                            Thêm mã part
                                        @elseif ($ticket->support_type == 2)
                                            Rollback
                                        @elseif ($ticket->support_type == 3)
                                            Hủy số phiếu
                                        @elseif ($ticket->support_type == 4)
                                            Điều chỉnh thông tin
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $ticket->description }}</td>
                                    <td>
                                        @if ($ticket->priority == 1)
                                            Normal
                                        @elseif ($ticket->priority == 2)
                                            Critical
                                        @elseif ($ticket->priority == 3)
                                            High
                                        @elseif ($ticket->priority == 4)
                                            Low
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>Chờ leader approve</td>
                                </tr>
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
                            <td>Chờ leader approve</td>
                        </tr>
                        
                    </table>

                </div>

            </div>

        </div>


        <x-common-ticket-form title="EEG Software Support" action1="/create-software-ticket" id="create-sw-ticket"> <!-- action="/create-software-ticket" method="POST"> -->

            <label>Reciept</label>
            <input type="text" class="Ticket-Form-Body-Input" placeholder="Nhập số phiếu tại đây" name="ticket_reciept" required>

            <label>Support Type</label>
            <select name="support_type" class="Ticket-Form-Body-Input">
                <option value="1">Thêm mã part</option>
                <option value="2">Rollback</option>
                <option value="3">Hủy số phiếu</option>
                <option value="4">Điều chỉnh thông tin</option>
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
            <input type="file" name="attachment" multiple>

            <button type="submit" class="Ticket-Form-Body-Input" id="software-ticket-submit-btn">Submit</button>

        </x-common-ticket-form>
        
        <script>
            
        </script>
        
    </body>

    


</html>