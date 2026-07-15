<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/ttex.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        

        <x-common-header title="TTEX Ticket Details">
            <li>
                <form action="/ttex-tickets-menu">
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

            @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) && $ticket->status == '1')
                
                <li>
                    <form class="js-input-required-btn" data-target="close-ttex-ticket">
                        <button type="button"><i class="ti-check"></i> Close ticket</button>
                    </form>
                </li>

            @endif
                
            </x-common-header>

        
        <div class="ttex-ticket-content">
            <x-common-ticket-detail-form>
                <h2>Ticket Details</h2>
                <li>
                    <lable>TTEX Bill</lable>
                    <h2>{{ $ticket->ttex_bill }}</h2>
                    
                </li>

                <li>
                    <lable>Category</lable>
                    <h2>
                        <span class="ticket-status {{ $ticket->category_data['class'] }}">
                            {{ $ticket->category_data['text'] }}
                        </span>
                    </h2>
                    
                </li>

                <li>
                    <lable>Shipment Type</lable>
                    <h2>
                        <span class="ticket-status {{ $ticket->shipment_type_data['class'] }}">
                            {{ $ticket->shipment_type_data['text'] }}
                        </span>
                    </h2>
                        
                </li>

                <li>
                    <lable>Part Status</lable>
                    <h2>
                        <span class="ticket-status {{ $ticket->part_status_data['class'] }}">
                            {{ $ticket->part_status_data['text'] }}
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
                    <lable>Hạn trả def cho kho</lable>
                    <h2>{{ $ticket->part_return_deadline }}</h2>
                    
                </li>

                <li>
                    <lable>Thông tin người gửi</lable>
                    <h2>{{ $ticket->sender_info }}</h2>
                    
                </li>

                <li>
                    <lable>Thông tin người nhận</lable>
                    <h2>{{ $ticket->receiver_info }}</h2>
                    
                </li>

                <li>
                    <lable>Mô tả hàng hóa</lable>
                    <h2>{{ $ticket->shipment_description }}</h2>
                    
                    
                    
                </li>

                <li>
                    <lable>Note</lable>
                    <h2>{{ $ticket->note }}</h2>
                    
                </li>

                <li>
                    <lable>Ngày tạo ticket</lable>
                    <h2>{{ $ticket->created_at }}</h2>
                    
                </li>

                
                <li style="height: auto;">
                    <lable>Ngày điều tin</lable>
                    
                    <h2>{{ $ticket->booking_date }}</h2>
                    
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

                @if(($ticket->status == '1') && $ticket->user_id == auth()->user()->id)
                <x-slot:footer>
                    <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                </x-slot:footer>
                @endif
            </x-common-ticket-detail-form>

            <x-common-ticket-comments action1="{{ route('add-comment-ttex-ticket', $ticket->id) }}" id="add-comment-form">
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

            <x-common-ticket-form title="Edit TTEX Ticket" id="edit-ticket-details" action1="{{ route('edit-ttex-ticket', $ticket->id) }}">
                @method('PATCH')

                <label>Category</label>
                <select name="category" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->category == '1')>ASRC</option>
                    <option value="2" @selected($ticket->category == '2')>HPS</option>
                    <option value="3" @selected($ticket->category == '3')>Onsite Geox</option>
                    <option value="4" @selected($ticket->category == '4')>Part NBD</option>
                    <option value="5" @selected($ticket->category == '5')>Others</option>
                    <option value="6" @selected($ticket->category == '6')>Văn phòng phẩm/Tài liệu</option>

                    
                </select>

                <label>Shipment Type</label>
                <select name="shipment_type" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->shipment_type == '1')>Tài liệu</option>
                    <option value="2" @selected($ticket->shipment_type == '2')>Thiết bị điện/điện tử</option>
                    <option value="3" @selected($ticket->shipment_type == '3')>Văn phòng phẩm</option>

                    
                </select>

                <label>Part Status</label>
                <select name="part_status" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->part_status == '1')>Good part</option>
                    <option value="2" @selected($ticket->part_status == '2')>Def part</option>
                    <option value="3" @selected($ticket->part_status == '3')>Good part - Unused</option>

                    
                </select>

                <label>Hạn trả def cho kho (Điền vào nếu đổi từ Good part sang Def hoặc Unused)</label>
                <input type="date" class="ticket-form-body-input" name="part_return_deadline" value="{{ $ticket->part_return_deadline}}">

                <label>Thông tin người gửi</label>
                <input type="text" class="ticket-form-body-input" name="sender_info" value="{{ $ticket->sender_info }}">

                <label>Thông tin người nhận</label>
                <input type="text" class="ticket-form-body-input" name="receiver_info" value="{{ $ticket->receiver_info }}">

                <label>Mô tả hàng hóa</label>
                <input type="text" class="ticket-form-body-input multiple-row" name="shipment_description" value="{{ $ticket->shipment_description }}">

                <label>Note</label>
                <input type="text" class="ticket-form-body-input" name="note" value="{{ $ticket->note }}">

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

            <x-common-ticket-form title="Close TTEX Ticket" id="close-ttex-ticket" action1="{{ route('close-ttex-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Status</label>
                <select name="status" class="ticket-form-body-input" required>
                    <option value="2">Completed - Đã điều tin</option>
                    <option value="3">Rejected</option>
                </select>

                <label>Bill TTEX</label>
                <input type="text" class="ticket-form-body-input" name="ttex_bill" value="" placeholder="Điền vào nếu complete">

                <label>Comment</label>
                <input type="text" class="ticket-form-body-input" name="comment" value="" placeholder="Comment cho người tạo ticket nếu có">

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" >Close ticket</button> 
                </x-slot:footer>

            </x-common-ticket-form>

        </div>

    </body>

</html>