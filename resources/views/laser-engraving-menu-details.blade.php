<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/laser-engraving.js', 'resources/css/icons/themify-icons.css'])

        
    </head>

    <body class="background-enable">
        <x-common-header title="Laser Engraving Ticket Details">
            <li>

                <a href="{{ url('/laser-engraving-menu') }}" class="button">
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
                @case(2)
                    @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN'))
                        <li>
                            <form id="change-laser-engraving-status-to-in-progress" class="js-input-required-btn" data-target="change-laser-engraving-status-to-in-progress" action="{{ route('change-laser-engraving-status-to-in-progress', $ticket->id) }}" method="PATCH">
                                @csrf
                                <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                            </form>
                        </li>
                        
                        <li>
                            <button type="button" data-target="close-laser-engraving-ticket-form" class="js-input-required-btn"><i class="ti-check"></i>Close Ticket</button>
                            
                        </li>
                    @endif
                @break
                
                @case(4)
                    @if($ticket->user_id == auth()->user()->id || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN'))
                        <li>
                            <form id="re-open-laser-engraving-ticket" class="js-input-required-btn" data-target="re-open-laser-engraving-ticket" action="{{ route('re-open-laser-engraving-ticket', $ticket->id) }}" method="PATCH">
                                @csrf
                                <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                            </form>
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
                            'icon' => 'ti-angle-double-up',
                            'label' => 'Priority',
                            'value' => match ($ticket->priority) {
                                '1' => 'Normal',
                                '2' => 'Critical',
                                '3' => 'High',
                                '4' => 'Low',

                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->priority) {
                                '1' => 'success',
                                '2' => 'danger',
                                '3' => 'warning',
                                '4' => 'primary',

                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Status',
                            'value' => match ($ticket->status) {
                                '1' => 'Not started',
                                '2' => 'In progress',
                                '3' => 'Completed',
                                '4' => 'Rejected',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Info base',
                            'value' => $ticket->info_base,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-layout-column4',
                            'label' => 'Barcode Info',
                            'value' => $ticket->barcode_info,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Description',
                            'value' => $ticket->description,
                            'type' => 'text'
                        ]
                    ]"

                    
                >

                    <x-common-attachments-table-card
                        :attachments="$ticket->active_attachments"
                    />

                    <x-slot:footer>
                        @if((($ticket->status == '1') && $ticket->user_id == auth()->user()->id) || (auth()->user()->hasRole('ROLE_SUPER_ADMIN')))
                            <button type="button" class="js-input-required-btn" data-target="edit-laser-engraving-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        @endif
                    </x-slot:footer>

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                    <x-common-ticket-comments-card
                        :comments="$ticket->ticket_comments"
                        :showAttachments="true"
                        :actionRoute="route('add-comment-laser-engraving-ticket', $ticket->id)"
                    >


                    </x-common-ticket-comments-card>

                    <!-- ================= Timeline ================= -->

                    <x-common-ticket-tracking-info
                        :trackings="$ticket->ticket_tracking_info"
                    />

            </div>
        </div>
        

            <x-common-ticket-form title="Edit Ticket Khắc Base" id="edit-laser-engraving-ticket-details" action1="{{ route('edit-laser-engraving-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Receipt</label>
                <input type="text" class="ticket-form-body-input" name="ticket_receipt" value=" {{ $ticket->ticket_receipt }}" required>

                <label>Priority</label>
                <select name="priority" class="ticket-form-body-input">
                    <option value="1" {{ $ticket->priority == 1 ? 'selected' : '' }}>Normal</option>
                    <option value="2" {{ $ticket->priority == 2 ? 'selected' : '' }}>Critical</option>
                    <option value="3" {{ $ticket->priority == 3 ? 'selected' : '' }}>High</option>
                    <option value="4" {{ $ticket->priority == 4 ? 'selected' : '' }}>Low</option>
                </select>

                <label>Info base</label>
                <input type="text" class="ticket-form-body-input" name="info_base" value=" {{ $ticket->info_base }}" required>
                <label>Barcode Info</label>
                <input type="text" class="ticket-form-body-input" name="barcode_info" value=" {{ $ticket->barcode_info }}" required>
                <label>Description</label>
                <textarea class="ticket-form-body-input" name="description" rows="5">{{ $ticket->description }}</textarea>
                
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
                {{-- <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>
                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Save</button> 
                </x-slot:footer> --}}

                <div class="upload-group">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit">Save</button> 
                </x-slot:footer>
            </x-common-ticket-form>

            <x-common-ticket-form title="Close Ticket Khắc Base" id="close-laser-engraving-ticket-form" action1="{{ route('close-laser-engraving-ticket', $ticket->id) }}">
                @method('PATCH')
                <label>Status</label>
                <select name="ticket_status" class="ticket-form-body-input" required>
                    <option value="3">Completed</option>
                    <option value="4">Rejected</option>
                </select>

                <label>Comment</label>
                <textarea name="ticket_comment" class="ticket-form-body-input" placeholder="Comment cho người tạo ticket nếu có"></textarea>

                <label class="ticket-form-body-input">Attach File:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple required>
                    <ul class="file-list"></ul>
                </div>
                

                <x-slot:footer>
                    <button type="submit">Close ticket</button>
                </x-slot:footer>
            </x-common-ticket-form>

        
    </body>
</html>