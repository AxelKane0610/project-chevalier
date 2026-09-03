<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/loan-unit-part.js',  'resources/css/icons/themify-icons.css', ])
    
    </head>

    <body>
        <x-common-header title="Loan Unit & Part Menu">
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
                        <button type="button" class="js-input-required-btn" data-target="create-loan-unit-part-ticket"><i class="ti-plus"></i> Create Ticket</button>
                        

                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-loan-unit-part-tickets-btn" data-target = "pending-loan-unit-part-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$tickets->total()}}
                            </span>
                            <i class="ti-timer"></i> 
                            Show Pending Loan Unit Part Tickets
                        </button>
                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-loan-unit-part-tickets-btn" data-target = "all-loan-unit-part-tickets-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$all_tickets->total()}}
                            </span>
                            <i class="ti-list-ol"></i> 
                            Show All Tickets
                        </button>
                    </div>



                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-loan-unit-part-tickets-container">
                            
                            <div class="common-table-filter">
                                <h2>Pending Tickets</h2>
                                <div class="search-box">
                                    <i class="ti-search"></i>
                                    <input type="text" placeholder="Search Receipt" id="search-pending-loan-unit-part-tickets" class="ajax-search">
                                </div>

                                <h2>Status</h2>
                                <select class="ajax-filter" name="status" id="all-loan-unit-part-tickets-status-filter">

                                    <option value="">All</option>
                                    <option value="1">Open</option>
                                    <option value="2">In progress</option>
                                    <option value="3">Completed</option>
                                    <option value="4">Canceled</option>
                                    
                                </select>

                            </div>


                            <div id="pending-loan-unit-part-tickets-table-container">
                                @include('tables.pending-loan-unit-part-tickets-table')
                            </div>
                        </div>



                        <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-loan-unit-part-tickets-container">
                            
                            <div class="common-table-filter">
                                <h2>All Tickets</h2>
                                <div class="search-box">
                                    <i class="ti-search"></i>
                                    <input type="text" placeholder="Search Receipt" id="search-loan-unit-part-all-tickets" class="ajax-search">
                                </div>

                                <h2>Status</h2>
                                <select class="ajax-filter" name="status" id="all-loan-unit-part-tickets-status-filter">

                                    <option value="">All</option>
                                    <option value="1">Open</option>
                                    <option value="2">In progress</option>
                                    <option value="3">Completed</option>
                                    <option value="4">Canceled</option>
                                    
                                </select>

                            </div>
                            
                            <div id="all-loan-unit-part-tickets-table-container">
                                @include('tables.all-loan-unit-part-tickets-table')
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <x-common-ticket-form title="Mượn máy & part form" action1="/create-loan-unit-part-ticket" id="create-loan-unit-part-ticket">
            <lable>Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu" name="ticket_receipt" required>

            <label>Customer's Unit Info</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin máy của khách hàng" name="customer_unit_info" required>
 
            <label>Part Request</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin part cần mượn (Mã part & Tên part)" name="part_request" required>

            
            <label>Attach File:</label> 
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Submit</button> 
            </x-slot:footer>

        </x-common-ticket-form>
    </body>

</html>