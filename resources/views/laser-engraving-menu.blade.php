<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/laser-engraving.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <x-common-header title="Laser Engraving Support">
            <li>
                <form action="/main-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
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
                    <form action=""  >
                        <button type="button" class="js-input-required-btn" id="create-laser-engraving-ticket-btn" data-target="create-laser-engraving-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                    </form>

                    <button class="btn btn-primary table-btn w-100" id="show-pending-laser-engraving-tickets-btn" data-target = "pending-laser-engraving-tickets-container"><i class="ti-timer"></i> Show Pending Laser Engraving Tickets</button>
                    <button class="btn btn-primary table-btn w-100" id="show-all-laser-engraving-tickets-btn" data-target = "all-laser-engraving-tickets-container"><i class="ti-list-ol"></i> Show All Tickets</button>
                
                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-laser-engraving-tickets-container">

                        <h2>Pending Tickets</h2>

                        <table id="pending-laser-engraving-tickets-table" class="common-table" width="100%" >
                            <tr>
                                <th width="5%"></th>
                                <th width="14%">Receipt</th>
                                <th width="20%">Info Base</th>
                                <th width="39%">Description</th>
                                <th width="11%">Priority</th>
                                <th width="11%">Status</th>
                            </tr>
                        
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    
                                        <tr>
                                            <td>
                                                <a href="/laser-engraving-menu-details/{{ $ticket->id }}">
                                                    <button><i class="ti-arrow-right" ></i></button>
                                                </a>
                                            </td>
                                            <td>{{ $ticket->ticket_receipt }}</td>
                                            <td>{{ $ticket->info_base }}</td>
                                            <td>{{ $ticket->description }}</td>
                                            <td>
                                                <span class="ticket-priority {{ $ticket->priority_data['class'] }}">
                                                    {{ $ticket->priority_data['text'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                    {{ $ticket->status_data['text'] }}
                                                </span></td>
                                        </tr>
                                    
                                @endforeach
                            </tbody>

                        </table>
                        </div>

                        <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-laser-engraving-tickets-container">
                        <h2>All Tickets</h2>
                        <div class="common-table-filter">
                            <div class="search-box">
                                <i class="ti-search"></i>
                                <input type="text" placeholder="Search Receipt, info base" id="search-laser-engraving-input">
                            </div>


                        </div>
                        <table id="all-laser-engraving-tickets-table" class="common-table" width="100%" >
                            <thead>
                                <th width="5%"></th>
                                <th width="14%">Receipt</th>
                                <th width="20%">Info Base</th>
                                <th width="39%">Description</th>
                                <th width="11%">Priority</th>
                                <th width="11%">Status</th>
                            </thead>
                        
                            <tbody>
                                @foreach ($all_tickets as $ticket)
                                    
                                        <tr>
                                            <td>
                                                <a href="/laser-engraving-menu-details/{{ $ticket->id }}">
                                                    <button><i class="ti-arrow-right" ></i></button>
                                                </a>
                                            </td>
                                            <td>{{ $ticket->ticket_receipt }}</td>
                                            <td>{{ $ticket->info_base }}</td>
                                            <td>{{ $ticket->description }}</td>
                                            <td>
                                                <span class="ticket-priority {{ $ticket->priority_data['class'] }}">
                                                    {{ $ticket->priority_data['text'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                                    {{ $ticket->status_data['text'] }}
                                                </span></td>
                                        </tr>
                                    
                                @endforeach
                            </tbody>

                        </table>

                    
                        </div>
                    </div>
                </div>
            </div>
        </div>

            
                


                    

        <x-common-ticket-form title="Laser Engraving Support" action1="/create-laser-engraving-ticket" id="create-laser-engraving-ticket-form"> <!-- action="/create-laser-engraving-ticket" method="POST"> -->

                        <label class="ticket-form-body-input">Receipt</label>
                        <input type="text" class="ticket-form-body-input" placeholder="Nhập số phiếu tại đây" name="ticket_receipt" required>


                        <label class="ticket-form-body-input">Priority</label>
                        <select name="priority" class="ticket-form-body-input">
                            <option value="1">Normal</option>
                            <option value="2">Critical</option>
                            <option value="3">High</option>
                            <option value="4">Low</option>
                        </select>

                        <label class="ticket-form-body-input">Info Base</label>
                        <input type="text" class="ticket-form-body-input" placeholder="Nhập thông tin mặt base cần khắc tại đây" name="info_base" required>

                        <label class="ticket-form-body-input">Description</label>
                        <textarea name="description" class="ticket-form-body-input multiple-row" placeholder="Nhập mô tả vấn đề bạn cần hỗ trợ" required></textarea>
                        
                        <label class="ticket-form-body-input">Attach File:</label>
                        <div class="upload-group ">
                            <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                            <ul class="file-list"></ul>
                        </div>
                        
                        <x-slot:footer>
                            <button class="ticket-form-body-input" type="submit" id="laser-engraving-ticket-submit-btn">Submit</button> 
                        </x-slot:footer>
                        
                        <!-- class="ticket-form-body-input" id="software-ticket-submit-btn"-->

        </x-common-ticket-form>
            

        
    </body>
</html>