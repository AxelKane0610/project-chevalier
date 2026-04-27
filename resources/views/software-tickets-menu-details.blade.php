<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/software-ticket-details.js'])
        <link rel="stylesheet" href="{{ asset('icons/themify-icons.css') }}">
    </head>

    <body class="background-enable">

        
        <x-common-header title="EEG Software Support">
            <li>
                <form action="/software-tickets-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>

            <li>
                <form>
                    @csrf
                    <button type="submit"><i class="ti-search test-js"></i>Search</button>
                </form>

            </li>
            <li>
                <form action="/main-menu">
                    @csrf
                    <button type="submit"><i class="ti-layout-grid2"></i>Quick Navigation</button>
                </form>
            </li>

            @can('hasRole', 'ROLE_TICKET_SW_ADMIN', 'ROLE_SUPER_ADMIN') <!-- Chỉ hiển thị nút action nếu người dùng có vai trò admin hoặc super admin -->
            
                @switch($ticket->status)
                    @case(1)
                    @case(2)
                    @case(3)
                        <li>
                            <form action="#">
                                <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                            </form>
                        </li>
                        <li>
                            
                            <form action="{{ route('send-approval-request', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-angle-double-right"></i>Send Approval </button>
                            </form>
                            
                        </li>
                        <li>
                            <form action="" id="complete-sw-ticket" data-target="close-ticket-form" class="js-input-required-btn">
                                @csrf
                                <button type="button"><i class="ti-check"></i>Close Ticket</button>
                            </form>
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
                                @switch($ticket->priority)
                                    @case(1) Normal @break
                                    @case(2) Critical @break
                                    @case(3) High @break
                                    @case(4) Low @break
                                @endswitch
                            </h2>
                            
                    </li>
                    <li>
                        <lable>Support type</lable>
                        
                            <h2>
                                
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
                            </h2>
                            
                    </li>
                    <li>
                        <lable>Status</lable>
                        
                            <h2>
                                @switch($ticket->status)
                                    @case(1) Open @break
                                    @case(2) In Progress @break
                                    @case(3) Waiting Approval @break
                                    @case(4) Complete @break
                                    @case(5) Rejected @break
                                @endswitch
                            </h2>
                            
                    </li>
                    <li style="height: 200px;">
                        <lable>Issue description</lable>
                        
                        <p>{{ $ticket->description }}</p>
                        
                    </li>

                    <li>
                        <lable>Attachments</lable>
                        
                        <table class="attachments-table">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->attachments as $attachment)
                                    @if(in_array($attachment->type_of_ticket, [1, 2])) {{-- Cách viết gọn thay cho switch --}}
                                    <tr>
                                        <td>{{ $attachment->name }}</td>
                                        <td>
                                            
                                            
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                                <i class="ti-eye"></i>
                                            </a>
                                            
                                            
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" download="{{ $attachment->name }}" class="btn btn-secondary">
                                                <i class="ti-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </li>
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                

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

            <x-common-ticket-form title="EEG Ticket Close" id="close-ticket-form" action1="{{ route('close-software-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Status</label>
                <select name="ticket_status" class="ticket-form-body-input">
                    <option value="4">Complete</option>
                    <option value="5">Reject</option>
                    <option value="6">Cancel</option>
                </select>

                <label>Issue Owner</label>
                <select name="issue_owner" class="ticket-form-body-input">
                    <option value="1">System Matters</option>
                    <option value="2">Human Matters</option>
                    <option value="3">Customer Matters</option>
                    <option value="4">Others</option>
                    <option value="5">Parts Matters</option>
                </select>

                <label>Comment</label>
                <textarea name="ticket_comment" class="ticket-form-body-input" placeholder="Comment cho người tạo ticket nếu có"></textarea>
                <x-slot:footer>
                    <button type="submit" class="ticket-form-body-input">Close ticket</button>
                </x-slot:footer>
                
            </x-common-ticket-form>

            <x-common-ticket-form title="Edit Ticket EEG" id="edit-ticket-details" action1="">

                <label>Reciept</label>
                <input type="text" class="ticket-form-body-input" name="ticket_reciept" value=" {{ $ticket->ticket_reciept }}">

                <label>Support Type</label>
                <select name="support_type" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->support_type == 1)>Thêm mã part/product</option>
                    <option value="2" @selected($ticket->support_type == 2)>Rollback</option>
                    <option value="3" @selected($ticket->support_type == 3)>Hủy số phiếu/Ẩn lịch sử bảo hành</option>
                    <option value="4" @selected($ticket->support_type == 4)>Điều chỉnh thông tin</option>
                    <option value="5" @selected($ticket->support_type == 5)>Unmark Re-Repair</option>
                    <option value="6" @selected($ticket->support_type == 6)>Lỗi hệ thống</option>
                    <option value="7" @selected($ticket->support_type == 7)>Cấp quyền export data</option>
                    <option value="8" @selected($ticket->support_type == 8)>Đề xuất thay đổi/cải tiến</option>
                    <option value="9" @selected($ticket->support_type == 9)>Vấn đề khác</option>
                </select>

                <label>Priority</label>
                <select name="priority" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->priority == 1)>Normal</option>
                    <option value="2" @selected($ticket->priority == 2)>Critical</option>
                    <option value="3" @selected($ticket->priority == 3)>High</option>
                    <option value="4" @selected($ticket->priority == 4)>Low</option>
                </select>

                <label>Issue description</label>
                <textarea name="description" class="ticket-form-body-input multiple-row">{{$ticket->description}}</textarea>

                
                <label><b>Attachments</b></label>
                
                @if($ticket->attachments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                @foreach($ticket->attachments as $attachment)
                                    <tr>
                                        <td class="align-middle">
                                            
                                            <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank">
                                                {{ $attachment->name ?? 'File đính kèm' }}
                                            </a>
                                        </td>
                                        <td class="align-middle text-center" width="150">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="delete_files[]" value="{{ $attachment->id }}" id="del_{{ $attachment->id }}">
                                                <label class="form-check-label text-danger" for="del_{{ $attachment->id }}">
                                                    Xóa file này
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Tích vào ô "Xóa file này" nếu muốn gỡ bỏ file đính kèm trước đó.</small>
                @else
                    <p class="text-muted">Không có file nào được đính kèm</p>
                @endif
                
                <label class="ticket-form-body-input">Đính kèm thêm files:</label>
                <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" id="software-ticket-submit-btn">Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>

        </div>


        
        
    </body>

</html>