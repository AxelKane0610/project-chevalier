<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/hps-warehouse.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Quản lý kho HPS">
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

                        @if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN'))
                            <button type="button" class="js-input-required-btn" data-target="import-hps-warehouse-item-form"><i class="ti-plus"></i> Nhập kho</button>
                        @endif

                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-items-btn" data-target = "all-hps-items-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$items->total()}}
                            </span>
                            <i class="ti-list"></i> 
                            Show All
                        </button>

                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-items-btn" data-target = "pending-hps-items-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$pending_items->total()}}
                            </span>
                            <i class="ti-timer"></i> 
                            Show Pending Items
                        </button>


                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="all-hps-items-container">
                            <div class="common-table-filter">
                                <div class="filter-group">
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input type="text" placeholder="Search Serial, Box SN, Product Number, Model, Invoice" id="search-hps-warehouse-input" class="ajax-search">
                                    </div>
                                </div>
                                
                                <div class="filter-group">
                                    <h2>Current Status:</h2>
                                    <select name="current_status" class="ajax-filter">
                                        <option value="">All</option>
                                        <option value="1">Đang lưu kho</option>
                                        <option value="2">Đã hủy</option>
                                        <option value="3">Đã xuất máy, chờ thu hồi</option>
                                        <option value="4">Chờ hủy</option>
                                        <option value="5">Handover</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <h2>Current Site:</h2>
                                    <select name="current_site" class="ajax-filter">
                                        <option value="">All</option>
                                        <option value="1">HCM</option>
                                        <option value="2">HN</option>
                                        <option value="3">DN</option>
                                        <option value="4">CT</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Tình trạng máy:</h2>
                                    <select name="unit_re_import_status" class="ajax-filter">
                                        <option value="">All</option>
                                        <option value="1">New</option>
                                        <option value="2">Good</option>
                                        <option value="3">DOA</option>
                                        <option value="4">Not good</option>
                                        <option value="5">Scrap</option>

                                    </select>
                                </div>

                                


                            </div>
                            <div id="all-hps-items-table-container">
                                @include('tables.hps-warehouse-items-table')
                            </div>

                        </div>


                        <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="pending-hps-items-container" >
                            <div class="common-table-filter">
                                <div class="filter-group">
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input type="text" placeholder="Search Serial, Box SN, Product Number, Model, Invoice" id="search-hps-warehouse-input" class="ajax-search">
                                    </div>
                                </div>
                                
                            
                                
                                <div class="filter-group">
                                    <h2>Site:</h2>
                                    <select name="current_site" class="ajax-filter">
                                        <option value="">All</option>
                                        <option value="1">HCM</option>
                                        <option value="2">HN</option>
                                        <option value="3">DN</option>
                                        <option value="4">CT</option>
                                    </select>
                                </div>

                                

                                


                            </div>
                            <div id="all-hps-items-table-container">
                                @include('tables.hps-warehouse-pending-items-table')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    <x-common-ticket-form title="Nhập kho" id="import-hps-warehouse-item-form" action1="/import-asset">
        @method('POST')

        <table class="table" id="items-table">
            <thead>
                <tr>
                    <th width="15%">Serial Number</th>
                    <th width="15%">Product Number</th>
                    <th width="25%">Model</th>
                    <th width="15%">PO Number/E-Claim</th>
                    <th width="15%">Price</th>
                    <th width="15%">Note</th>
                    <th width="10%"></th>
                </tr>
            </thead>

            <tbody id="items-body">
                <tr>
                    <td>
                        <input type="text"
                            name="serial_number[]"
                            class="form-control"
                            required>
                    </td>


                    <td>
                        <input type="text"
                            name="product_number[]"
                            class="form-control"
                            required>
                    </td>

                    <td>
                        <input type="text"
                            name="model[]"
                            class="form-control"
                            readonly
                            required>
                    </td>

                    <td>
                        <input type="text"
                            name="po_number[]"
                            class="form-control"
                            >
                    </td>

                    <td>
                        <input type="text"
                            name="price[]"
                            class="form-control"
                            >
                    </td>

                    <td>
                        <input type="text"
                            name="note[]"
                            class="form-control"
                            >
                    </td>

                    <td>
                        <button type="button" class="btn btn-danger remove-row">
                            X
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-primary" id="add-row">
            + Add Row
        </button>

        <label>Import Souce</label>
        <select name="import_source" class="ticket-form-body-input" required>
            <option value="1">FPT</option>  
            <option value="2">DGW</option>
            <option value="3">SRFR</option>

        </select>

        <label>PO Type</label>
        <select name="po_type" class="ticket-form-body-input" required>
            <option value="1">E-Claim</option>  
            <option value="2">PO Remaining</option>
            <option value="3">Forecast</option>

        </select>

        <label>Import Status</label>
        <select name="import_status" class="ticket-form-body-input" required>
            <option value="1">New FB</option>  
            <option value="2">RFB</option>

        </select>

        <label>Trạng thái máy</label>
        <select name="unit_re_import_status" class="ticket-form-body-input" required>
            <option value="1">New</option>  
            <option value="2">Good</option>
            <option value="3">DOA</option>  
            <option value="4">Not good</option>
            <option value="5">Scrap</option>

        </select>

        <label>Site</label>
        <select name="site" class="ticket-form-body-input" required>
            <option value="1">HCM</option>  
            <option value="2">HN</option>
            <option value="3">DN</option>  
            <option value="4">CT</option>

        </select>

        <label>Location</label>
        <input type="text"
            name="location"
            class="form-control"
            >

        <label>Invoice</label>
        <input type="text" name="invoice" class="form-control" placeholder="Ví dụ: 1K25TPP_123456_BBBG">

        

        <label>Attach File:</label>
        <div class="upload-group ">
            <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
            <ul class="file-list"></ul>
        </div>

        <x-slot:footer>
            <button class="ticket-form-body-input" type="submit">Nhập kho</button> 
        </x-slot:footer>
        


    </x-common-ticket-form>


    </body>
</html>