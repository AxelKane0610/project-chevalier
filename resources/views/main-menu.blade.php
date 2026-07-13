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
                <li>
                    <div class="search-container">
                        <form action="">
                            @csrf
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
            </li>

        </x-common-header>

        
        
        <div id="main-menu-categories-container">
            
            <a href="{{ url('/software-tickets-menu') }}">
                <div class="software-tickets-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/Ticket Software Icon.png') }}" alt="">
                    </div>
                    
                    <h2>Software</h2>
                    <p>Liên quan tới lỗi hệ thống EEG hoặc có đề xuất cải thiện hệ thống</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
            </a>
            
           <a href="{{ url('/ttex-tickets-menu') }}">
                <div class="ttex-tickets-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/transport_image.png') }}" alt="">
                    </div>
                    
                    <h2>Điều tin TTEX</h2>
                    <p>Luân chuyển hàng hóa từ TTBH hoặc ngược lại</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
           </a>
            
            
            <a href="{{ url('/laser-engraving-menu') }}">
                <div class="laser-engraving-btn">
                    
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/laser_engraving_image.png') }}" alt="">
                    </div>
                    
                    <h2>Khắc base</h2>
                    <p>Khắc base (Mặt D) sau khi thay</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                    
                </div>
            </a>

            <a href="{{ url('/loan-unit-part-menu') }}">
                <div class="loanunit-tickets-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/loanunit_image.png') }}" alt="">
                    </div>
                    
                    <h2>Mượn máy & parts</h2>
                    <p>Mượn máy/part để bảo hành hoặc test</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
            </a>

            <div class="training-submit-btn">
                <div class="ticket-img-description">
                    <img src="{{ asset('imgs/training_image.png') }}" alt="">
                </div>
                
                <h2>Submit training</h2>
                <p>Submit các chứng chỉ sau khi hoàn thành training</p>
                <div class="card-footer">
                    <div class="card-footer-text">Learn more</div>
                    <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                </div>
            </div>

            <a href="{{ url('/subk-management') }}">

                <div class="subk-management-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/subk_image.png') }}" alt="">
                    </div>
                    
                    <h2>Quản lý SubK</h2>
                    <p>Quản lý các thành viên trong team/country</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>

            </a>

            <a href="{{ url('/invoice-exceptional-menu') }}">

                <div class="invoice-exceptional-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/invoice_exceptional_image.png') }}" alt="">
                    </div>
                    
                    <h2>Invoice Exceptional</h2>
                    <p>Xin approve đối với các trường hợp hóa đơn xuất sau hoặc cần active bảo hành </p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
                
            </a>

            <a href="#">

                <div class="onboard-offboard-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/onboard_offboard_image.png') }}" alt="">
                    </div>
                    
                    <h2>Onboard & Offboard</h2>
                    <p>Thực hiện onboard khi có nhân sự mới và offboard khi có nhân sự nghỉ việc</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
                
            </a>

            <a href="{{ url('/out-of-office-tickets-menu') }}">

                <div class="offline-request-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/offline_request_image.png') }}" alt="">
                    </div>
                    
                    <h2>Out of office Request</h2>
                    <p>Xin nghỉ phép/đi trễ/về sớm</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
                
            </a>

            <a href="{{ url('/thermal-event-tickets-menu') }}">

                <div class="overheat-request-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/overheat.png') }}" alt="">
                    </div>
                    
                    <h2>Thermal Event</h2>
                    <p>Xin approve exceptional cho các trường hợp quá nhiệt</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
                
            </a>

            <a href="{{ url('/spectre-crown-warehouse-menu') }}">

                <div class="spectre-crown-warehouse-btn">
                    <div class="ticket-img-description">
                        <img src="{{ asset('imgs/spectre_crown_warehouse_image.png') }}" alt="">
                    </div>
                    
                    <h2>Quản lý kho Crown - Spectre</h2>
                    <p>Quản lý kho Crown Spectre</p>
                    <div class="card-footer">
                        <div class="card-footer-text">Learn more</div>
                        <div class="card-footer-icon"><i class="ti-angle-right"></i></div>
                    </div>
                </div>
                
            </a>

        </div>
        
        
    </body>
</html>