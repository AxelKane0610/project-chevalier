<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/out-of-office.js', 'resources/css/icons/themify-icons.css'])
            
    </head>

    <body>
        <x-common-header title="Out of Office Ticket Details">
            <li>
                <a href="{{ url('/out-of-office-tickets-menu') }}" class="button">
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
            
            @if ($ticket->status == '1' && $ticket->user_id == auth()->user()->id)
                <li>
                    <form id="send-approve-out-of-office-ticket" data-target="send-approve-out-of-office-ticket" action="{{route('send-approve-out-of-office-ticket', $ticket->id) }}">
                        @method('POST')
                        <button type="submit"><i class="ti-angle-double-right"></i>Send Approval </button>
                    </form>
                </li>
            @endif

            @if ($ticket->status == '2' && auth()->user()->hasRole('ROLE_OUT_OF_OFFICE_ADMIN'))
                @can('is-leader-of-ticket', $ticket)
                <li>
                    <form id="approve-out-of-office-ticket" class="js-input-required-btn" data-target="approve-out-of-office-ticket" action="{{ route('approve-out-of-office-ticket', $ticket->id) }}" method="POST">
                        
                        <button type="submit"><i class="ti-thumb-up"></i>Approve</button>
                    </form>
                </li>

                <li>
                    <form id="reject-out-of-office-ticket" class="js-input-required-btn" data-target="reject-out-of-office-ticket" action="{{ route('reject-out-of-office-ticket', $ticket->id) }}" method="POST">
                        
                        <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                    </form>
                </li>
                @endcan
            @endif

            @if($ticket->user_id == auth()->user()->id && $ticket->status == '4')
                <li>
                    <form id="re-open-out-of-office-ticket" class="js-input-required-btn" data-target="re-open-out-of-office-ticket" action="{{ route('re-open-out-of-office-ticket', $ticket->id) }}" method="POST">
                        
                        <button type="submit"><i class="ti-back-left"></i>Request Re-Open</button>
                    </form>
                </li>
            @endif


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
                            'icon' => 'ti-list',
                            'label' => 'Type of Leave',
                            'value' => match ($ticket->type_of_leave) {
                                '1' => 'Xin nghỉ phép',
                                '2' => 'Xin đi trễ',
                                '3' => 'Xin về sớm',
                                '4' => 'Xin không chấm công vào',
                                '5' => 'Xin không chấm công ra',
                                '6' => 'Quên chấm công vào/ra',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->type_of_leave) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                '5' => 'warning',
                                '6' => 'info',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Status',
                            'value' => match ($ticket->status) {
                                '1' => 'Open',
                                '2' => 'Waiting for approval',
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
                            'label' => 'Reasons for leave',
                            'value' => $ticket->reasons_for_leave,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Days of leave',
                            'value' => $ticket->days_of_leave,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Start Date',
                            'value' => $ticket->start_date,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'End Date',
                            'value' => $ticket->end_date,
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
                    :actionRoute="route('add-comment-out-of-office-ticket', $ticket->id)"
                >

                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket->ticket_tracking_info"
                />
            </div>
        </div>



        <x-common-ticket-form title="Edit Out of Office Ticket" id="edit-ticket-details" action1="{{ route('edit-out-of-office-ticket', $ticket->id) }}">
            @method('PATCH')

            <li>
                <lable>Type of Leave</lable>
                <select name="type_of_leave" class="ticket-form-body-input">
                    <option value="1" @selected($ticket->type_of_leave == 1)>Xin nghỉ phép</option>
                    <option value="2" @selected($ticket->type_of_leave == 2)>Xin đi trễ</option>
                    <option value="3" @selected($ticket->type_of_leave == 3)>Xin về sớm</option>
                    <option value="4" @selected($ticket->type_of_leave == 4)>Xin không chấm công vào</option>
                    <option value="5" @selected($ticket->type_of_leave == 5)>Xin không chấm công ra</option>
                    <option value="6" @selected($ticket->type_of_leave == 6)>Quên chấm công vào/ra</option>
                </select>
                
                
            </li>

            <li>
                <lable>Reasons for leave</lable>
                <input type="text" class="ticket-form-body-input" name="reasons_for_leave" value="{{ $ticket->reasons_for_leave }}">

                
            </li>

            <li>
                <lable>Days of leave</lable>
                <input type="number" class="ticket-form-body-input" name="days_of_leave" value="{{ $ticket->days_of_leave }}" step="0.5">
            </li>

            <li>
                <lable>Start Date</lable>
                <input type="datetime-local" class="ticket-form-body-input" name="start_date" value="{{ $ticket->start_date }}" required>

                
            </li>

            <li>
                <lable>End Date</lable>
                <input type="datetime-local" class="ticket-form-body-input" name="end_date" value="{{ $ticket->end_date }}" required>
            </li>

            <label><b>Attachments</b></label>
                
                @if($ticket->active_attachments->count() > 0) 
                    <x-common-attachments-table>
                        @foreach($ticket->active_attachments as $attachment)
                            <tr>
                                <td>
                                    
                                    <!-- <a href="{{ asset('attachments/' . $attachment->path) }}" target="_blank"> -->
                                        {{ $attachment->name ?? 'File đính kèm' }}
                                    <!-- </a> -->
                                </td>
                                <td>
                                    <!-- <div class="form-check">
                                        
                                        <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <input class="form-check-input" type="checkbox" name="delete_files[]" value="{{ $attachment->id }}" id="del_{{ $attachment->id }}">
                                        <label class="form-check-label text-danger" for="del_{{ $attachment->id }}">
                                            Xóa
                                        </label>
                                    </div> -->

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

    </body>

</html>