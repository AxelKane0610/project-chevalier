<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/loan-unit-part.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Loan Units & Parts Ticket Details">
            <li>
                <a href="{{ url('/loan-unit-part-menu') }}" class="button">
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
            @if( ($ticket->status == '1' || $ticket->status == '2') && $ticket->user_id == auth()->user()->id)
                <li>
                    <button type="button" class="js-input-required-btn" data-target="add-loan-unit-part"><i class="ti-plus"></i> Add part</button>
                    
                </li>
            @endif

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')) && ($ticket->status == '1' || $ticket->status == '2'))
                <li>
                    <button type="button" class="js-input-required-btn" data-target="close-loan-unit-part-ticket" ><i class="ti-check"></i>Close Ticket</button>
                    
                </li>
            
            @endif


        </x-common-header>

        <div class="common-table-container">
            <table class="common-table" width="100%">
                <th width="50px"></th>
                <th width="50px">Part Request</th>
                <th width="50px">Status</th>
                <th width="50px">Loan Unit Asset Tag</th> 
                <th width="50px">Loan Unit Serial Number</th> 
                <th width="50px">CT Loaned</th>
                <th width="50px">New CT Return</th>
                <th width="50px">Original</th>
                <th width="50px">Start Date</th>
                <th width="50px">End Date</th>
                <th width="50px">Note</th>


                @foreach($ticket->parts_details as $parts)
                <tr>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-row">
                            @if(($parts->status == '1' && $ticket->user_id == auth()->user()->id && ($ticket->status == '1' || $ticket->status == '2')) || (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')))
                                
                                <button type="button" 
                                class="btn-edit-part js-input-required-btn"
                                data-id="{{ $parts->id }}"
                                data-receipt = "{{$parts->ticket_receipt}}"
                                data-status ="{{ $parts->status }}"
                                data-part_request="{{ $parts->part_request }}"
                                data-asset_tag="{{ $parts->loan_unit_asset_tag }}"
                                data-serial_number="{{ $parts->loan_unit_serial_number }}"
                                data-ct_loaned="{{ $parts->ct_loaned }}"
                                data-new_ct_return="{{ $parts->new_ct_return }}"
                                data-original="{{ $parts->original }}"
                                data-start_date="{{ $parts->start_date }}"
                                data-end_date="{{ $parts->end_date }}"
                                data-note="{{ $parts->note }}"
                                data-target="edit-loan-unit-part-details"
                                ><i class="ti-pencil"></i></button>
                                
                            @endif

                            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')) && $parts->status == '1' && ($ticket->status == '1' || $ticket->status == '2'))
                                
                                <button type="button" class="issue-loan-unit-part-btn js-input-required-btn" data-target="issue-loan-unit-part" data-id="{{ $parts->id }}"><i class="ti-hand-point-right"></i></button>
                                
                            @endif

                            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')) && $parts->status == '2' && ($ticket->status == '1' || $ticket->status == '2'))
                                
                                <button type="button" class="return-loan-unit-part-btn js-input-required-btn" data-target="return-loan-unit-part" data-id="{{ $parts->id }}"><i class="ti-check"></i></button>
                                
                            @endif

                            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')) && ($parts->status == '1'))
                                <form class="js-input-required-btn" data-target="cancel-loan-unit-part" id="cancel-loan-unit-part" method="PATCH" action="{{ route('cancel-loan-unit-part', $parts->id ) }}">
                                    <button type="submit" class="cancel-loan-unit-part-btn" ><i class="ti-close"></i></button>
                                </form>
                            @endif
                        </div>

                        
                        

                        
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
                    <td>
                        <span class="ticket-status {{ $parts->original_data['class'] }}">
                            {{ $parts->original_data['text'] }}
                        </span>
                    </td>
                    
                    <td>{{$parts->start_date}}</td>
                    <td>{{$parts->end_date}}</td>
                    <td>{{$parts->note}}</td>


                </tr>
                @endforeach
            </table>
        </div>


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
                                'icon' => 'ti-arrow-circle-right',
                                'label' => 'Status',
                                'value' => match ($ticket->status) {
                                    '1' => 'Open',
                                    '2' => 'In progress',
                                    '3' => 'Completed',
                                    '4' => 'Canceled',
                                    default => 'Unknown',
                                },
                                'type' => 'badge',
                                'color' => match ($ticket->status) {
                                    '1' => 'primary',
                                    '2' => 'secondary',
                                    '3' => 'success',
                                    '4' => 'info',
                                    default => 'Unknown',
                                },
                            ],
                            [
                                'icon' => 'ti-align-justify',
                                'label' => 'Customer Unit Info',
                                'value' => $ticket->customer_unit_info,
                                'type' => 'text'
                            ],
                            [
                                'icon' => 'ti-calendar',
                                'label' => 'Ngày request',
                                'value' => $ticket->created_at,
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
                        :actionRoute="route('add-comment-loan-unit-part-ticket', $ticket->id)"
                    >


                    </x-common-ticket-comments-card>

                    <!-- ================= Timeline ================= -->

                    <x-common-ticket-tracking-info
                        :trackings="$ticket->ticket_tracking_info"
                    />
            </div>
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
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>
            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" >Save</button> 
            </x-slot:footer>
        </x-common-ticket-form>

        <x-common-ticket-form title="Edit Loan Unit & Part Details" id="edit-loan-unit-part-details" action1="">
            @method('PATCH')

            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" name="part_request" value="" id="edit-part-request-part-details">

            

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')))
                <label>Status</label>
                <select id="edit-status" class="ticket-form-body-input" name="status">
                    <option value="1" >Requested</option>
                    <option value="2" >Borrowed, not returned yet</option>
                    <option value="3" >Returned</option>
                    <option value="4" >Canceled</option>
                    <option value="5" >Will not be returned</option>
                    
                </select>
                
                <label>Loan Unit Asset Tag</label>
                <input type="text" class="ticket-form-body-input" name="loan_unit_asset_tag" value="" id="edit-loan-unit-asset-tag">

                <label>Loan Unit Serial Number</label>
                <input type="text" class="ticket-form-body-input" name="loan_unit_serial_number" value="" id="edit-loan-unit-serial-number">

                <label>CT Loaned</label>
                <input type="text" class="ticket-form-body-input" name="ct_loaned" value="" id="edit-ct-loaned">

                <label>New CT Return</label>
                <input type="text" class="ticket-form-body-input" name="new_ct_return" value="" id="edit-new-ct-return">

                <label>Original</label>
                <select name="original" class="ticket-form-body-input" id="edit-original">
                    <option value="1">Crown</option>
                    <option value="2">Spectre</option>
                    <option value="3">T1 (FPT, DGW, Elite)</option>
                    
                </select>

                <label>Start Date</label>
                <input type="date" class="ticket-form-body-input" name="start_date" value="" id="edit-start-date">

                <label>End Date</label>
                <input type="date" class="ticket-form-body-input" name="end_date" value="" id="edit-end-date">

                <label>Note</label>
                <input type="text" class="ticket-form-body-input" name="note" value="" id="edit-note">

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Edit</button> 
                </x-slot:footer>

            @endif

            
        </x-common-ticket-form>

        <x-common-ticket-form title="Issue Loan Unit & Part" id="issue-loan-unit-part" action1="">
            @method('PATCH')

            <label>Loan Unit Asset Tag</label>
            <input type="text" class="ticket-form-body-input" name="loan_unit_asset_tag" placeholder="Điền vào nếu máy/part cho mượn là từ kho Spectre/Crown" value="" >

            <label>Loan Unit Serial Number</label>
            <input type="text" class="ticket-form-body-input" name="loan_unit_serial_number" placeholder="Serial Number của máy cho mượn, không có để N/A" value="" required>

            <label>CT Loaned</label>
            <input type="text" class="ticket-form-body-input" name="ct_loaned" placeholder="CT của linh kiện cho mượn, không có điền N/A" required>

            <label>Original</label>
            <select name="original" class="ticket-form-body-input" id="edit-original" required>
                <option value="1" >Crown</option>
                <option value="2" >Spectre</option>
                <option value="3" >T1 (FPT, DGW, Elite)</option>
            </select>

            <label>Start Date</label>
            <input type="date" class="ticket-form-body-input" name="start_date" value="{{ today()->format('Y-m-d') }}" id="edit-start-date" >

            <label>Note</label>
            <input type="text" class="ticket-form-body-input" name="note" placeholder="Ghi chú nếu có">

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Issue unit/part</button> 
            </x-slot:footer>
        </x-common-ticket-form>

        <x-common-ticket-form title="Add Loan Unit & Part" id="add-loan-unit-part" action1="{{ route('add-loan-unit-part', $ticket->id) }}">
            @method('POST')

            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" name="part_request" placeholder="Điền mã part & tên part muốn mượn thêm" required>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Add unit/part</button> 
            </x-slot:footer>
        </x-common-ticket-form>

        <x-common-ticket-form title="Confirm Unit/Part Return" id="return-loan-unit-part" action1="">
            @method('PATCH')

            <label>New CT Return</label>
            <input type="text" class="ticket-form-body-input" name="new_ct_return" value="" placeholder="Điền CT của linh kiện trả về, không có để N/A" required>

            <label>End Date</label>
            <input type="date" class="ticket-form-body-input" name="end_date" value="{{ today()->format('Y-m-d') }}"  required>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Return unit/part</button> 
            </x-slot:footer>
        </x-common-ticket-form>

        <x-common-ticket-form title="Close ticket" id="close-loan-unit-part-ticket" action1="{{ route('close-loan-unit-part-ticket', $ticket->id) }}">
            @method('PATCH')
            <label>Status (Chọn Completed nếu có cho mượn part)</label>
            <select name="status" class="ticket-form-body-input" required>
                <option value="3">Completed</option>
                <option value="4">Canceled</option>
            </select>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Close ticket</button> 
            </x-slot:footer>
        </x-common-ticket-form>

    
    </body>

</html>