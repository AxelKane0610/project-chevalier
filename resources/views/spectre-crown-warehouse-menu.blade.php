<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/spectre-crown-warehouse.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Quản lý kho Crown - Spectre">
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
                        <button type="button" class="js-input-required-btn" data-target="create-spectre-crown-warehouse-item-form"><i class="ti-plus"></i> Nhập kho</button>
                        


                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-thermal-event-tickets-container">
                            <div class="common-table-filter">
                                <div class="filter-group">
                                    <div class="search-box">
                                        <i class="ti-search"></i>
                                        <input type="text" placeholder="Search Serial, Box SN, Product Number & Model" id="search-spectre-crown-warehouse-input">
                                    </div>
                                </div>
                                
                                <div class="filter-group">
                                    <h2>Category:</h2>
                                    <select id="category-filter">
                                        <option value="">All</option>
                                        <option value="1">Laptop</option>
                                        <option value="2">Accessories (Chuột, phím,...)</option>
                                        <option value="3">Màn hình</option>
                                        <option value="4">Máy scanner</option>
                                        <option value="5">PC (Máy tính để bàn)</option>
                                        <option value="6">Máy in khổ lớn</option>
                                        <option value="7">Máy in khổ nhỏ</option>
                                        <option value="8">Khác</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <h2>Warehouse:</h2>
                                    <select id="warehouse-filter">
                                        <option value="">All</option>
                                        <option value="1">Spectre</option>
                                        <option value="2">CROWN HCM</option>
                                        <option value="3">CROWN HN</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Availability:</h2>
                                    <select id="availability-filter">
                                        <option value="">All</option>
                                        <option value="1">Available</option>
                                        <option value="2">Not Available</option>
                                        <option value="3">In Use</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Condition:</h2>
                                    <select id="condition-filter">
                                        <option value="">All</option>
                                        <option value="1">Good working</option>
                                        <option value="2">Not tested</option>
                                        <option value="3">Can't use</option>
                                    </select>
                                </div>


                            </div>
                            <div id="table-data-container">
                                @include('tables.spectre-crown-warehouse-items-table')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-common-ticket-form title="Nhập kho" id="create-spectre-crown-warehouse-item-form" action1="/import-asset">
            @method('POST')
            <label>Asset Tag</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Asset Tag, nếu để trống hệ thống sẽ tự generate" name="asset_tag">


            <label>Asset Type</label>
            <select name="asset_type" class="ticket-form-body-input">
                <option value="1">BUFFER</option>
                <option value="2">CRT Unit</option>
                <option value="3">DASS Unit</option>
                <option value="4">DEMO Unit</option>
                <option value="5">DOA</option>
                <option value="6">Support Unit</option>

            </select>

            <label>Model</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Model" name="model" required>

            <label>Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Serial Number" name="serial_number" required>

            <label>Box Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Box Serial Number" name="box_serial_number" required>

            <label>Product Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập Product Number" name="product_number" required>

            <label>Category</label>
            <select name="category" class="ticket-form-body-input" required>
                <option value="1">Laptop</option>
                <option value="2">Accessory (Chuột, phím,...)</option>
                <option value="3">Màn hình</option>
                <option value="4">Máy scan</option>
                <option value="5">PC (Máy tính để bàn)</option>
                <option value="6">Máy in khổ lớn</option>
                <option value="7">Máy in khổ nhỏ</option>
                <option value="8">Others</option>

            </select>

            <label>Warehouse</label>
            <select name="warehouse" class="ticket-form-body-input" required>
                <option value="1">SPECTRE</option>
                <option value="2">CROWN HCM</option>
                <option value="3">CROWN HN</option>

            </select>

            <label>Available</label>
            <select name="available_status" class="ticket-form-body-input" required>
                <option value="1">Available</option>
                <option value="2">Not available</option>
                <option value="3">In use</option>

            </select>

            <label>Condition</label>
            <select name="condition" class="ticket-form-body-input" required>
                <option value="1">Good working</option>
                <option value="2">Chưa test</option>
                <option value="3">Can't use</option>
            </select>
            
            <label>Note</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập ghi chú" name="note">

            <label>Quantity</label>
            <input type="number" class="ticket-form-body-input" placeholder="Nhập số lượng" name="quantity" value="1" required>

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