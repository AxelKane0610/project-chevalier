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
        </div>
    </body>
</html>