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
                <li>
                    <a href="/main-menu">
                        <i class="ti-home"></i>
                        Home
                    </a>
                </li>

                <li>
                    <a href="">
                        <i class="ti-search"></i>
                        Quick Search
                    </a>
                </li>
                <li>
                    <a href="/main-menu">
                        <i class="ti-layout-grid2"></i>
                        Quick Navigation
                    </a>
                </li>

                    @can('check_role', 'ROLE_SW_TICKET_ADMIN' || 'ROLE_SUPER_ADMIN') <!-- Chỉ hiển thị nút action nếu người dùng có vai trò admin hoặc super admin -->
                    
                        @switch($ticket->status)
                            @case(1)
                            @case(2)
                            @case(3)
                                <li>
                                    <a href="">
                                        <i class="ti-alarm-clock"></i>
                                        In Progress
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <i class="ti-angle-double-right"></i>
                                        Send approve
                                    </a>
                                </li>
                                <li>
                                    <a href="" id="complete-sw-ticket">
                                        <i class="ti-check"></i>
                                        Complete
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <i class="ti-thumb-down"></i>
                                        Reject
                                    </a>
                                </li>
                            @break
                            
                            
                        @endswitch
                    
                    @endcan

                    @if ( ($ticket->status == 4 || $ticket->status == 5) && $ticket->user_id == auth()->user()->id )
                        <li>
                            <a href="">
                                <i class="ti-back-left"></i>
                                Request Re-Open
                            </a>
                        </li>
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

        <script>
            const completeTicketBtn = document.querySelector('#complete-sw-ticket');
            completeTicketBtn.addEventListener('click', function() 
            {
                
                fetch(`/change-ticket-status/{{ $ticket->id }}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 4 }) // Gửi status mới là "Complete"
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ticket marked as complete!');
                        location.reload(); // Tải lại trang để cập nhật trạng thái
                    } else {
                        alert('Failed to update ticket status.');
                    }
                })
                // .then(response => response.json())
                // .then(data => {
                //     console.log(data);
                //     // Cập nhật giao diện hoặc hiển thị thông báo thành công
                //     alert('Ticket marked as complete!');
                //     location.reload(); // Tải lại trang để cập nhật trạng thái
                // })
                // .catch(error => console.error('Error:', error));
            });
        </script>

    </body>

</html>