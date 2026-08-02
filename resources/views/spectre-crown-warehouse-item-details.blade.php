<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
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
                            'color' => 'primary',
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
                                '3' => 'warning',
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

        
    </body>

</html>