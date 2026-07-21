<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Spectre - Crown Item Details">
            <li>
                <form action="/spectre-crown-warehouse-menu">
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
                <th width="25%">User Owner</th>
                <th width="25%">Receipt</th>   
                <th width="25%">Part Request</th>
                <th width="25%">CT Loaned</th> 
                <th width="25%">New CT Return</th> 
                <th width="25%">Status</th> 
                <th width="25%">Start Date</th> 
                <th width="25%">End Date</th> 



                @foreach($item_details->loan_unit_part_tickets as $ticket)
                <tr>
                    <td>
                        
                        <button type="submit"><i class="ti-na"></i></button>
                        <a href="/loan-unit-part-ticket-details/{{ $ticket->ticket_id }}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>

                    </td>
                    
                    <td>{{$ticket->user_owner->fullname}}</td>
                    <td>{{$ticket->ticket_receipt}}</td>
                    <td>{{$ticket->part_request}}</td>
                    <td>{{$ticket->ct_loaned}}</td>
                    <td>{{$ticket->new_ct_return}}</td>
                    <td>
                        @switch($ticket->status)
                            @case('1')
                                Requested
                                @break

                            @case('2')
                                Borrowed, not returned yet
                                @break

                            @case('3')
                                Returned
                                @break

                            @case('4')
                                Returned
                                @break

                            @default
                                Unknown
                        @endswitch

                    </td>
                    <td>{{$ticket->start_date}}</td>
                    <td>{{$ticket->end_date}}</td>
                    
                </tr>
                @endforeach
            </table>
        </div>

        <div class="thermal-event-ticket-content">
            <x-common-ticket-detail-form>
                <h2>Asset Details</h2>
                <li>
                    <lable>Asset Tag</lable>
                    <h2>{{$item_details->asset_tag}}</h2>
                    
                </li>

                <li>
                    <lable>Model</lable>
                    <h2>{{$item_details->model}}</h2>
                    
                </li>

                <li>
                    <lable>Serial Number</lable>
                    <h2>{{$item_details->serial_number}}</h2>
                    
                </li>

                <li>
                    <lable>Product Number</lable>
                    <h2>{{$item_details->product_number}}</h2>
                    
                </li>

                <li>
                    <lable>Category</lable>
                    <h2>
                        <span class="ticket-status {{ $item_details->category_data['class'] }}">
                            {{ $item_details->category_data['text'] }}
                        </span>

                    </h2>
                        
                </li>

                <li>
                    <lable>Asset Type</lable>
                    {{-- <h2>
                        <span class="ticket-status {{ $item_details->asset_type['class'] }}">
                            {{ $item_details->asset_type['text'] }}
                        </span>
                    </h2> --}}
                        
                </li>

                <li>
                    <lable>Warehouse</lable>
                    <h2>
                        <span class="ticket-status {{ $item_details->warehouse_data['class'] }}">
                            {{ $item_details->warehouse_data['text'] }}
                        </span>
                    </h2>
                    
                    
                </li>

                <li>
                    <lable>Avaialble Status</lable>
                    <h2>
                        <span class="ticket-status {{ $item_details->available_status_data['class'] }}">
                            {{ $item_details->available_status_data['text'] }}
                        </span>
                    </h2>
                    
                    
                </li>

                <li>
                    <lable>Condition Status</lable>
                    <h2>
                        <span class="ticket-status {{ $item_details->condition_data['class'] }}">
                            {{ $item_details->condition_data['text'] }}
                        </span>
                    </h2>
                    
                    
                </li>



                <li>
                    <lable>Attachments ({{ $item_details->active_attachments->count() }} files)</lable>
                    <x-common-attachments-table>
                            @foreach ($item_details->active_attachments as $attachment)
                                
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

                
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                
            </x-common-ticket-detail-form>

            {{-- <x-common-ticket-comments action1="{{ route('add-comment-thermal-event-ticket', $ticket->id) }}" id="add-comment-form">
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

        </div>
    </body>

</html>