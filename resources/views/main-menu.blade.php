<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/icons/themify-icons.css'])
        <!-- <link rel="stylesheet" href="/icons/themify-icons.css"> -->
    
    </head>

    <body class="background-enable">

        <x-common-header title="Xin chào, hôm nay bạn cần hỗ trợ gì ?">
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

        
        
<div class="container-fluid py-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 row-cols-xl-5 g-4 d-flex justify-content-center">

        <!-- Card -->

        <div class="main-menu col" style="width: 300px">
            <a href="{{ url('/software-tickets-menu') }}"
               class="text-decoration-none">

                <div class="card h-100 shadow-lg border-0 rounded-5">

                    <div class="card-body d-flex flex-column text-center">

                        <div class="ticket-img-description">
                            <img src="{{ asset('imgs/Ticket Software Icon.png') }}"
                                 class="img-fluid"
                                 alt="">
                        </div>

                        <h4 class="fw-bold mt-3">
                            Software Support
                        </h4>

                        <p class="text-muted flex-grow-1">
                            Liên quan tới lỗi hệ thống hoặc có đề xuất cải thiện hệ thống
                        </p>

                        <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                            <span>Learn more</span>
                            <i class="ti-angle-right"></i>
                        </div>

                    </div>

                </div>
            </a>
        </div>

        <div class="main-menu col" style="width: 300px">
            <a href="{{ url('/ttex-tickets-menu') }}"
               class="text-decoration-none">

                <div class="card h-100 shadow-lg border-0 rounded-5">

                    <div class="card-body d-flex flex-column text-center">

                        <div class="ticket-img-description">
                            <img src="{{ asset('imgs/transport_image.png') }}"
                                 class="img-fluid"
                                 alt="">
                        </div>

                        <h4 class="fw-bold mt-3">
                            Điều tin TTEX
                        </h4>

                        <p class="text-muted flex-grow-1">
                            Luân chuyển hàng hóa từ TTBH hoặc ngược lại
                        </p>

                        <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                            <span>Learn more</span>
                            <i class="ti-angle-right"></i>
                        </div>

                    </div>

                </div>
            </a>
        </div>

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_USER') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/laser-engraving-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/laser_engraving_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Khắc base
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Khắc base (Mặt D) sau khi thay
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_USER') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/loan-unit-part-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/loanunit_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Mượn máy & part
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Mượn máy/part để bảo hành hoặc test
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif


        <div class="main-menu col" style="width: 300px">
            <a href="{{ url('/submit-training') }}"
               class="text-decoration-none">

                <div class="card h-100 shadow-lg border-0 rounded-5">

                    <div class="card-body d-flex flex-column text-center">

                        <div class="ticket-img-description">
                            <img src="{{ asset('imgs/training_image.png') }}"
                                 class="img-fluid"
                                 alt="">
                        </div>

                        <h4 class="fw-bold mt-3">
                            Submit Training
                        </h4>

                        <p class="text-muted flex-grow-1">
                            Submit các chứng chỉ sau khi hoàn thành training
                        </p>

                        <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                            <span>Learn more</span>
                            <i class="ti-angle-right"></i>
                        </div>

                    </div>

                </div>
            </a>
        </div>

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/subk-management') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/subk_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Quản lý SubK
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Quản lý các thành viên trong team/country
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_USER') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/invoice-exceptional-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/invoice_exceptional_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Invoice Exceptional
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Xin approve đối với các trường hợp hóa đơn xuất sau hoặc cần active bảo hành
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/onboard-offboard-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/onboard_offboard_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Onboard & Offboard
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Thực hiện onboard khi có nhân sự mới và offboard khi có nhân sự nghỉ việc
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_OUT_OF_OFFICE_USER') || auth()->user()->hasRole('ROLE_OUT_OF_OFFICE_ADMIN') )
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/out-of-office-tickets-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/offline_request_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Out of Office Request
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Xin nghỉ phép/đi trễ/về sớm
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_USER') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_LV1_APPROVER') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_LV2_APPROVER'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/thermal-event-tickets-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/overheat.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Thermal Event
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Xin approve exceptional cho các trường hợp quá nhiệt
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SPECTRE_CROWN_WAREHOUSE_ADMIN'))
            <div class="main-menu col" style="width: 300px">
                <a href="{{ url('/spectre-crown-warehouse-menu') }}"
                class="text-decoration-none">

                    <div class="card h-100 shadow-lg border-0 rounded-5">

                        <div class="card-body d-flex flex-column text-center">

                            <div class="ticket-img-description">
                                <img src="{{ asset('imgs/spectre_crown_warehouse_image.png') }}"
                                    class="img-fluid"
                                    alt="">
                            </div>

                            <h4 class="fw-bold mt-3">
                                Spectre - Crown Warehouse
                            </h4>

                            <p class="text-muted flex-grow-1">
                                Quản lý kho Spectre & Crown
                            </p>

                            <div class="mt-auto d-flex justify-content-center align-items-center gap-2">
                                <span>Learn more</span>
                                <i class="ti-angle-right"></i>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        @endif

    </div>
</div>
        
    </body>
</html>