<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/out-of-office.js', 'resources/css/icons/themify-icons.css'])
            
    </head>

    <body>
        <div>
            <x-common-header title="Out of Office Ticket Details">
                <li>
                    <form action="/out-of-office-tickets-menu">
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="#">
                        <button type="submit"><i class="ti-search"></i>Search</button>
                    </form>
                </li>
                @if ($ticket->status == '1' && $ticket->user_id == auth()->user()->id)
                    <li>
                        <form id="send-approve-out-of-office-ticket" data-target="send-approve-out-of-office-ticket" action="{{route('send-approve-out-of-office-ticket', $ticket->id) }}">
                            @method('POST')
                            <button type="submit"><i class="ti-angle-double-right"></i>Send Approval </button>
                        </form>
                    </li>
                @endif

                @if ($ticket->status == '2' && auth()->user()->hasRole('ROLE_OUT_OF_OFFICE_ADMIN'))
                    @can('is-leader-of-ticket', $ticket)
                    <li>
                        <form id="approve-out-of-office-ticket" class="js-input-required-btn" data-target="approve-out-of-office-ticket" action="{{ route('approve-out-of-office-ticket', $ticket->id) }}" method="POST">
                            
                            <button type="submit"><i class="ti-thumb-up"></i>Approve</button>
                        </form>
                    </li>

                    <li>
                        <form id="reject-out-of-office-ticket" class="js-input-required-btn" data-target="reject-out-of-office-ticket" action="{{ route('reject-out-of-office-ticket', $ticket->id) }}" method="POST">
                            
                            <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                        </form>
                    </li>
                    @endcan
                @endif

                @if($ticket->user_id == auth()->user()->id && $ticket->status == '4')
                    <li>
                        <form id="re-open-out-of-office-ticket" class="js-input-required-btn" data-target="re-open-out-of-office-ticket" action="{{ route('re-open-out-of-office-ticket', $ticket->id) }}" method="POST">
                            
                            <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                        </form>
                    </li>
                @endif


            </x-common-header>

            <div class="out-of-office-tickets-content">
            <x-common-ticket-detail-form>
                <h2>Ticket Details</h2>
                <li>
                    <lable>User Owner</lable>
                    <h2>{{ $ticket->user_owner->fullname }}</h2>
                    
                </li>

                <li>
                    <lable>Type of Leave</lable>
                    <h2>
                        <span class="ticket-status {{ $ticket->type_of_leave_data['class'] }}">
                            {{ $ticket->type_of_leave_data['text'] }}
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

                <li>
                    <lable>Reasons for leave</lable>
                    <h2>{{ $ticket->reasons_for_leave }}</h2>
                    
                </li>

                <li>
                    <lable>Days of leave</lable>
                    <h2>{{ $ticket->days_of_leave }}</h2>
                    
                </li>

                <li>
                    <lable>Start Date</lable>
                    <h2>{{ $ticket->start_date }}</h2>
                    
                </li>

                <li>
                    <lable>End Date</lable>
                    <h2>{{ $ticket->end_date }}</h2>
                    
                </li>

                <li>
                    <lable>Attachments ({{ $ticket->active_attachments->count() }} files)</lable>
                    <x-common-attachments-table>
                        @foreach ($ticket->active_attachments as $attachment)
                            
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

                    @if(($ticket->status == '1') && $ticket->user_id == auth()->user()->id)
                        <x-slot:footer>
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        </x-slot:footer>
                    @endif
                </li>
            </x-common-ticket-detail-form>

            <x-common-ticket-comments action1="{{ route('add-comment-out-of-office-ticket', $ticket->id) }}" id="add-comment-out-of-office-ticket-form">
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
                    <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                    <ul id="fileList"></ul>
                    <button type="submit"><i class="ti-comment"></i>Comment</button>

                </x-slot:footer>
            </x-common-ticket-comments>

            <div class="software-tickets-tracking-info">
                <h2>Tracking Info</h2>
                    
                @foreach($ticket->ticket_tracking_info as $tracking)
                    <h3>
                        {{ $tracking->user->fullname }}
                        {{ $tracking->action }}
                        {{ $tracking->created_at }}
                    </h3>
                    
    
                @endforeach
                
            </div>
        </div>

        <x-common-ticket-form title="Edit Out of Office Ticket" id="edit-ticket-details" action1="{{ route('edit-out-of-office-ticket', $ticket->id) }}">
            @method('PATCH')

            <li>
                <lable>Type of Leave</lable>
                <select name="type_of_leave" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->type_of_leave == 1)>Xin nghỉ phép</option>
                    <option value="2" @selected($ticket->type_of_leave == 2)>Xin đi trễ</option>
                    <option value="3" @selected($ticket->type_of_leave == 3)>Xin về sớm</option>
                    <option value="4" @selected($ticket->type_of_leave == 4)>Xin không chấm công vào</option>
                    <option value="5" @selected($ticket->type_of_leave == 5)>Xin không chấm công ra</option>
                    <option value="6" @selected($ticket->type_of_leave == 6)>Quên chấm công vào/ra</option>
                </select>
                
                
            </li>

            <li>
                <lable>Reasons for leave</lable>
                <input type="text" class="ticket-form-body-input" name="reasons_for_leave" value="{{ $ticket->reasons_for_leave }}">

                
            </li>

            <li>
                <lable>Days of leave</lable>
                <input type="number" class="ticket-form-body-input" name="days_of_leave" value="{{ $ticket->days_of_leave }}" step="0.5">
            </li>

            <li>
                <lable>Start Date</lable>
                <input type="datetime-local" class="ticket-form-body-input" name="start_date" value="{{ $ticket->start_date }}" required>

                
            </li>

            <li>
                <lable>End Date</lable>
                <input type="datetime-local" class="ticket-form-body-input" name="end_date" value="{{ $ticket->end_date }}" required>
            </li>

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
                <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" >Save</button> 
                </x-slot:footer>
        </x-common-ticket-form>

    </body>

</html>