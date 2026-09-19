<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/bbnt.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Quản lý biên bản nghiệm thu">
            <li>
                <a href="{{ url('/main-menu') }}" class="button">
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
        </x-common-header>


        <div class="d-flex flex-grow-1 overflow-hidden vh-100">
            <div class="container-fluid my-5 flex-grow-1">
                <div class="row flex-grow-1 h-100">
                    <div class="col-2 d-flex justify-content-center align-items-center flex-column gap-3">

                        
                        <button type="button" class="js-input-required-btn" data-target="create-bbnt-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                        
                        
                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-items-btn" data-target = "pending-bbnt-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$pending_tickets->total()}}
                            </span>
                            <i class="ti-timer"></i> 
                            Pending Tickets
                        </button>


                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-items-btn" data-target = "all-bbnt-ticket-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$all_tickets->total()}}
                            </span>
                            <i class="ti-list"></i> 
                            Show All Tickets
                        </button>

                        


                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-bbnt-tickets-container">
                                    
                            <div class="common-table-filter">
                                <div class="filter-group">
                                    <h2>Pending Tickets</h2>
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input class="ajax-search" type="text" placeholder="Search Receipt, số Invoice, SN & PN" id="search-invoice-exceptional-receipt-input">
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <h2>Onsite Type</h2>
                                    <select class="ajax-filter" name="onsite_type">
                                        <option value="">All</option>
                                        <option value="1">MÁY TÍNH</option>
                                        <option value="2">MÁY IN</option>
                                        
                                    </select>
                                </div>

                                

                            </div>

                            <div id="all-invoice-exception-tickets-table-container">
                                @include('tables.pending-bbnt-tickets-table')
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <x-common-ticket-form title="Request nghiệm thu" id="create-bbnt-ticket-form" action1="/create-bbnt-ticket">
            @method('POST')
            <label class="ticket-form-body-input">Công ty request</label>
            <input type="text" class="ticket-form-body-input" value="{{ auth()->user()->fullname }}" readonly>

            <label class="ticket-form-body-input">Địa chỉ</label>
            <input type="text" class="ticket-form-body-input" name="partner_address" placeholder="Nhập địa chỉ của công ty" required>

            <label class="ticket-form-body-input">Thành phố</label>
            <input type="text" class="ticket-form-body-input" name="partner_city" placeholder="Nhập thành phố" required>

            <label class="ticket-form-body-input">Onsite Type</label>
            <select name="onsite_type" class="ticket-form-body-input" required>
                <option value="1">MÁY TÍNH</option>
                <option value="2">MÁY IN</option>
            </select>

            <label class="ticket-form-body-input">Tổng số lượng case</label>
            <input type="number" class="ticket-form-body-input" name="total_case" placeholder="Nhập tổng số lượng case nghiệm thu" required>

            <label class="ticket-form-body-input">Tổng số tiền (VND)</label>
            <input type="text" 
                class="ticket-form-body-input" 
                name="total_amount" 
                id="total_amount" 
                placeholder="Nhập tổng số tiền" 
                inputmode="numeric" 
                required>

            <label class="ticket-form-body-input">Ghi chú thêm (nếu có)</label>
            <input type="string" class="ticket-form-body-input" name="notes" placeholder="Ghi chú">

            <label class="ticket-form-body-input">Đính kèm file (đính kèm ít nhất 1 file):</label>
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple required>
                <ul class="file-list"></ul>
            </div>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Submit</button> 
            </x-slot:footer>

        </x-common-ticket-form>



    </body>
</html>