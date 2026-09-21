<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/bbnt.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Ticket Details">
            <li>
                <a href="{{ url('/bbnt-partner-onsite-menu') }}" class="button">
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

            @if( $ticket->status == '1' && (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN') ))
                <li>
                    <form id="change-bbnt-ticket-status-to-in-progress" class="js-input-required-btn" data-target="change-bbnt-ticket-status-to-in-progress" action="{{ route('change-bbnt-ticket-status-to-in-progress', $ticket->id) }}">
                        @csrf
                        @method('POST')
                        <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                    </form>
                </li>
            @endif

            @if( auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN') )
                <li>
                    <button type="button" class="js-input-required-btn" data-target="close-bbnt-ticket"><i class="ti-check"></i> Complete ticket</button>
                </li>
            @endif
        </x-common-header>


        <div class="container-fluid px-4 py-4">
            <div class="row g-4" style="min-height: calc(100vh - 90px);">

                <!-- ================= Ticket Detail ================= -->
                <x-common-ticket-details-card 
                    :rows="[
                        [
                            'icon' => 'ti-bookmark',
                            'label' => 'Title',
                            'value' => $ticket->document_name,
                            'type' => 'text'
                        ],

                        [
                            'icon' => 'ti-user',
                            'label' => 'Người request',
                            'value' => $ticket->user_owner->fullname ?? 'N/A',
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-email',
                            'label' => 'Địa chỉ email nhận kết quả',
                            'value' => $ticket->email_address ?? 'N/A',
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Địa chỉ',
                            'value' => $ticket->partner_address,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Thành phố',
                            'value' => $ticket->partner_city,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Tổng số lượng case',
                            'value' => $ticket->total_case,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Tổng số tiền',
                            'value' => number_format($ticket->total_amount ?? 0, 0, ',', '.'),
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-list',
                            'label' => 'Onsite Type',
                            'value' => match ($ticket->onsite_type) {
                                '1' => 'MÁY TÍNH',
                                '2' => 'MÁY IN',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->onsite_type) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Trạng thái',
                            'value' => match ($ticket->status) {
                                '1' => 'Đã submit, chờ kiểm tra',
                                '2' => 'Đang kiểm tra',
                                '3' => 'Hoàn tất',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->status) {
                                '1' => 'primary',
                                '2' => 'danger',
                                '3' => 'success',
                                default => 'Unknown',
                            },
                        ],

                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Ghi chú',
                            'value' => $ticket->notes,
                            'type' => 'text'
                        ],
                        
                        
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$ticket->active_attachments"
                    />

                
                    <x-slot:footer>
                        @if(( $ticket->user_id == auth()->user()->id && $ticket->status == '1') || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN'))
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        @endif
                    </x-slot:footer>
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$ticket->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-bbnt-ticket', $ticket->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket->ticket_tracking_info"
                />
            </div>

            


            
        </div>

        <x-common-ticket-form title="Edit BBNT Ticket" id="edit-ticket-details" action1="{{ route('edit-bbnt-ticket-details', $ticket->id) }}">
            @method('PATCH')
            <label>Title</label>
            <input type="text" class="ticket-form-body-input" name="document_name" value="{{ $ticket->document_name}}" required>

            <label>Địa chỉ email nhận kết quả (Có thể điền nhiều email)</label>
            <input type="string" class="ticket-form-body-input" name="email_address" value="{{ $ticket->email_address}}" required>

            <label>Địa chỉ</label>
            <input type="text" class="ticket-form-body-input" name="partner_address" value="{{ $ticket->partner_address}}" required>

            <label>Thành phố</label>
            <input type="text" class="ticket-form-body-input" name="partner_city" value="{{ $ticket->partner_city}}" required>

            <label class="ticket-form-body-input">Tổng số lượng case</label>
            <input type="number" class="ticket-form-body-input" name="total_case" placeholder="Nhập tổng số lượng case nghiệm thu" value="{{ $ticket->total_case}}" required>

            <label class="ticket-form-body-input">Tổng số tiền (VND)</label>
            <input type="text" 
                class="ticket-form-body-input" 
                name="total_amount" 
                id="total_amount" 
                placeholder="Nhập tổng số tiền" 
                inputmode="numeric" 
                value="{{ $ticket->total_amount ? number_format($ticket->total_amount, 0, ',', '.') : '' }}" 
                required>

            <label>Onsite Type</label>
            <select name="onsite_type" class="ticket-form-body-input">
                <option value="1" @selected($ticket->onsite_type == '1')>MÁY TÍNH</option>
                <option value="2" @selected($ticket->onsite_type == '2')>MÁY IN</option>

                
            </select>

            <label class="ticket-form-body-input">Ghi chú</label>
            <input type="text" class="ticket-form-body-input" name="notes" value="{{ $ticket->notes }}" placeholder="Nhập ghi chú">

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

        <x-common-ticket-form title="Complete BBNT Ticket" id="close-bbnt-ticket" action1="{{ route('close-bbnt-ticket', $ticket->id) }}">
                
            @method('POST')
            <label class="ticket-form-body-input">Comment thêm (nếu có)</label>
            <input type="text" class="ticket-form-body-input" name="comment" placeholder="Nhập comment">

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Complete</button> 
            </x-slot:footer>
        </x-common-ticket-form>


    </body>

</html>