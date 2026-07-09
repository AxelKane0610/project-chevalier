<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/laser-engraving.js', 'resources/css/icons/themify-icons.css'])

        
    </head>

    <body class="background-enable">
        <x-common-header title="Laser Engraving Ticket Details">
            <li>
                <form action="/laser-engraving-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>

            <li>
                <form>
                    <button type="submit"><i class="ti-search test-js"></i>Search</button>
                </form>

            </li>
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-layout-grid2"></i>Quick Navigation</button>
                </form>
            </li>
            @switch($ticket->status)
                @case(1)
                @case(2)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN'))
                        <li>
                            <form id="change-laser-engraving-status-to-in-progress" class="js-input-required-btn" data-target="change-laser-engraving-status-to-in-progress" action="{{ route('change-laser-engraving-status-to-in-progress', $ticket->id) }}" method="PATCH">
                                @csrf
                                <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                            </form>
                        </li>
                        
                        <li>
                            <form id="complete-laser-engraving-ticket" data-target="close-laser-engraving-ticket-form" class="js-input-required-btn">
                                @csrf
                                <button type="button"><i class="ti-check"></i>Close Ticket</button>
                            </form>
                        </li>
                    @endif
                @break
                
                @case(3)
                @case(4)
                    @if($ticket->user_id == auth()->user()->id || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN'))
                        <li>
                            <form id="re-open-laser-engraving-ticket" class="js-input-required-btn" data-target="re-open-laser-engraving-ticket" action="{{ route('re-open-laser-engraving-ticket', $ticket->id) }}" method="PATCH">
                                @csrf
                                <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                            </form>
                        </li>
                    @endif
                    
                @break
            @endswitch

        </x-common-header>

        <div class = "laser-engraving-ticket-content">
            <x-common-ticket-detail-form>
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
                    <lable>Status</lable>
                    <h2>
                        <span class="ticket-status {{ $ticket->status_data['class'] }}">
                            {{ $ticket->status_data['text'] }}
                        </span>
                    </h2>
                </li>
                <li>
                    <label>Info base</label>
                    <h2>{{ $ticket->info_base }}</h2>
                </li>
                <li style="height: 200px;">
                    <lable>Description</lable>
                    <p>{{ $ticket->description }}</p>
                </li>

                <li>
                    <lable>Attachments ({{ $ticket->active_attachments->count() }} files)</lable>
                    <x-common-attachments-table>
                            @foreach ($ticket->active_attachments as $attachment)
                                @if(in_array($attachment->type_of_ticket, [3])) {{-- Cách viết gọn thay cho switch --}}
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

                @if(($ticket->status == 1) && $ticket->user_id == auth()->user()->id)
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-laser-engraving-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                @endif
            </x-common-ticket-detail-form>

            <x-common-ticket-form title="Edit Ticket Khắc Base" id="edit-laser-engraving-ticket-details" action1="{{ route('edit-laser-engraving-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value=" {{ $ticket->ticket_receipt }}">

                <label>Priority</label>
                <select name="priority" class="ticket-form-body-input">
                    <option value="1" {{ $ticket->priority == 1 ? 'selected' : '' }}>Normal</option> // Nếu priority của ticket đang là 1 thì thêm thuộc tính selected vào option này, ngược lại thì không có
                    <option value="2" {{ $ticket->priority == 2 ? 'selected' : '' }}>Critical</option>
                    <option value="3" {{ $ticket->priority == 3 ? 'selected' : '' }}>High</option>
                    <option value="4" {{ $ticket->priority == 4 ? 'selected' : '' }}>Low</option>
                </select>

                <label>Info base</label>
                <input type="text" class="ticket-form-body-input" name="info_base" value=" {{ $ticket->info_base }}">
                <label>Description</label>
                <textarea class="ticket-form-body-input" name="description" rows="5">{{ $ticket->description }}</textarea>
                
                <label><b>Attachments</b></label>
                
                @if($ticket->active_attachments->count() > 0) 
                    <x-common-attachments-table>
                        @foreach($ticket->active_attachments as $attachment)
                            <tr>
                                <td>
                                    {{ $attachment->name ?? 'File đính kèm' }}
                                </td>
                                <td>
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
                {{-- <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Save</button> 
                </x-slot:footer> --}}

                <div class="upload-group">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>

            <x-common-ticket-comments action1="{{ route('add-comment-laser-engraving-ticket', $ticket->id) }}" id="add-comment-form">
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
            </x-common-ticket-comments>

            <x-common-ticket-form title="Close Ticket Khắc Base" id="close-laser-engraving-ticket-form" action1="{{ route('close-laser-engraving-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Status</label>
                <select name="ticket_status" class="ticket-form-body-input" required>
                    <option value="3">Completed</option>
                    <option value="4">Rejected</option>
                </select>

                <label>Comment</label>
                <textarea name="ticket_comment" class="ticket-form-body-input" placeholder="Comment cho người tạo ticket nếu có"></textarea>

                <label class="ticket-form-body-input">Attach File:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple required>
                    <ul class="file-list"></ul>
                </div>
                

                <x-slot:footer>
                    <button type="submit">Close ticket</button>
                </x-slot:footer>
            </x-common-ticket-form>

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
    </body>
</html>