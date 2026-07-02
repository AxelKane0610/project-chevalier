<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/loan-unit-part.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Loan Units & Parts Ticket Details">
            <li>
                <form action="/loan-unit-part-menu">
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
        </x-common-header>

        <div class="common-table-container">
            <table class="common-table" width="100%">
                <th width="5%"></th>
                <th width="50px">Part Request</th>
                <th width="50px">Status</th>
                <th width="50px">Loan Unit Asset Tag</th> 
                <th width="50px">Loan Unit Serial Number</th> 
                <th width="50px">CT Loaned</th>
                <th width="50px">New CT Return</th>
                <th width="50px">Original</th>
                <th width="50px">Start Date</th>
                <th width="50px">End Date</th>

                


                @foreach($ticket->parts_details as $parts)
                <tr>
                    <td>
                        <form class="js-input-required-btn" data-target="edit-loan-unit-part-details" action="" method="PATCH">
                            <button type="button" 
                            class="btn-edit-part"
                            data-id="{{ $parts->id }}"
                            data-receipt = "{{$parts->ticket_receipt}}"
                            data-status ="{{ $parts->status }}"
                            data-part_request="{{ $parts->part_request }}"
                            data-asset_tag="{{ $parts->loan_unit_asset_tag }}"
                            data-serial_number="{{ $parts->loan_unit_serial_number }}"
                            data-ct_loaned="{{ $parts->ct_loaned }}"><i class="ti-pencil"></i></button>
                        </form>

                        <form class="js-input-required-btn" data-target="delete-thermal-event-part-details" id="delete-thermal-event-part-details" action="{{ route('delete-thermal-event-part-details', $parts->id) }}" method="PATCH">
                            <button type="submit"><i class="ti-na"></i></button>
                        </form>

                        
                    </td>
                    <td>{{$parts->part_request}}</td>
                    <td>
                        <span class="ticket-status {{ $parts->status_data['class'] }}">
                            {{ $parts->status_data['text'] }}
                        </span>
                    </td>
                    
                    <td>{{$parts->loan_unit_asset_tag}}</td>
                    <td>{{$parts->loan_unit_serial_number}}</td>
                    <td>{{$parts->ct_loaned}}</td>
                    <td>{{$parts->new_ct_return}}</td>
                    <td>{{$parts->original}}</td>
                    <td>{{$parts->start_date}}</td>
                    <td>{{$parts->end_date}}</td>


                </tr>
                @endforeach
            </table>
        </div>

        <div class="loan-unit-part-ticket-content">
            <x-common-ticket-detail-form>
                <li>
                    <lable>Receipt</lable>
                    <h2>{{ $ticket->ticket_receipt }}</h2>
                    
                </li>

                <li>
                    <lable>User Owner</lable>
                    <h2>{{ $ticket->user_owner->fullname }}</h2>
                    
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
                    <lable>Customer Unit Info</lable>
                    <h2>{{ $ticket->customer_unit_info }}</h2>
                    
                </li>

                <li>
                    <lable>Ngày request</lable>
                    <h2>{{ $ticket->created_at }}</h2>
                    
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

                    @if(($ticket->status == 1) && $ticket->user_id == auth()->user()->id)
                        <x-slot:footer>
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        </x-slot:footer>
                    @endif
                </li>
            </x-common-ticket-detail-form>

            <x-common-ticket-comments action1="{{ route('add-comment-loan-unit-part-ticket', $ticket->id) }}" id="add-comment-form">
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

            <x-common-ticket-form title="Edit Loan Unit & Part Ticket" id="edit-ticket-details" action1="{{ route('edit-loan-unit-part-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value="{{ $ticket->ticket_receipt }}">

                <label>Customer Unit Info</label>
                <input type="text" class="ticket-form-body-input" name="customer_unit_info" value="{{ $ticket->customer_unit_info }}">

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
                <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" >Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>

            <x-common-ticket-form title="Edit Loan Unit & Part Details" id="edit-loan-unit-part-details" action1="">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value="" id="edit-receipt-part-details">

                <label>Part Request</label>
                <input type="text" class="ticket-form-body-input" name="part_request" value="" id="edit-part-request-part-details">

                @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')))
                    <label>Loan Unit Asset Tag</label>
                    <input type="text" class="ticket-form-body-input" name="loan_unit_asset_tag" value="">
                @endif

                
            </x-common-ticket-form>

        </div>
    </body>

</html>