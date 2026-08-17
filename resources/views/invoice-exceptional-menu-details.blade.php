<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/invoice-exceptional.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Invoice Exceptional Menu">
            <li>
                <a href="{{ url('/invoice-exceptional-menu') }}" class="button">
                    <button><i class="ti-home"></i>
                    Home
                    </button>
                </a>
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
                <a href="{{ url('/main-menu') }}" class="button">
                    <button><i class="ti-layout-grid2"></i>
                    Quick Navigation
                    </button>
                </a>
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

                        <li>
                            <button type="button" class="js-input-required-btn" data-target="request-sale-support-invoice-exceptional-ticket"><i class="ti-angle-double-right"></i> Request Sale Support</button>
                            
                        </li>


                    @endif
                @break

                
                
            @endswitch
        </x-common-header>


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
                            'icon' => 'ti-calendar',
                            'label' => 'Ngày request',
                            'value' => $ticket->created_at,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Support Type',
                            'value' => match ($ticket->support_type) {
                                '1' => 'Hóa đơn xuất sau (1 máy)',
                                '2' => 'Hóa đơn xuất sau (Nhiều máy)',
                                '3' => 'Kích hoạt bảo hành (1 máy)',
                                '4' => 'Kích hoạt bảo hành (Nhiều máy)',
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
                                '2' => 'Waiting approve invoice',
                                '3' => 'Waiting re-activate warranty',
                                '4' => 'Completed',
                                '5' => 'Rejected',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'info',
                                '4' => 'success',
                                '5' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Invoice Number',
                            'value' => $ticket->invoice_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Serial Number',
                            'value' => $ticket->serial_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Product Number',
                            'value' => $ticket->product_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Product Model',
                            'value' => $ticket->product_model,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Expired Date',
                            'value' => $ticket->expired_date,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Invoice Date',
                            'value' => $ticket->invoice_date,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Description',
                            'value' => $ticket->description,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Retail Name',
                            'value' => $ticket->retail_name,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-angle-double-right',
                            'label' => 'Company/Customer Name',
                            'value' => $ticket->company_customer_name,
                            'type' => 'text'
                        ],
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
                    :actionRoute="route('add-comment-invoice-exceptional-ticket', $ticket->id)"
                >
                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket->ticket_tracking_info"
                />
            </div>

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

        
        <x-common-ticket-form title="Request Sale Support" id="request-sale-support-invoice-exceptional-ticket" action1="{{ route('request-sale-support-invoice-exceptional-ticket', $ticket->id) }}">
            @method('POST')
            <label>Email team sale cần request hỗ trợ</label>
            <input type="text" class="ticket-form-body-input" name="email_request" value="" required placeholder="Nếu có nhiều email, mỗi email hãy cách nhau bằng dấu ;">

            <label>Hạn chót trả lời</label>
            <input type="datetime-local" class="ticket-form-body-input" name="deadline_date" value="" required>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" >Request sale support</button> 
            </x-slot:footer>
        </x-common-ticket-form>

    </body>
</html>