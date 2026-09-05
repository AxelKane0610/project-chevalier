<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/spectre-crown-warehouse.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Spectre - Crown Item Details">
            <li>
                
                <a href="{{ url('/spectre-crown-warehouse-menu') }}" class="button">
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

            @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN'))
                <li>
                    <button
                        type="button"
                        class="js-input-required-btn"
                        data-target="asset-export"
                    >
                        <i class="ti-angle-double-right"></i>
                        Export
                    </button>
                </li>
            @endif
        </x-common-header>

        <div class="common-table-container">
            <div class="table-responsive">
                <table class="common-table">
                    <thead>
                        <tr>
                            <th style="width: 7%"></th>
                            <th style="width: 10%">User Owner</th>
                            <th style="width: 10%">Receipt</th>
                            <th style="width: 10%">Part Request</th>
                            <th style="width: 10%">CT Loaned</th>
                            <th style="width: 12%">New CT Return</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 7%">Start Date</th>
                            <th style="width: 7%">End Date</th>
                            <th style="width: 19%">Note</th>
                        </tr>
                    </thead>

                    <tbody>

                    
                        @foreach($item_details->loan_unit_part_tickets as $ticket)
                        <tr>
                            
                            <td class="action-cell">
                                <div class="action-buttons">

                                    <button type="button"
                                        class="btn-edit-asset-export js-input-required-btn"
                                        data-id = "{{ $ticket->id }}"
                                        data-target="edit-asset-export"
                                        data-asset-tag="{{ $ticket->loan_unit_asset_tag }}"
                                        data-user-id="{{ $ticket->user_id }}"
                                        data-ticket-receipt="{{ $ticket->ticket_receipt }}"
                                        data-part-request="{{ $ticket->part_request }}"
                                        data-ct-loaned="{{ $ticket->ct_loaned }}"
                                        data-new-ct-return="{{ $ticket->new_ct_return }}"
                                        data-status="{{ $ticket->status }}"
                                        data-original="{{ $ticket->original }}"
                                        data-start-date="{{ $ticket->start_date }}"
                                        data-end-date="{{ $ticket->end_date }}"
                                        data-note="{{ $ticket->note }}"
                                    >
                                        <i class="ti-pencil"></i>
                                    </button>

                                    @if($ticket->ticket_id != null)
                                        <a href="/loan-unit-part-ticket-details/{{ $ticket->ticket_id }}">
                                            <button><i class="ti-arrow-right"></i></button>
                                        </a>
                                    @endif

                                </div>
                            </td>
                            
                            <td>{{$ticket->user_owner->fullname ?? 'N/A'}}</td>
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
                            <td>{{$ticket->note}}</td>

                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container-fluid px-4 py-4">
            <div class="row g-4" style="min-height: calc(100vh - 90px);">

                <!-- ================= Ticket Detail ================= -->
                <x-common-ticket-details-card 
                    :rows="[
                        [
                            'icon' => 'ti-tag',
                            'label' => 'Asset Tag',
                            'value' => $item_details->asset_tag,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Model',
                            'value' => $item_details->model,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Serial Number',
                            'value' => $item_details->serial_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Box Serial Number',
                            'value' => $item_details->box_serial_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Product Number',
                            'value' => $item_details->product_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Category',
                            'value' => match ($item_details->category) {
                                '1' => 'Laptop',
                                '2' => 'Accessory (Chuột, phím,...)',
                                '3' => 'Màn hình',
                                '4' => 'Máy scan',
                                '5' => 'PC (Máy tính để bàn)',
                                '6' => 'Máy in khổ lớn',
                                '7' => 'Máy in khổ nhỏ',
                                '8' => 'Others',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->category) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'warning',
                                '4' => 'success',
                                '5' => 'danger',
                                '6' => 'dark',
                                '7' => 'info',
                                '8' => 'info',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Asset Type',
                            'value' => match ($item_details->asset_type) {
                                '1' => 'BUFFER',
                                '2' => 'CRT Unit',
                                '3' => 'DASS Unit',
                                '4' => 'DEMO Unit',
                                '5' => 'DOA',
                                '6' => 'Support Unit',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->asset_type) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'warning',
                                '4' => 'success',
                                '5' => 'danger',
                                '6' => 'dark',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Warehouse',
                            'value' => match ($item_details->warehouse) {
                                '1' => 'SPECTRE',
                                '2' => 'CROWN HCM',
                                '3' => 'CROWN HN',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->warehouse) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'warning',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Import Date',
                            'value' => $item_details->import_date,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Available Status',
                            'value' => match ($item_details->available_status) {
                                '1' => 'Available',
                                '2' => 'Not available',
                                '3' => 'In use',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->available_status) {
                                '1' => 'success',
                                '2' => 'secondary',
                                '3' => 'warning',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Condition',
                            'value' => match ($item_details->condition) {
                                '1' => 'Good working',
                                '2' => 'Chưa test',
                                '3' => 'Cant use',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->condition) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Note',
                            'value' => $item_details->note,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Quantity',
                            'value' => $item_details->quantity,
                            'type' => 'text'
                        ],
                        
                        
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$item_details->active_attachments"
                    /> 

                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SPECTRE_CROWN_WAREHOUSE_ADMIN'))
                        <x-slot:footer>
                            <button type="button" class="js-input-required-btn" data-target="edit-asset-details"><i class="ti-pencil"></i> Edit</button>
                            
                        </x-slot:footer>
                    @endif
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$item_details->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-spectre-crown-warehouse', $item_details->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$item_details->ticket_tracking_info"
                />
            </div>
        </div>

        <x-common-ticket-form title="Edit Asset Details" id="edit-asset-details" action1="{{ route('edit-asset-details', $item_details->id) }}" method="POST" enctype="multipart/form-data">
            @method('PATCH')
            <label>Asset Tag</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Asset Tag" name="asset_tag" value="{{$item_details->asset_tag}}" required>

            <label>Model</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Model" name="model" value="{{$item_details->model}}" required>

            <label>Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Serial Number" name="serial_number" value="{{$item_details->serial_number}}" required>

            <label>Box Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Box Serial Number" name="box_serial_number" value="{{$item_details->box_serial_number}}" required>

            <label>Product Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Product Number" name="product_number" value="{{$item_details->product_number}}" required>

            <label>Category</label>
            <select name="category" class="ticket-form-body-input">
                <option value="1" @selected($item_details->category == 1)>Laptop</option>
                <option value="2" @selected($item_details->category == 2)>Accessory (Chuột, phím,...)</option>
                <option value="3" @selected($item_details->category == 3)>Màn hình</option>
                <option value="4" @selected($item_details->category == 4)>Máy scan</option>
                <option value="5" @selected($item_details->category == 5)>PC (Máy tính để bàn)</option>
                <option value="6" @selected($item_details->category == 6)>Máy in khổ lớn</option>
                <option value="7" @selected($item_details->category == 7)>Máy in khổ nhỏ</option>
                <option value="8" @selected($item_details->category == 8)>Others</option>
                
            </select>

            <label>Asset Type</label>
            <select name="asset_type" class="ticket-form-body-input">
                <option value="1" @selected($item_details->asset_type == 1)>BUFFER</option>
                <option value="2" @selected($item_details->asset_type == 2)>CRT Unit</option>
                <option value="3" @selected($item_details->asset_type == 3)>DASS Unit</option>
                <option value="4" @selected($item_details->asset_type == 4)>DEMO Unit</option>
                <option value="5" @selected($item_details->asset_type == 5)>DOA</option>
                <option value="6" @selected($item_details->asset_type == 6)>Support Unit</option>
                
            </select>

            <label>Warehouse</label>
            <select name="warehouse" class="ticket-form-body-input">
                <option value="1" @selected($item_details->warehouse == 1)>Spectre</option>
                <option value="2" @selected($item_details->warehouse == 2)>CROWN HCM</option>
                <option value="3" @selected($item_details->warehouse == 3)>CROWN HN</option>
                
            </select>

            <label>Available Status</label>
            <select name="available_status" class="ticket-form-body-input">
                <option value="1" @selected($item_details->available_status == 1)>Available</option>
                <option value="2" @selected($item_details->available_status == 2)>Not available</option>
                <option value="3" @selected($item_details->available_status == 3)>In use</option>
                
            </select>

            <label>Condition</label>
            <select name="condition" class="ticket-form-body-input">
                <option value="1" @selected($item_details->condition == 1)>Good working</option>
                <option value="2" @selected($item_details->condition == 2)>Chưa test</option>
                <option value="3" @selected($item_details->condition == 3)>Can't use</option>
                
            </select>

            <label>Import Date</label>
            <input type="date" class="ticket-form-body-input" placeholder="Ngày nhập kho" name="import_date" value="{{$item_details->import_date}}" >

            <label>Note</label>
            <input type="text" class="ticket-form-body-input" placeholder="Note" name="note" value="{{$item_details->note}}" >

            <label>Quantity</label>
            <input type="number" class="ticket-form-body-input" placeholder="Nhập số lượng" name="quantity" value="{{$item_details->quantity}}" required>
            
            <label><b>Attachments</b></label>
            
            @if($item_details->active_attachments->count() > 0) 
                <x-common-attachments-table>
                    @foreach($item_details->active_attachments as $attachment)
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
                <button class="ticket-form-body-input" type="submit">Edit</button> 
            </x-slot:footer>
        </x-common-ticket-form>

        <x-common-ticket-form title="Asset Export" id="asset-export" action1="{{ route('asset-export', $item_details->id) }}" method="POST" enctype="multipart/form-data">
            @method('POST')
            <label>Asset Tag</label>
            <input type="text" class="ticket-form-body-input" name="loan_unit_asset_tag" value="{{$item_details->asset_tag}}" readonly>

            <label>User Owner (Nếu user không thuộc SC, để trống)</label>
            <livewire:common-search-dropdown
                model-class="App\Models\User"
                :search-fields="['fullname']"
                display-field="fullname"
                value-field="id"
                name="user_id"
            />

            <label>Receipt (Nếu không có để N/A)</label>
            <input type="text" class="ticket-form-body-input" name="ticket_receipt" placeholder="" required>

            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" name="part_request" placeholder="" required>

            <label>CT Loaned</label>
            <input type="text" class="ticket-form-body-input" name="ct_loaned" placeholder="Điền CT của part cho mượn (Nếu có)">

            <label>Status</label>
            <select name="status" class="ticket-form-body-input" required>
                <option value="2" >Borrowed, not returned yet</option>
                <option value="3" >Returned</option>
                <option value="4" >Canceled</option>
                <option value="5" >Will not be returned</option>
                
            </select>

            <label>Original</label>
            <select name="original" class="ticket-form-body-input">
                <option value="1" @selected($item_details->original == 1)>Crown</option>
                <option value="2" @selected($item_details->original == 2)>Spectre</option>
                <option value="3" @selected($item_details->original == 3)>T1 (FPT, DGW, Elite)</option>
                
            </select>

            <label>Start Date</label>
            <input type="date" class="ticket-form-body-input" name="start_date" value="{{ today()->format('Y-m-d') }}">

            <label>Note</label>
            <input type="text" class="ticket-form-body-input" name="note" placeholder="Ghi chú">

            

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Export</button> 
            </x-slot:footer>

        </x-common-ticket-form>

        <x-common-ticket-form title="Edit Asset Export" id="edit-asset-export" action1="" enctype="multipart/form-data">
            @method('PATCH')
            <label>Asset Tag</label>
            <input type="text" class="ticket-form-body-input" id="edit-loan-unit-asset-tag" name="loan_unit_asset_tag" readonly>

            <label>User Owner (Nếu user không thuộc SC, để trống)</label>
            <livewire:common-search-dropdown
                model-class="App\Models\User"
                :search-fields="['fullname']"
                display-field="fullname"
                value-field="id"
                name="user_id"
            />

            <label>Receipt (Nếu không có để N/A)</label>
            <input type="text" class="ticket-form-body-input" id="edit-ticket-receipt" placeholder="Điền số phiếu nếu có" name="ticket_receipt">

            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" id="edit-part-request" placeholder="" name="part_request" required>

            <label>CT Loaned</label>
            <input type="text" class="ticket-form-body-input" id="edit-ct-loaned" placeholder="Điền CT của part cho mượn (Nếu có)" name="ct_loaned">

            <label>New CT Returned</label>
            <input type="text" class="ticket-form-body-input" id="edit-new-ct-return" placeholder="Điền CT của part cho trả (Nếu có)" name="new_ct_return">

            <label>Status</label>
            <select id="edit-status" class="ticket-form-body-input" name="status">
                <option value="1" >Requested</option>
                <option value="2" >Borrowed, not returned yet</option>
                <option value="3" >Returned</option>
                <option value="4" >Canceled</option>
                <option value="5" >Will not be returned</option>
                
            </select>

            <label>Original</label>
            <select id="edit-original" class="ticket-form-body-input" name="original">
                <option value="1" >Crown</option>
                <option value="2" >Spectre</option>
                <option value="3" >T1 (FPT, DGW, Elite)</option>
                
            </select>

            <label>Start Date</label>
            <input type="date" class="ticket-form-body-input" id="edit-start-date" name="start_date">

            <label>End Date</label>
            <input type="date" class="ticket-form-body-input" id="edit-end-date" name="end_date">

            <label>Note</label>
            <input type="text" class="ticket-form-body-input" id="edit-note" placeholder="Ghi chú" name="note">

            

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Edit</button> 
            </x-slot:footer>

        </x-common-ticket-form>


        
    </body>

</html>