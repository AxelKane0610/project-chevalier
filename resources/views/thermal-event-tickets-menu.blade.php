<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <div id="thermal-event-tickets-menu">

            <x-common-header title="Thermal Event Exceptional">
                <li>
                    <form action="/main-menu">
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="" class="js-input-required-btn" >
                        <button type="button"><i class="ti-search"></i>Search</button>
                    </form>
                    
                </li>
                
            </x-common-header>

            <div class="thermal-event-tickets-menu-content">

                <form action=""  >
                    <button type="button" class="js-input-required-btn" data-target="create-thermal-event-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                </form>

                <div class="common-table-container">
                    <h2>Pending Tickets</h2>
                    <table id="pending-thermal-event-tickets-table" class="common-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="14%">Receipt</th>
                            <th width="15%">User Owner</th>
                            <th width="39%">Issue Description</th>
                            <th width="11%">Status</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                
                                    <tr>
                                        <td>
                                            <a href="/thermal-event-tickets-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->ticket_receipt }}</td>
                                        <td>{{ $ticket->user_owner->fullname ?? 'N/A' }}</td>
                                        <td>{{ $ticket->description }}</td>
                                        
                                        <td>
                                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                {{ $ticket->status_data['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    
                            @endforeach
                        </tbody>

                    </table>
                </div>
                
            </div>

            <x-common-ticket-form title="Thermal Event Exceptional Form" action1="/create-thermal-event-ticket" id="create-thermal-event-ticket-form">
                <lable>Receipt</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu" name="ticket_receipt" required>

                <label>Serial Number</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="serial_number" required>

                <label>Product Number</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số product máy" name="product_number" required>

                <label>Product Model</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="product_model" required>

                <label>Description</label>
                <input type="text" class="ticket-form-body-input" placeholder="Mô tả issue của máy" name="description" required>

                <label>CDAX ID</label>
                <input type="number" class="ticket-form-body-input" placeholder="Nhập số serial máy" name="cdax_id" required>

                <label>Customer Type</label>
                <select name="customer_type" class="ticket-form-body-input">
                    <option value="1">Khách hàng lẻ</option>
                    <option value="2">Khách hàng công ty/doanh nghiệp</option>
                    <option value="3">T1/Đại lý bán lẻ</option>
                </select>

                <label>Company/Customer Name</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập tên khách hàng/công ty" name="company_customer_name" required>

                <label>Quan sát thực tế</label>
                <input type="text" class="ticket-form-body-input" placeholder="Nhập quan sát thực tế (không nước, không côn trùng, ...)" name="user_observations" required>

                <label>Nhiều part bị ảnh hưởng ?</label>
                <select name="multipart_affected_check" class="ticket-form-body-input" id="multipart_affected_check">
                    <option value="1">Chỉ có 1 part bị ảnh hưởng</option>
                    <option value="2">Có nhiều hơn 1 part bị ảnh hưởng</option>
                </select>

                <div id="thermal_event_parts_details">
                    <label>Part MO Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập số MO của part" name="part_mo_number" required>

                    <label>Part Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập mã part" name="part_number" required>

                    <label>Part Description</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập tên part" name="part_description" required>

                    <label>Part CT Number</label>
                    <input type="text" class="ticket-form-body-input" placeholder="Nhập CT của part, nếu không có để N/A" name="part_ct_number" required>
                </div>
                
                <label>Attach File:</label>
                <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
                <ul id="fileList"></ul>

                <x-slot:footer>
                    <button class="ticket-form-body-input" type="submit" id="thermal-event-ticket-submit-btn">Submit</button> 
                </x-slot:footer>

            </x-common-ticket-form>

        </div>
    </body>

</html>