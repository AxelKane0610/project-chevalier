<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/spectre-crown-warehouse.js', 'resources/css/app.css',  'resources/css/icons/themify-icons.css', ])
    
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
                    

                    <table id="spectre-crown-warehouse-items-table" class="common-table" width="100%" >
                        <thead>
                            <th width="5%"></th>
                            <th width="5%">Asset Tag</th>
                            <th width="10%">Serial Number</th>
                            <th width="10%">Box SN</th>
                            <th width="10%">Product Number</th>
                            <th width="20%">Model</th>
                            <th width="10%">Category</th>
                            <th width="10%">Warehouse</th>
                            <th width="10%">Available Status</th>
                            <th width="10%">Condition</th>

                        </thead>
                    
                        <tbody>
                            @foreach ($items as $item)
                                
                                <tr
                                    data-category="{{ $item->category }}"
                                    data-warehouse="{{ $item->warehouse }}"
                                    data-availability="{{ $item->available_status }}"
                                    data-condition="{{ $item->condition }}"
                                >
                                    <td>
                                        <a href="/spectre-crown-warehouse-item-details/{{ $item->id }}">
                                            <button><i class="ti-arrow-right" ></i></button>
                                        </a>
                                    </td>
                                    <td>{{ $item->asset_tag }}</td>
                                    <td>{{ $item->serial_number }}</td>
                                    <td>{{ $item->box_serial_number }}</td>
                                    <td>{{ $item->product_number }}</td>
                                    <td>{{ $item->model }}</td>
                                    
                                    <td>
                                        <span class="ticket-status {{ $item->category_data['class'] }}">
                                            {{ $item->category_data['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ticket-status {{ $item->warehouse_data['class'] }}">
                                            {{ $item->warehouse_data['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ticket-status {{ $item->available_status_data['class'] }}">
                                            {{ $item->available_status_data['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ticket-status {{ $item->condition_data['class'] }}">
                                            {{ $item->condition_data['text'] }}
                                        </span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    {{-- <div class="d-flex justify-content-end"> --}}
                    <div width="100%">
                        {{ $items->links('pagination::bootstrap-5') }}
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