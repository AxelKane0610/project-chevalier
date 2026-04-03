<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    </head>

    <body class="background-enable">

        
            <x-common-header title="EEG Software Support">
                <li><i class="ti-home"></i><a href="/software-tickets-menu">Home</a></li>
                <li><i class="ti-search"></i><a href="#">Quick Search</a></li>
                <li><i class="ti-layout-grid2"></i><a href="/main-menu">Quick Navigation</a></li>
                
                    @can('check_role', 'ROLE_SW_TICKET_ADMIN' || 'ROLE_SUPER_ADMIN')
                    
                        @switch($ticket->status)
                            @case(1)
                            @case(2)
                            @case(3)
                                <li><i class="ti-alarm-clock"></i><a href="#">In Progress</a></li>
                                <li><i class="ti-angle-double-right"></i><a href="#">Send approve</a></li>
                                <li><i class="ti-check"></i><a href="#">Complete</a></li>
                                <li><i class="ti-thumb-down"></i><a href="#">Reject</a></li>
                                
                            @break
                            
                            
                        @endswitch
                    
                    @endcan

                    @if ($ticket->status == 4 || $ticket->status == 5)
                        <li><i class="ti-back-left"></i><a href="#">Request Re-Open</a></li>
                    @endif
                
            </x-common-header>

                
        <div class="software-tickets-content">

            <x-common-ticket-detail-form>
                
                    <li>
                        <lable>Reciept</lable>
                        
                        <h2>{{ $ticket->ticket_reciept }}</h2>
                        
                    </li>
                    <li>
                        <lable>Người request</lable>
                        
                        <h2>{{ $ticket->user_owner->fullname }}</h2>
                        
                    </li>
                    <li>
                        <lable>Ngày request</lable>
                        
                        <h2>{{ $ticket->created_at }}</h2>
                        
                    </li>
                    <li>
                        <lable>Priority</lable>
                        
                            <h2>
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
                            </h2>
                            
                    </li>
                    <li>
                        <lable>Support type</lable>
                        
                            <h2>
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
                            </h2>
                            
                    </li>
                    <li>
                        <lable>Status</lable>
                        
                            <h2>
                                @if ($ticket->status == 1)
                                    Open
                                @elseif ($ticket->status == 2)
                                    In Progress
                                @elseif ($ticket->status == 3)
                                    Waiting Approval
                                @elseif ($ticket->status == 4)
                                    Complete
                                @elseif ($ticket->status == 5)
                                    Rejected
                                @else
                                    N/A
                                @endif
                            </h2>
                            
                    </li>
                    <li style="height: 200px;">
                        <lable>Issue description</lable>
                        
                        <p>{{ $ticket->description }}</p>
                        
                    </li>
                    <!-- <li>
                        <lable>Issue Owner</lable>
                        
                        <h2>{{ $ticket->issue_owner }}</h2>
                        
                    </li>
                    <li>
                        <lable>Người thực hiện ticket</lable>
                        
                        <h2>{{ $ticket->assignee }}</h2>
                        
                    </li> -->
                

            </x-common-ticket-detail-form>
                
            <div class="software-tickets-comments">
                <form class="software-tickets-comment-form">
                    <label>Write a comment</label>
                    <textarea name="" id="" style="height: 100px; font-family: inherit ;" placeholder="Nhập comment tại đây"></textarea>
                    <button type="submit">Comment</button>
                </form>
            </div>

            <div class="software-tickets-tracking-info">
                <h2>Tracking Info</h2>
                <h3>Nguyễn Thanh Hải create ticket at 26/10/2025 10:35 AM</h3>
                <h3>Trịnh Minh Vương send approval at 26/10/2025 11:00 AM</h3>
            </div>

        </div>

    </body>

</html>