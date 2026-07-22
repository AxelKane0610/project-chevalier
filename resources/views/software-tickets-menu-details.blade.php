<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/software-ticket-details.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">

        
        <x-common-header title="EEG Software Support">
            <li>
                <form action="/software-tickets-menu">
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

            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-layout-grid2"></i>Quick Navigation</button>
                </form>
            </li>

            
                    @switch($ticket->status)
                        @case(1)
                        @case(2)
                            @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TICKET_SW_ADMIN'))
                                <li>
                                    <form id="change-ticket-software-status-to-in-progress" class="js-input-required-btn" data-target="change-ticket-software-status-to-in-progress" action="{{ route('change-ticket-software-status-to-in-progress', $ticket->id) }}" method="PATCH">
                                        @csrf
                                        <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                                    </form>
                                </li>

                                <li>
                                    <form data-target="send-approval-form" class="js-input-required-btn">
                                        <button type="button"><i class="ti-angle-double-right"></i>Send Approval </button>
                                    </form>
                                </li>

                                <li>
                                    <form action="" id="complete-sw-ticket" data-target="close-ticket-form" class="js-input-required-btn">
                                        @csrf
                                        <button type="button"><i class="ti-check"></i>Close Ticket</button>
                                    </form>
                                </li>
                            @endif
                        @break

                        @case(3)
                            @if(auth()->user()->hasRole('ROLE_APPROVE_ROLLBACK') || auth()->user()->hasRole('ROLE_APPROVE_EXPORT_DATA')) <!-- Chỉ hiển thị nút action nếu người dùng là leader của ticket -->
                                @can('is-leader-of-ticket', $ticket)
                                <li>
                                    <form id="approve-ticket-form" class="js-input-required-btn" data-target="approve-ticket-form" action="{{ route('approve-ticket', $ticket->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"><i class="ti-thumb-up"></i>Approve</button>
                                    </form>
                                </li>

                                <li>
                                    <form id="reject-ticket-form" class="js-input-required-btn" data-target="reject-ticket-form" action="{{ route('reject-ticket', $ticket->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                                    </form>
                                </li>
                                @endcan
                            @endif
                            
                        @break

                        
                        @case(5)
                        @case(6)
                            @if($ticket->user_id == auth()->user()->id || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SW_TICKET_ADMIN'))
                            <li>
                                <form id="re-open-ticket-form" class="js-input-required-btn" data-target="re-open-ticket-form" action="{{ route('re-open-ticket', $ticket->id) }}" method="PATCH">
                                    @csrf
                                    <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                                </form>
                            </li>
                            @endif
                        @break
                        
                        
                    @endswitch
            
        </x-common-header>

                
        {{-- <div class="software-ticket-content"> --}}

            {{-- <x-common-ticket-detail-form>
                <h2>Tickets Details</h2>
                <li>
                    <lable>Receipt</lable>
                    
                    <h2>{{ $ticket->ticket_receipt }}</h2>
                    
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
                            <span class="ticket-priority {{ $ticket->priority_data['class'] }}">
                                {{ $ticket->priority_data['text'] }}
                            </span>
                        </h2>
                        
                </li>
                <li>
                    <lable>Support type</lable>
                    
                        <h2>
                            <span class="ticket-support-type {{ $ticket->support_type_data['class'] }}">
                                {{ $ticket->support_type_data['text'] }}
                            </span>

                        </h2>
                        
                </li>
                <li>
                    <lable>Status</lable>
                    
                        <h2>
                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                {{ $ticket->status_data['text'] }}
                            </span>

                        </h2>
                        
                </li>
                <li style="height: 200px;">
                    <lable>Issue description</lable>
                    
                    <p>{{ $ticket->description }}</p>
                    
                </li>

                <li>
                    <lable>Attachments ({{ $ticket->active_attachments->count() }} files)</lable>
                    <x-common-attachments-table>
                            @foreach ($ticket->active_attachments as $attachment)
                                @if(in_array($attachment->type_of_ticket, [1]))
                                <tr>
                                    <td>{{ $attachment->name }}</td>
                                    <td>
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" download="{{ $attachment->name }}" class="btn btn-secondary">
                                            <i class="ti-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                    </x-common-attachments-table>
                </li>
                    
                @if(($ticket->status == '1') && $ticket->user_id == auth()->user()->id)
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                @endif
                

            </x-common-ticket-detail-form> --}}


            {{-- <x-common-ticket-comments action1="{{ route('add-comment-software-ticket', $ticket->id) }}" id="add-comment-form">
                <h2>Comments</h2>
                @foreach($ticket->ticket_comments as $comment)
                <li>
                    <h2>{{ $comment->user->fullname }}</h2>
                    <h3>{{ $comment->created_at }}</h3>
                    <p>{{ $comment->comment }}</p>
                    @if ($comment->attachments->count() > 0)
                    <x-common-attachments-table>
                            @foreach($comment->attachments as $attachment)
                                <tr>
                                    <td>{{ $attachment->name }}</td>
                                    <td>
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" download="{{ $attachment->name }}" class="btn btn-secondary">
                                            <i class="ti-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                    </x-common-attachments-table>
                    @endif
                    
                </li>
                @endforeach

                

                <x-slot:footer>
                    
                    <label>Write a comment</label>
                    <textarea name="comment" style="height: 100px; font-family: inherit ;" placeholder="Nhập comment tại đây"></textarea>
                    <label class="ticket-form-body-input">Attach File:</label>
                    <div class="upload-group ">
                        <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                        <ul class="file-list"></ul>
                    </div>
                    <button type="submit"><i class="ti-comment"></i>Comment</button>

                </x-slot:footer>
            </x-common-ticket-comments> --}}

            {{-- <div class="software-tickets-tracking-info">
                <h2>Tracking Info</h2>
                    
                @foreach($ticket->ticket_tracking_info as $tracking)
                    <h3>
                        {{ $tracking->user->fullname }}
                        {{ $tracking->action }}
                        {{ $tracking->created_at }}
                    </h3>
                    
    
                @endforeach
                
            </div> --}}



            

            <div class="container-fluid px-4 py-4">

                <div class="row g-4" style="min-height: calc(100vh - 90px);">

                    <!-- ================= Ticket Detail ================= -->

                    <x-common-ticket-details-card 
                        :rows="[
                            [
                                'icon' => 'ti-receipt',
                                'label' => 'Receipt',
                                'value' => $ticket->ticket_receipt,
                                'type' => 'text'
                            ],
                            [
                                'icon' => 'ti-user',
                                'label' => 'Người request',
                                'value' => $ticket->user_owner->fullname,
                                'type' => 'text'
                            ],
                            [
                                'icon' => 'ti-agenda',
                                'label' => 'Ngày request',
                                'value' => $ticket->created_at,
                                'type' => 'text'
                            ],
                            [
                                'icon' => 'ti-angle-double-up',
                                'label' => 'Priority',
                                'value' => match ($ticket->priority) {
                                    '1' => 'Normal',
                                    '2' => 'Critical',
                                    '3' => 'High',
                                    '4' => 'Low',

                                    default => 'Unknown',
                                },
                                'type' => 'badge',
                                'color' => match ($ticket->priority) {
                                    '1' => 'success',
                                    '2' => 'danger',
                                    '3' => 'warning',
                                    '4' => 'primary',

                                    default => 'Unknown',
                                },
                            ],
                            [
                                'icon' => 'ti-arrow-right',
                                'label' => 'Support Type',
                                'value' => match ($ticket->support_type) {
                                    '1' => 'Thêm mã part/product',
                                    '2' => 'Rollback',
                                    '3' => 'Hủy số phiếu/Ẩn lịch sử bảo hành',
                                    '4' => 'Điều chỉnh thông tin',
                                    '5' => 'Unmark Re-Repair',
                                    '6' => 'Lỗi hệ thống',
                                    '7' => 'Cấp quyền export data',
                                    '8' => 'Đề xuất thay đổi/cải tiến',
                                    '9' => 'Vấn đề khác',


                                    default => 'Unknown',
                                },
                                'type' => 'badge',
                                'color' => 'primary'
                            ],
                            [
                                'icon' => 'ti-arrow-circle-right',
                                'label' => 'Status',
                                'value' => match ($ticket->status) {
                                    '1' => 'Open',
                                    '2' => 'In progress',
                                    '3' => 'Waiting approval',
                                    '4' => 'Complete',
                                    '5' => 'Rejected',
                                    '6' => 'Canceled',

                                    default => 'Unknown',
                                },
                                'type' => 'badge',
                                'color' => match ($ticket->status) {
                                    '1' => 'primary',
                                    '2' => 'secondary',
                                    '3' => 'info',
                                    '4' => 'success',
                                    '5' => 'light',
                                    '6' => 'dark',

                                    default => 'Unknown',
                                },
                            ],
                            [
                                'icon' => 'ti-menu',
                                'label' => 'Issue Description',
                                'value' => $ticket->description,
                                'type' => 'text'
                            ]
                        ]"

                        
                    >
                    
                    <x-common-attachments-table-card
                        :attachments="$ticket->active_attachments"
                    />

                    
                    <x-slot:footer>
                        @if((($ticket->status == '1') && $ticket->user_id == auth()->user()->id) || (auth()->user()->hasRole('ROLE_SUPER_ADMIN')))
                        <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        @endif
                    </x-slot:footer>
                    

                    </x-common-ticket-details-card>
                        

                    <!-- ================= Comment ================= -->

                    <x-common-ticket-comments-card
                        :comments="$ticket->ticket_comments"
                        :showAttachments="true"
                        :actionRoute="route('add-comment-software-ticket', $ticket->id)"
                    >


                    </x-common-ticket-comments-card>

                    <!-- ================= Timeline ================= -->

                    <div class="col-lg-4">
                        <div class="card shadow border-0 rounded-4 h-100">
                            <div class="card-header bg-white py-3 px-4">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="bi bi-clock-history text-warning me-2"></i>
                                    Tracking History
                                </h5>

                            </div>

                            <div class="card-body p-4 overflow-auto">
                                <!-- item -->
                                <div class="d-flex mb-4">
                                    <div class="me-3 text-center">
                                        <div class="bg-primary rounded-circle"
                                            style="width:14px;height:14px;"></div>

                                        <div class="border-start mx-auto"
                                            style="height:55px;"></div>

                                    </div>

                                    <div>
                                        <div class="fw-semibold">
                                            Nguyễn Thanh Hải
                                        </div>

                                        <div class="text-muted">
                                            Created Ticket
                                        </div>

                                        <small class="text-secondary">
                                            16 Jun 2026 14:38
                                        </small>

                                    </div>

                                </div>

                                <!-- item -->
                                

                                <!-- item -->

                                

                            </div>

                        </div>

                    </div>

                </div>

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

            <x-common-ticket-form title="Send Approval" id="send-approval-form" action1="{{ route('send-approval-request', $ticket->id) }}">
                @method('POST')
                <label>Approval Type</label>
                <select name="approval_type" class="ticket-form-body-input">
                    <option value="1">Rollback Warranty</option>
                    <option value="2">Rollback Trade</option>
                    <option value="3">Cấp quyền export data</option>
                </select>


                <x-slot:footer>
                    <button type="submit" class="ticket-form-body-input">Send Approval</button>
                </x-slot:footer>
                
            </x-common-ticket-form>

            <x-common-ticket-form title="Edit Ticket EEG" id="edit-ticket-details" action1="{{ route('edit-software-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value=" {{ $ticket->ticket_receipt }}">

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
                <textarea name="description" class="ticket-form-body-input multiple-row" style="height: 200px;">{{$ticket->description}}</textarea>

                
                <label><b>Attachments</b></label>
                
                @if($ticket->active_attachments->count() > 0) 
                    <x-common-attachments-table>
                        @foreach($ticket->active_attachments as $attachment)
                            <tr>
                                <td>
                                    
                                    <!-- <a href="{{ asset('attachments/' . $attachment->path) }}" target="_blank"> -->
                                        {{ $attachment->name ?? 'File đính kèm' }}
                                    <!-- </a> -->
                                </td>
                                <td>
                                    <!-- <div class="form-check">
                                        
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <input class="form-check-input" type="checkbox" name="delete_files[]" value="{{ $attachment->id }}" id="del_{{ $attachment->id }}">
                                        <label class="form-check-label text-danger" for="del_{{ $attachment->id }}">
                                            Xóa
                                        </label>
                                    </div> -->

                                    <div>
                                        
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <input type="checkbox" name="delete_files[]" value="{{ $attachment->id }}" id="del_{{ $attachment->id }}">
                                        <label for="del_{{ $attachment->id }}">
                                            Xóa
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-common-attachments-table>
                            
                    <small class="text-muted">Tích vào ô "Xóa" nếu muốn gỡ bỏ file đính kèm trước đó.</small>
                @else
                    <p class="text-muted">Không có file nào được đính kèm</p>
                @endif
                
                <label class="ticket-form-body-input">Đính kèm thêm files:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" >Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>

        {{-- </div> --}}


        
        
    </body>

</html>