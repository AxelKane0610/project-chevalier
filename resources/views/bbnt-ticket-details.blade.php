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

            <li>
                <form id="change-bbnt-ticket-status-to-in-progress" class="js-input-required-btn" data-target="change-bbnt-ticket-status-to-in-progress" action="{{ route('change-bbnt-ticket-status-to-in-progress', $ticket->id) }}">
                    @csrf
                    @method('POST')
                    <button type="submit"><i class="ti-alarm-clock"></i>In Progress</button>
                </form>
            </li>
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
                                '2' => 'secondary',
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
                        @if(( $ticket->user_id == auth()->user()->id && $ticket->status == '1') || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_USER'))
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

    </body>

</html>