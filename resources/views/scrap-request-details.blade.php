<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/scrap-request.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Ticket Details">
            <li>
                <a href="{{ url('/scrap-request-menu') }}" class="button">
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
                    @if( auth()->user()->hasRole('ROLE_SUPER_ADMIN') || $ticket->user_id == auth()->user()->id)
                        <li>
                            <form class="js-input-required-btn" data-target="send-approve-scrap-request" id="send-approve-scrap-request" action = "{{ route('send-approve-scrap-request', $ticket->id)}}" method="POST">
                                @method('POST')
                                <button type="submit"><i class="ti-angle-double-right"></i>Send Approval </button>
                            </form>
                        </li>

                    @endif
                @break

                @case(5)
                    @if( auth()->user()->hasRole('ROLE_SUPER_ADMIN') || $ticket->user_id == auth()->user()->id)
                        <li>
                            <form id="re-open-scrap-ticket-form" class="js-input-required-btn" data-target="re-open-scrap-ticket-form" action="{{ route('re-open-scrap-ticket', $ticket->id) }}" method="PATCH">
                                @csrf
                                @method('PATCH')
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
                            'icon' => 'ti-user',
                            'label' => 'Người request',
                            'value' => $ticket->user_owner->fullname,
                            'type' => 'text'
                        ],

                        [
                            'icon' => 'ti-user',
                            'label' => 'Loại hàng hủy',
                            'value' => $ticket->scrap_type ?? 'N/A',
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Ngày kiểm hủy',
                            'value' => $ticket->scrap_date ?? 'N/A',
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Thông tin hàng hủy',
                            'value' => $ticket->scrap_description,
                            'type' => 'text'
                        ],
                        
                        [
                            'icon' => 'ti-list',
                            'label' => 'Status',
                            'value' => match ($ticket->status) {
                                '1' => 'Open',
                                '2' => 'Waiting leader approve',
                                '3' => 'Waiting manager approve',
                                '4' => 'Fully approved',
                                '5' => 'Rejected',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'warning',
                                '4' => 'success',
                                '5' => 'danger'
                            },
                        ],
                        
                        
                        
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$ticket->active_attachments"
                    />

                
                    <x-slot:footer>
                        @if(( $ticket->user_id == auth()->user()->id && $ticket->status == '1') || auth()->user()->hasRole('ROLE_SUPER_ADMIN'))
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        @endif
                    </x-slot:footer>
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$ticket->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-scrap-ticket', $ticket->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket->ticket_tracking_info"
                />
            </div>

        </div>


    </body>

    <x-common-ticket-form title="Edit Ticket" id="edit-ticket-details" action1="{{ route('edit-scrap-ticket-details', $ticket->id) }}">
        @method('PATCH')
        <label>Loại hàng hủy</label>
        <input type="text" class="ticket-form-body-input" name="scrap_type" value="{{ $ticket->scrap_type}}" required>

        <label>Ngày kiểm hủy</label>
        <input type="date" class="ticket-form-body-input" name="scrap_date" value="{{ $ticket->scrap_date}}" required>

        <label>Thông tin hàng hủy (Tóm tắt)</label>
        <textarea class="ticket-form-body-input multiple-row" name="scrap_description" placeholder="Ví dụ: 1 máy in, 2 máy tính, 3 máy scan,..."  required>{{ $ticket->scrap_description }}</textarea>

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

</html>