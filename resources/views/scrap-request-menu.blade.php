<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/scrap-request.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Quản lý hủy hàng">
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

                        
                        <button type="button" class="js-input-required-btn" data-target="create-scrap-ticket-form"><i class="ti-plus"></i> Create Ticket</button>

                        <button class="btn btn-primary table-btn w-100 position-relative" data-target = "pending-scrap-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$pending_tickets->total()}}
                            </span>
                            <i class="ti-timer"></i> 
                            Pending Tickets
                        </button>


                        <button class="btn btn-primary table-btn w-100 position-relative" data-target = "all-scrap-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$all_tickets->total()}}
                            </span>
                            <i class="ti-list"></i> 
                            Show All Tickets
                        </button>

                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-scrap-tickets-container">

                            <div id="pending-scrap-tickets-table-container">
                                @include('tables.pending-scrap-tickets-table')
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-scrap-tickets-container">
                            <div class="common-table-filter">
                                <div class="filter-group">
                                    <h2>All Tickets</h2>
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input class="ajax-search" type="text" placeholder="Search thông tin hàng hủy tóm tắt hoặc loại hàng hủy">
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <h2>Status</h2>
                                    <select class="ajax-filter" name="status">
                                        <option value="">All</option>
                                        <option value="1">Open</option>
                                        <option value="2">Waiting leader approve</option>
                                        <option value="3">Waiting manager approve</option>
                                        <option value="4">Fully approved</option>
                                        <option value="5">Rejected</option>

                                        
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Month</h2>
                                    <select class="ajax-filter" name="month">
                                        <option value="">All Months</option>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                        
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Year</h2>
                                    <select class="ajax-filter" name="year">
                                        <option value="">All Years</option>
                                        @for ($year = now()->year; $year >= 2026; $year--)
                                            <option value="{{ $year }}"
                                                {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                        
                                    </select>
                                </div>

                                

                            </div>

                            <div id="all-scrap-tickets-table-container">
                                @include('tables.all-scrap-tickets-table')
                            </div>
                        </div>

                        

                    </div>
                </div>
            </div>
        </div>

        <x-common-ticket-form title="Submit hủy hàng" id="create-scrap-ticket-form" action1="/create-scrap-ticket">
            @method('POST')
            <label class="ticket-form-body-input">Loại hàng hủy</label>
            <input type="text" class="ticket-form-body-input" placeholder="Ví dụ: ASRC, HPS, Part Trade,..." name="scrap_type" required>

            <label class="ticket-form-body-input">Ngày kiểm hủy</label>
            <input type="date" class="ticket-form-body-input" value="{{ now()->format('Y-m-d') }}" name="scrap_date" required>

            <label>Thông tin hàng hủy (Tóm tắt)</label>
            <textarea class="ticket-form-body-input multiple-row" name="scrap_description" placeholder="Ví dụ: 1 máy in, 2 máy tính, 3 máy scan,..." required></textarea>

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