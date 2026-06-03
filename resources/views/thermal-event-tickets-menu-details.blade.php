<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Thermal Event Exceptional">
            <li>
                <form action="/thermal-event-tickets-menu">
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
                    @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_USER')) && $ticket->user_id == auth()->user()->id)
                        <li>
                            <form data-target="send-approval-form" class="js-input-required-btn">
                                <button type="button"><i class="ti-angle-double-right"></i>Send Approval </button>
                            </form>
                        </li>

                    @endif
                @break

                @case(2)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_LV1_APPROVER') )
                        <li>
                            <form id="approve-ticket-form" class="js-input-required-btn" data-target="approve-ticket-form" action="{{ route('thermal-event-approve-lv1', $ticket->id) }}" method="POST">
                                
                                <button type="submit"><i class="ti-thumb-up"></i>Approve Lv1</button>
                            </form>
                        </li>

                        <li>
                            <form id="reject-ticket-form" class="js-input-required-btn" data-target="reject-ticket-form" action="{{ route('thermal-event-reject', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                            </form>
                        </li>
                    @endif
                    
                @break

                @case(3)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_LV2_APPROVER') )
                        <li>
                            <form id="approve-ticket-form" class="js-input-required-btn" data-target="approve-ticket-form" action="{{ route('thermal-event-approve-lv2', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-up"></i>Fully approve</button>
                            </form>
                        </li>

                        <li>
                            <form id="reject-ticket-form" class="js-input-required-btn" data-target="reject-ticket-form" action="{{ route('thermal-event-reject', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                            </form>
                        </li>
                    @endif
                    
                @break

                
                
            @endswitch
        </x-common-header>

        <div class="thermal-event-ticket-content">
            <x-common-ticket-detail-form>
                <h2>Tickets Details</h2>
                <li>
                    <lable>Receipt</lable>
                    <h2>{{ $ticket->ticket_receipt }}</h2>
                    
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
                    <lable>CDAX ID</lable>
                    <h2>{{ $ticket->cdax_id }}</h2>
                    
                </li>

                <li>
                    <lable>Customer Type</lable>
                    <!-- <h2>{{ $ticket->customer_type }}</h2> -->
                     <h2>
                        <span class="ticket-customer-type {{ $ticket->customer_type_data['class'] }}">
                            {{ $ticket->customer_type_data['text'] }}
                        </span>
                     </h2>
                    
                    
                </li>

                <li>
                    <lable>Company/Customer Name</lable>
                    <h2>{{ $ticket->company_customer_name }}</h2>
                    
                </li>

                <li>
                    <lable>Part MO Number</lable>
                    <h2>{{ $ticket->part_mo_number }}</h2>
                    
                </li>

                <li>
                    <lable>Part Number</lable>
                    <h2>{{ $ticket->part_number }}</h2>
                    
                </li>

                <li>
                    <lable>Part Description</lable>
                    <h2>{{ $ticket->part_description }}</h2>
                    
                </li>

                <li>
                    <lable>Part CT Number</lable>
                    <h2>{{ $ticket->part_ct_number }}</h2>
                    
                </li>

                <li>
                    <lable>User Observation</lable>
                    <h2>{{ $ticket->user_observations }}</h2>
                    
                </li>

                
                <li style="height: 200px;">
                    <lable>Issue description</lable>
                    
                    <p>{{ $ticket->description }}</p>
                    
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
                        
                    
                </li>

                @if(($ticket->status == 1) && $ticket->user_id == auth()->user()->id)
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                @endif
            </x-common-ticket-detail-form>

            <x-common-ticket-comments action1="{{ route('add-comment-thermal-event-ticket', $ticket->id) }}" id="add-comment-form">
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





            <x-common-ticket-form title="Edit Thermal Event Ticket" id="edit-ticket-details" action1="{{ route('edit-thermal-event-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value=" {{ $ticket->ticket_receipt }}">

                <label>Serial Number</label>
                <input type="text" class="ticket-form-body-input" name="serial_number" value=" {{ $ticket->serial_number }}">

                <label>Product Number</label>
                <input type="text" class="ticket-form-body-input" name="product_number" value=" {{ $ticket->product_number }}">

                <label>Product Model</label>
                <input type="text" class="ticket-form-body-input" name="product_model" value=" {{ $ticket->product_model }}">

                <label>Description</label>
                <input type="text" class="ticket-form-body-input" name="description" value=" {{ $ticket->description }}">

                <label>CDAX ID</label>
                <input type="text" class="ticket-form-body-input" name="cdax_id" value=" {{ $ticket->description }}">

                <label>Customer Type</label>
                <select name="customer_type" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->customer_type == 1)>Khách hàng lẻ</option>
                    <option value="2" @selected($ticket->customer_type == 2)>Khách hàng công ty/doanh nghiệp</option>
                    <option value="3" @selected($ticket->customer_type == 3)>T1/Đại lý bán lẻ</option>
                    
                </select>

                <label>Company/Customer Name</label>
                <input type="text" class="ticket-form-body-input" name="company_customer_name" value=" {{ $ticket->company_customer_name }}">

                <label>Part MO Number</label>
                <input type="text" class="ticket-form-body-input" name="part_mo_number" value=" {{ $ticket->part_mo_number }}">

                <label>Part Number</label>
                <input type="text" class="ticket-form-body-input" name="part_number" value=" {{ $ticket->part_number }}">

                <label>Part Description</label>
                <input type="text" class="ticket-form-body-input" name="part_description" value=" {{ $ticket->part_description }}">

                <label>Part CT Number</label>
                <input type="text" class="ticket-form-body-input" name="part_ct_number" value=" {{ $ticket->part_ct_number }}">

                <label>User Observations</label>
                <input type="text" class="ticket-form-body-input" name="user_observations" value=" {{ $ticket->user_observations }}">

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

            
        </div>
    </body>
</html>