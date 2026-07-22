<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/spectre-crown-warehouse.js', 'resources/css/app.css',  'resources/css/icons/themify-icons.css', ])
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    </head>

    <body class="background-enable">
        <div id="spectre-crown-warehouse-menu">

            <x-common-header title="Quản lý kho Crown - Spectre">
                <li>
                    <form action="/main-menu">
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="#">
                        <button type="submit"><i class="ti-search"></i>Search</button>
                    </form>
                </li>
            </x-common-header>

            <div class="spectre-crown-warehouse-menu-content">

                <form action=""  >
                    <button type="button" class="js-input-required-btn" data-target="create-spectre-crown-warehouse-item-form"><i class="ti-plus"></i> Nhập kho</button>
                </form>
                

                <div class="common-table-container">
                    <div class="common-table-filter">
                        <div class="search-box">
                            <i class="ti-search"></i>
                            <input type="text" placeholder="Search Serial, Box SN, Product Number & Model" id="search-spectre-crown-warehouse-input">
                        </div>

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

                        <h2>Warehouse:</h2>
                        <select id="warehouse-filter">
                            <option value="">All</option>
                            <option value="1">Spectre</option>
                            <option value="2">CROWN HCM</option>
                            <option value="3">CROWN HN</option>
                        </select>

                        <h2>Availability:</h2>
                        <select id="availability-filter">
                            <option value="">All</option>
                            <option value="1">Available</option>
                            <option value="2">Not Available</option>
                            <option value="3">In Use</option>
                        </select>

                        <h2>Condition:</h2>
                        <select id="condition-filter">
                            <option value="">All</option>
                            <option value="1">Good working</option>
                            <option value="2">Not tested</option>
                            <option value="3">Can't use</option>
                        </select>


                    </div>
                    <div id="table-data-container">
                        @include('tables.spectre-crown-warehouse-items-table')
                    </div>
                    

                    

                </div>

                <x-common-ticket-form title="Nhập kho" id="create-spectre-crown-warehouse-item-form" action1="">
                    @method('POST')
                    <label>Asset Type</label>
                    <select name="asset_type" class="ticket-form-body-input">
                        <option value="1">BUFFER</option>
                        <option value="2">CRT Unit</option>
                        <option value="3">DASS Unit</option>
                        <option value="4">DEMO Unit</option>
                        <option value="5">DOA</option>
                        <option value="6">Support Unit</option>

                    </select>
                    
                </x-common-ticket-form>
            
        </div>
    </body>

</html>