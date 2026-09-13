<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/hps-warehouse.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="HPS Item Details">
            <li>
                
                <a href="{{ url('/hps-warehouse-menu') }}" class="button">
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

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN') || auth()->user()->site_id == $item_details->current_site) && ($item_details->current_status == 1))
                <li>
                    <button
                        type="button"
                        class="js-input-required-btn"
                        data-target="asset-export"
                    >
                        <i class="ti-angle-double-right"></i>
                        Xuất máy
                    </button>
                </li>
            @endif

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN') || auth()->user()->site_id == $item_details->current_site) && ($item_details->current_status == 3))
                <li>
                    <button
                        type="button"
                        class="js-input-required-btn"
                        data-target="asset-re-import"
                    >
                        <i class="ti-back-left"></i>
                        Nhập lại máy
                    </button>
                </li>
            @endif
        </x-common-header>

        <div class="common-table-container " style="zoom: 0.85;">
            <div class="table-responsive">
                <table class="common-table">
                    <thead>
                        <tr>
                            <th style="width: 7%"></th>
                            <th style="width: 10%">HPS Receipt</th>
                            <th style="width: 10%">CE Owner</th>
                            <th style="width: 10%">Program Support</th>
                            <th style="width: 10%">Export Serial Number</th>
                            <th style="width: 15%">Export Model</th>
                            <th style="width: 10%">Export Site</th>
                            <th style="width: 10%">Người xuất máy</th>
                            <th style="width: 10%">SN nhập lại</th>
                            <th style="width: 15%">Model nhập lại</th>
                            <th style="width: 10%">Site nhập lại</th>
                            <th style="width: 10%">Người thu hồi</th>
                            <th style="width: 10%">Trạng thái máy nhập lại</th>



                        </tr>
                    </thead>

                    <tbody>

                    
                        @foreach($item_details->export_details as $details)
                            
                            <tr>
                                <td>
                                    <i class="ti-angle-double-right"></i>
                                </td>
                                <td>{{ $details->hps_receipt }}</td>
                                <td>{{ $details->ceOwner?->fullname ?? '' }}</td>
                                <td>{{ match ( $details->program_support) {
                                    '1' => 'FB30',
                                    '2' => 'Handover',
                                    '3' => 'Luân chuyển nội bộ',
                                    '4' => 'Sàn số serial',
                                    '5' => 'WUE',
                                    '6' => 'Xuất mượn',

                                    default => '',
                                } }}</td>
                                <td>{{ $details->export_serial_number }}</td>
                                <td>{{ $details->export_model }}</td>
                                <td>
                                    {{ match ( $details->export_site) {
                                    '1' => 'HCM',
                                    '2' => 'HN',
                                    '3' => 'DN',
                                    '4' => 'CT',
                                    default => '',
                                } }}
                                </td>
                                <td>{{ $details->user_owner_export->fullname ?? '' }}</td>
                                <td>{{ $details->re_import_serial_number }}</td>
                                <td>{{ $details->re_import_model }}</td>
                                <td>{{ match ( $details->re_import_site) {
                                    '1' => 'HCM',
                                    '2' => 'HN',
                                    '3' => 'DN',
                                    '4' => 'CT',
                                    default => '',
                                } }}</td>
                                <td>{{ $details->user_owner_re_import->fullname ?? '' }}</td>

                                <td>{{ match ($details->re_import_status) {
                                    '1' => 'New',
                                    '2' => 'Good',
                                    '3' => 'DOA',
                                    '4' => 'Not good',
                                    '5' => 'Scrap',

                                    default => '',
                                } }}</td>

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
                            'icon' => 'ti-user',
                            'label' => 'Người nhập kho',
                            'value' => $item_details->user_owner->fullname ?? 'Unknown',
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
                            'icon' => 'ti-align-justify',
                            'label' => 'Model',
                            'value' => $item_details->model,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Ngày nhập kho',
                            'value' => $item_details->import_date,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Import Source',
                            'value' => match ($item_details->import_source) {
                                '1' => 'FPT',
                                '2' => 'DGW',
                                '3' => 'SRFR',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->import_source) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'PO Number',
                            'value' => $item_details->po_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'PO Type',
                            'value' => match ($item_details->po_type) {
                                '1' => 'E-Claim',
                                '2' => 'PO Remaining',
                                '3' => 'Forecast',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->warehouse) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Invoice',
                            'value' => $item_details->invoice,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Price',
                            'value' => $item_details->price,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Import Status',
                            'value' => match ($item_details->import_status) {
                                '1' => 'New FB',
                                '2' => 'RFB',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->import_status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Site',
                            'value' => match ($item_details->site) {
                                '1' => 'HCM',
                                '2' => 'HN',
                                '3' => 'DN',
                                '4' => 'CT',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->site) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-settings',
                            'label' => 'Location',
                            'value' => $item_details->location,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Current Status',
                            'value' => match ($item_details->current_status) {
                                '1' => 'Đang lưu kho',
                                '2' => 'Đã hủy',
                                '3' => 'Đã xuất máy, chờ thu hồi',
                                '4' => 'Chờ hủy',
                                '5' => 'Handover',

                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->current_status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                '5' => 'warning',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Current HPS Receipt',
                            'value' => $item_details->current_hps_receipt,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Current Serial Number',
                            'value' => $item_details->current_serial_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Current Box Serial Number',
                            'value' => $item_details->current_box_serial_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Current Product Number',
                            'value' => $item_details->current_product_number,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Current Site',
                            'value' => match ($item_details->current_site) {
                                '1' => 'HCM',
                                '2' => 'HN',
                                '3' => 'DN',
                                '4' => 'CT',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->current_site) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Current Location',
                            'value' => $item_details->current_location,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Tình trạng máy',
                            'value' => match ($item_details->unit_re_import_status) {
                                '1' => 'New',
                                '2' => 'Good',
                                '3' => 'DOA',
                                '4' => 'Not good',
                                '5' => 'Scrap',

                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($item_details->unit_re_import_status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                '5' => 'warning',

                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Note',
                            'value' => $item_details->note,
                            'type' => 'text'
                        ],
                        
                        
                        
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$item_details->active_attachments"
                    /> 

                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN'))
                        <x-slot:footer>
                            <button type="button" class="js-input-required-btn" data-target="edit-asset-details"><i class="ti-pencil"></i> Edit</button>
                            
                        </x-slot:footer>
                    @endif
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$item_details->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-hps-warehouse', $item_details->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$item_details->ticket_tracking_info"
                />
            </div>
        </div>


        <x-common-ticket-form title="Edit Asset Details" id="edit-asset-details" action1="{{ route('edit-hps-asset-details', $item_details->id) }}" method="POST" enctype="multipart/form-data">
            @method('PATCH')

            <label>Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Serial Number" name="serial_number" value="{{$item_details->serial_number}}" required>

            <label>Box Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Box Serial Number" name="box_serial_number" value="{{$item_details->box_serial_number}}" required>

            <label>Product Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Product Number" name="product_number" value="{{$item_details->product_number}}" required>

            <label>Model</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Model" name="model" value="{{$item_details->model}}" required>

            <label>Ngày nhập kho</label>
            <input type="date" class="ticket-form-body-input" placeholder="Nhập Ngày nhập kho" name="import_date" value="{{$item_details->import_date}}" required>

            <label>Import Source</label>
            <select class="ticket-form-body-input" name="import_source" required>
                <option value="1" {{ $item_details->import_source == 1 ? 'selected' : '' }}>FPT</option>
                <option value="2" {{ $item_details->import_source == 2 ? 'selected' : '' }}>DGW</option>
                <option value="3" {{ $item_details->import_source == 3 ? 'selected' : '' }}>SRFR</option>
            </select>

            <label>PO Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập PO Number" name="po_number" value="{{$item_details->po_number}}" >

            <label>PO Type</label>
            <select class="ticket-form-body-input" name="po_type" required>
                <option value="1" {{ $item_details->po_type == 1 ? 'selected' : '' }}>E-Claim</option>
                <option value="2" {{ $item_details->po_type == 2 ? 'selected' : '' }}>PO Remaining</option>
                <option value="3" {{ $item_details->po_type == 3 ? 'selected' : '' }}>Forecast</option>
            </select>

            <label>Invoice</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Invoice" name="invoice" value="{{$item_details->invoice}}">

            <label>Price</label>
            <input type="number" class="ticket-form-body-input" placeholder="Nhập giá" name="price" value="{{$item_details->price}}" step="0.01">

            <label>Import Status</label>
            <select class="ticket-form-body-input" name="import_status" required>
                <option value="1" {{ $item_details->import_status == 1 ? 'selected' : '' }}>New FB</option>
                <option value="2" {{ $item_details->import_status == 2 ? 'selected' : '' }}>RFB</option>
            </select>

            <label>Site</label>
            <select class="ticket-form-body-input" name="site" required>
                <option value="1" {{ $item_details->site == 1 ? 'selected' : '' }}>HCM</option>
                <option value="2" {{ $item_details->site == 2 ? 'selected' : '' }}>HN</option>
                <option value="3" {{ $item_details->site == 3 ? 'selected' : '' }}>DN</option>
                <option value="4" {{ $item_details->site == 4 ? 'selected' : '' }}>CT</option>
            </select>

            <label>Location</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Location" name="location" value="{{$item_details->location}}">

            <label>Current Status</label>
            <select class="ticket-form-body-input" name="current_status" required>
                <option value="1" {{ $item_details->current_status == 1 ? 'selected' : '' }}>Đang lưu kho</option>
                <option value="2" {{ $item_details->current_status == 2 ? 'selected' : '' }}>Đã hủy</option>
                <option value="3" {{ $item_details->current_status == 3 ? 'selected' : '' }}>Đã xuất máy, chờ thu hồi</option>
                <option value="4" {{ $item_details->current_status == 4 ? 'selected' : '' }}>Chờ hủy</option>
                <option value="5" {{ $item_details->current_status == 5 ? 'selected' : '' }}>Handover</option>

            </select>

            <label>Current HPS Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Current HPS Receipt" name="current_hps_receipt" value="{{$item_details->current_hps_receipt}}">

            <label>Current Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Current Serial Number" name="current_serial_number" value="{{$item_details->current_serial_number}}" required>

            <label>Current Box Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Current Box Serial Number" name="current_box_serial_number" value="{{$item_details->current_box_serial_number}}" required>

            <label>Current Product Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Current Product Number" name="current_product_number" value="{{$item_details->current_product_number}}" required>

            <label>Current Site</label>
            <select class="ticket-form-body-input" name="current_site" required>
                <option value="1" {{ $item_details->current_site == 1 ? 'selected' : '' }}>HCM</option>
                <option value="2" {{ $item_details->current_site == 2 ? 'selected' : '' }}>HN</option>
                <option value="3" {{ $item_details->current_site == 3 ? 'selected' : '' }}>DN</option>
                <option value="4" {{ $item_details->current_site == 4 ? 'selected' : '' }}>CT</option>
            </select>

            <label>Current Location</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Current Location" name="current_location" value="{{$item_details->current_location}}">

            <label>Tình trạng máy</label>
            <select class="ticket-form-body-input" name="unit_re_import_status" required>
                <option value="1" {{ $item_details->unit_re_import_status == 1 ? 'selected' : '' }}>New</option>
                <option value="2" {{ $item_details->unit_re_import_status == 2 ? 'selected' : '' }}>Good</option>
                <option value="3" {{ $item_details->unit_re_import_status == 3 ? 'selected' : '' }}>DOA</option>
                <option value="4" {{ $item_details->unit_re_import_status == 4 ? 'selected' : '' }}>Not good</option>
                <option value="5" {{ $item_details->unit_re_import_status == 5 ? 'selected' : '' }}>Scrap</option>
            </select>

            <label>Notes</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Notes" name="note" value="{{$item_details->note}}">

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


        <x-common-ticket-form title="Export Asset" id="asset-export" action1="{{ route('export-hps-asset', $item_details->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>HPS Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập HPS Receipt" name="hps_receipt" required>

            <label>CE Owner (Nếu user không thuộc SC, để trống)</label>
            <livewire:common-search-dropdown
                model-class="App\Models\User"
                :search-fields="['fullname']"
                display-field="fullname"
                value-field="id"
                name="ce_owner"
            />

            <label>Program Support</label>
            <select class="ticket-form-body-input" name="program_support" required>
                <option value="1">FB30</option>
                <option value="2">Handover</option>
                <option value="3">Luân chuyển nội bộ</option>
                <option value="4">Sàn số serial</option>
                <option value="5">WUE</option>
                <option value="6">Xuất mượn</option>


            </select>

            <label>Export Site</label>
            <select class="ticket-form-body-input" name="export_site" required>
                <option value="1" {{ $item_details->current_site == 1 ? 'selected' : '' }}>HCM</option>
                <option value="2" {{ $item_details->current_site == 2 ? 'selected' : '' }}>HN</option>
                <option value="3" {{ $item_details->current_site == 3 ? 'selected' : '' }}>DN</option>
                <option value="4" {{ $item_details->current_site == 4 ? 'selected' : '' }}>CT</option>
            </select>

            <label>Export Date</label>
            <input type="date" class="ticket-form-body-input" name="export_date" value="{{ today()->format('Y-m-d') }}" required>

            <label>Export Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Export Serial Number" name="export_serial_number" value="{{ $item_details->current_serial_number }}" required>

            <label>Export Box SN</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Export Box SN" name="export_box_serial_number" value="{{ $item_details->current_box_serial_number }}">

            <label>Export Model</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Export Model" name="export_model" value="{{ $item_details->model }}" required>

            <label>Export Location</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Export Location" name="export_location" value="{{ $item_details->current_location }}">

            <label>Notes</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Ghi chú" name="note">

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Export</button> 
            </x-slot:footer>
        </x-common-ticket-form>
    </body>

</html>