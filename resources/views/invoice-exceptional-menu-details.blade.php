<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/invoice-exceptional.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Invoice Exceptional Menu">
            <li>
                <form action="/invoice-exceptional-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
            </li>

            <li>
                <form>
                    <button type="submit"><i class="ti-search test-js"></i>Search</button>
                </form>

            </li>

            @switch($ticket->status)
                @case(1)
                    @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_USER')) && $ticket->user_id == auth()->user()->id)
                        <li>
                            <form id="send-approve-invoice-exceptional" data-target="send-approve-invoice-exceptional" action="{{ route('send-approve-invoice-exceptional', $ticket->id) }}" method="POST" class="js-input-required-btn">
                                <button type="submit"><i class="ti-angle-double-right"></i>Send Approval </button>
                            </form>
                        </li>
                    @endif
                @break

                @case(2)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER') )
                        <li>
                            <form id="approve-invoice-exceptional-lv1" class="js-input-required-btn" data-target="approve-invoice-exceptional-lv1" action="{{ route('invoice-exceptional-approve-lv1', $ticket->id) }}" method="POST">
                                
                                <button type="submit"><i class="ti-thumb-up"></i>Approve Lv1</button>
                            </form>
                        </li>

                        <li>
                            <form id="reject-invoice-exceptional" class="js-input-required-btn" data-target="reject-invoice-exceptional" action="{{ route('invoice-exceptional-reject', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                            </form>
                        </li>
                    @endif
                    
                @break

                @case(3)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER') )
                        <li>
                            <form id="approve-invoice-exceptional-lv2" class="js-input-required-btn" data-target="approve-invoice-exceptional-lv2" action="{{ route('invoice-exceptional-approve-lv2', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-up"></i>Fully approve</button>
                            </form>
                        </li>

                        <li>
                            <form id="reject-invoice-exceptional" class="js-input-required-btn" data-target="reject-invoice-exceptional" action="{{ route('invoice-exceptional-reject', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                            </form>
                        </li>
                    @endif
                    
                @break

                
                @case(5)
                    @if($ticket->user_id == auth()->user()->id || auth()->user()->hasRole('ROLE_SUPER_ADMIN'))
                        <li>
                            <form id="re-open-invoice-exceptional-ticket" class="js-input-required-btn" data-target="re-open-invoice-exceptional-ticket" action="{{ route('re-open-invoice-exceptional-ticket', $ticket->id) }}" method="PATCH">
                                @csrf
                                <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                            </form>
                        </li>
                    @endif
                @break

                
                
            @endswitch
        </x-common-header>

        <div class="invoice-exceptional-ticket-content">
            <x-common-ticket-detail-form>
                <h2>Tickets Details</h2>
                <li>
                    <lable>Receipt</lable>
                    <h2>{{ $ticket->ticket_receipt }}</h2>
                    
                </li>

                <li>
                    <lable>Support Type</lable>
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

                <li>
                    <lable>User Owner</lable>
                    <h2>{{ $ticket->user_owner->fullname }}</h2>
                    
                </li>

                <li>
                    <lable>Invoice Number</lable>
                    <h2>{{ $ticket->invoice_number }}</h2>
                    
                </li>

                <li>
                    <lable>Serial Number</lable>
                    <h2>{{ $ticket->serial_number }}</h2>
                    
                </li>

                <li>
                    <lable>Product Number</lable>
                    <h2>{{ $ticket->product_number }}</h2>
                    
                </li>

                <li>
                    <lable>Product Model</lable>
                    <h2>{{ $ticket->product_model }}</h2>
                    
                </li>

                <li>
                    <lable>Expired Date</lable>
                    <h2>{{ $ticket->expired_date }}</h2>
                    
                </li>

                <li>
                    <lable>Invoice Date</lable>
                    <h2>{{ $ticket->invoice_date }}</h2>
                    
                </li>

                <li>
                    <lable>Description</lable>
                    <h2>{{ $ticket->description }}</h2>
                    
                </li>

                <li>
                    <lable>Retail Name</lable>
                    <h2>{{ $ticket->retail_name }}</h2>
                    
                </li>

                <li>
                    <lable>Company/Customer Name</lable>
                    <h2>{{ $ticket->company_customer_name }}</h2>
                    
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

            </x-common-ticket-detail-form>

            <x-common-ticket-comments action1="{{ route('add-comment-invoice-exceptional-ticket', $ticket->id) }}" id="add-comment-form">
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

            <x-common-ticket-form title="Edit Invoice Exceptional Ticket" id="edit-ticket-details" action1="{{ route('edit-invoice-exceptional-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value="{{ $ticket->ticket_receipt }}">

                <label>Invoice Number</label>
                <input type="text" class="ticket-form-body-input" name="invoice_number" value="{{ $ticket->invoice_number }}">

                <label>Serial Number</label>
                <input type="text" class="ticket-form-body-input" name="serial_number" value="{{ $ticket->serial_number }}">

                <label>Product Number</label>
                <input type="text" class="ticket-form-body-input" name="product_number" value="{{ $ticket->product_number }}">

                <label>Product Model</label>
                <input type="text" class="ticket-form-body-input" name="product_model" value="{{ $ticket->product_model }}">

                <label>Invoice Date</label>
                <input type="date" class="ticket-form-body-input" name="invoice_date" value="{{ $ticket->invoice_date }}">

                <label>Expired Date</label>
                <input type="date" class="ticket-form-body-input" name="expired_date" value="{{ $ticket->expired_date }}">

                <label>Description</label>
                <input type="text" class="ticket-form-body-input" name="description" value="{{ $ticket->description }}">

                <label>Retail Name</label>
                <input type="text" class="ticket-form-body-input" name="retail_name" value="{{ $ticket->retail_name }}">

                <label>Company/Customer Name</label>
                <input type="text" class="ticket-form-body-input" name="company_customer_name" value="{{ $ticket->company_customer_name }}">

                <label>Support Type</label>
                <select name="support_type" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->support_type == 1)>Hóa đơn xuất sau (1 máy)</option>
                    <option value="2" @selected($ticket->support_type == 2)>Hóa đơn xuất sau (Nhiều máy)</option>
                    <option value="3" @selected($ticket->support_type == 3)>Kích hoạt bảo hành (1 máy)</option>
                    <option value="4" @selected($ticket->support_type == 4)>Kích hoạt bảo hành (Nhiều máy)</option>
                </select>

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
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" >Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>
        </div>
    </body>
</html>