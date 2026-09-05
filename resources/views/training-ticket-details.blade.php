<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/training-management.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Training Ticket Details">
            <li>
                <a href="{{ url('/submit-training-menu') }}" class="button">
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

            @if ( ($ticket_details->user_id == auth()->user()->id) || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TRAINING_ADMIN') )
                <form id="send-verify-training-ticket" data-target="send-verify-training-ticket" class="js-input-required-btn" action="{{ route('send-verify-training-ticket', $ticket_details->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit"><i class="ti-angle-double-right"></i>Send verify</button>
                </form>
            @endif

            @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TRAINING_ADMIN')) && $ticket_details->status == '5' )
                <li>
                    <form id="approve-ticket-form" class="js-input-required-btn" data-target="approve-ticket-form" action="{{ route('confirm-training-completed', $ticket_details->id) }}" method="POST">
                        <button type="submit"><i class="ti-thumb-up"></i>Confirm training completed</button>
                    </form>
                </li>

                <li>
                    <form id="reject-ticket-form" class="js-input-required-btn" data-target="reject-ticket-form" action="{{ route('reject-training-completed', $ticket_details->id) }}" method="POST">
                        @csrf
                        <button type="submit"><i class="ti-thumb-down"></i>Reject</button>
                    </form>
                </li>
            @endif

        </x-common-header>

        <div class="common-table-container">
            <table class="common-table" width="100%">
                <thead>
                    <th width="25%">Training No</th>
                    <th width="25%">Course ID</th>   
                    <th width="25%">Course Name</th>
                    <th width="25%">Start Date</th> 
                    <th width="25%">End Date</th> 
                </thead>
                
                @foreach($ticket_details->training_courses as $training_course)
                <tbody>
                    
                    <tr>
                        
                        <td>{{$training_course->training_no}}</td>
                        <td>{{$training_course->course_id}}</td>
                        <td>{{$training_course->course_name}}</td>
                        <td>{{$training_course->start_date}}</td>
                        <td>{{$training_course->end_date}}</td>

                    </tr>
                
                </tbody>
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
                            'label' => 'Training No',
                            'value' => $ticket_details->training_no,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-user',
                            'label' => 'User Owner',
                            'value' => $ticket_details->user_owner->fullname,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Status',
                            'value' => match ($ticket_details->status) {
                                '1' => 'Open',
                                '2' => 'Chưa submit',
                                '3' => 'Đã submit, chờ verify',
                                '4' => 'Completed',
                                '5' => 'Rejected',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket_details->status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'secondary',
                                '4' => 'success',
                                '5' => 'danger',
                                default => 'Unknown',
                            },
                        ],
                        
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$ticket_details->active_attachments"
                    />

                    
                    <x-slot:footer>
                        @if( $ticket_details->user_id == auth()->user()->id || auth()->user()->hasRole('ROLE_SUPER_ADMIN') )
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit/Upload Certificates</button>
                        @endif
                    </x-slot:footer>
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$ticket_details->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-training-ticket', $ticket_details->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket_details->ticket_tracking_info"
                />
            </div>
        </div>


        <x-common-ticket-form title="Edit/Upload Certificates" id="edit-ticket-details" action1="{{ route('edit-upload-training-ticket', $ticket_details->id) }}">
            @method('PATCH')
            @if($ticket_details->active_attachments->count() > 0) 
                <x-common-attachments-table>
                    @foreach($ticket_details->active_attachments as $attachment)
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
            <div class="upload-group">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Save</button> 
            </x-slot:footer>
        </x-common-ticket-form>

    </body>

</html>