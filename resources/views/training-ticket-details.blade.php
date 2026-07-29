<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Training Ticket Details">
            <li>
                <form action="/submit-training-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
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
                        @if((($ticket_details->status == '1') && $ticket_details->user_id == auth()->user()->id))
                        <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
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

    </body>

</html>