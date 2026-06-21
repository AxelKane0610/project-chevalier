<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/icons/themify-icons.css', 'resources/js/invoice-exceptional.js'])
        
    </head>

    <body>

        <div id="software-tickets-menu">

            <x-common-header title="Invoice Exceptional">
                <li>
                    <form action="/main-menu">
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="" class="js-input-required-btn" >
                        @csrf
                        <button type="button"><i class="ti-search"></i>Search</button>
                    </form>
                    
                </li>
                
            </x-common-header>

            <div class="invoice-exceptional-tickets-menu-content">

                <form action="" >
                    <button type="button" class="js-input-required-btn" id="create-invoice-exceptional-ticket-btn" data-target="create-invoice-exceptional-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                </form>
                

                <div class="common-table-container">
                    <h2>Pending Tickets</h2>

                    <table class="common-table pending-invoice-exceptional-tickets-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="14%">Receipt</th>
                            <th width="20%">Support Type</th>
                            <th width="39%">Issue Description</th>
                            <th width="11%">Product Model</th>
                            <th width="11%">Status</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                
                                    <tr>
                                        <td>
                                            <a href="/invoice-exceptional-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->ticket_receipt }}</td>
                                        <td>
                                            
                                            <span class="ticket-support-type {{ $ticket->support_type_data['class'] }}">
                                                {{ $ticket->support_type_data['text'] }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->description }}</td>
                                        <td>{{ $ticket->product_model }}</td>
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

        <x-common-ticket-form title="Invoice Exceptional Ticket Form" action1="/create-invoice-exceptional-ticket" id="create-invoice-exceptional-ticket-form">

            <label class="ticket-form-body-input">Receipt</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu" name="ticket_receipt" required>

            <label class="ticket-form-body-input">Invoice Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số hóa đơn" name="invoice_number" required>

            <label class="ticket-form-body-input">Serial Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số serial" name="serial_number" required>

            <label class="ticket-form-body-input">Product Number</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập số product" name="product_number" required>

            <label class="ticket-form-body-input">Product Model</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập model máy" name="product_model" required>

            <label class="ticket-form-body-input">Invoice Date</label>
            <input type="date" class="ticket-form-body-input" placeholder="Nhập ngày của hóa đơn" name="invoice_date" required>

            <label class="ticket-form-body-input">Expired Date</label>
            <input type="date" class="ticket-form-body-input" placeholder="Nhập ngày expired" name="expired_date" required>

            <label class="ticket-form-body-input">Retail Name</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập tên retail" name="retail_name" required>

            <label class="ticket-form-body-input">Company/Customer Name</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập tên công ty/tên khách hàng" name="company_customer_name" required>

            <label class="ticket-form-body-input">Support Type</label>
            <select name="support_type" class="ticket-form-body-input">
                <option value="1">Hóa đơn xuất sau (1 máy)</option>
                <option value="2">Hóa đơn xuất sau (Nhiều máy)</option>
                <option value="3">Kích hoạt bảo hành (1 máy)</option>
                <option value="4">Kích hoạt bảo hành (Nhiều máy)</option>
            </select>


            <label class="ticket-form-body-input">Description</label>
            <textarea name="description" class="ticket-form-body-input multiple-row" placeholder="Nhập mô tả vấn đề bạn cần hỗ trợ" required></textarea>
            
            <label class="ticket-form-body-input">Attach File:</label>
            <input class="ticket-form-body-input" type="file" name="attachments[]" multiple id="fileInput">
            <ul id="fileList"></ul>
            
            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" id="invoice-exceptional-ticket-submit-btn">Submit</button> 
            </x-slot:footer>
            
            <!-- class="ticket-form-body-input" id="software-ticket-submit-btn"-->

        </x-common-ticket-form>
    </body>

</html>