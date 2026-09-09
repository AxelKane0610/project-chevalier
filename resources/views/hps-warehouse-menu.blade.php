<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/spectre-crown-warehouse.js',  'resources/css/icons/themify-icons.css', ])
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
                                    <h2>Current Status:</h2>
                                    <select id="current-status-filter">
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
                                    <select id="warehouse-filter">
                                        <option value="">All</option>
                                        <option value="1">HCM</option>
                                        <option value="2">HN</option>
                                        <option value="3">DN</option>
                                        <option value="4">CT</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <h2>Tình trạng máy:</h2>
                                    <select id="printer-status-filter">
                                        <option value="">All</option>
                                        <option value="1">New</option>
                                        <option value="2">Good</option>
                                        <option value="3">DOA</option>
                                        <option value="4">Not good</option>
                                        <option value="5">Scrap</option>

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
                                @include('tables.hps-warehouse-items-table')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>




    </body>
</html>