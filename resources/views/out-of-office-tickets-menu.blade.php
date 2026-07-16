<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        <script>
            window.searchReceiptUrl = "{{ route('search.receipt') }}";
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/out-of-office.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>
        <x-common-header title="Out of Office">
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

        <div class="out-of-office-tickets-menu-content">

            <form action="" >
                <button type="button" class="js-input-required-btn" id="create-out-of-office-ticket-btn" data-target="create-out-of-office-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
            </form>
            

            <div class="common-table-container">
                <h2>Pending Tickets</h2>

                <table class="common-table pending-out-of-office-tickets-table" width="100%" >
                    <tr>
                        <th width="5%"></th>
                        <th width="10%">User Owner</th>
                        <th width="10%">Type of Leave</th>
                        <th width="10%">Start Date</th>
                        <th width="10%">End Date</th>
                        <th width="25%">Reasons for leave</th>
                        <th width="10%">Status</th>
                    </tr>
                
                    <tbody>
                        @foreach ($tickets as $ticket)
                            
                            <tr>
                                <td>
                                    <a href="/out-of-office-tickets-menu-details/{{ $ticket->id }}">
                                        <button><i class="ti-arrow-right" ></i></button>
                                    </a>
                                </td>
                                <td>{{ $ticket->user_owner->fullname }}</td>
                                <td>
                                    <span class="ticket-status {{ $ticket->type_of_leave_data['class'] }}">
                                        {{ $ticket->type_of_leave_data['text'] }}
                                    </span>
                                </td>
                                <td>{{ $ticket->start_date }}</td>
                                <td>{{ $ticket->end_date }}</td>
                                <td>{{ $ticket->reasons_for_leave }}</td>
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

            @if($tickets_waiting_approval->count() > 0)
                <div class="common-table-container">
                    <h2>Pending Approval Tickets</h2>

                    <table class="common-table pending-out-of-office-tickets-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="10%">User Owner</th>
                            <th width="10%">Type of Leave</th>
                            <th width="10%">Start Date</th>
                            <th width="10%">End Date</th>
                            <th width="25%">Reasons for leave</th>
                            <th width="10%">Status</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($tickets_waiting_approval as $ticket)
                                
                                <tr>
                                    <td>
                                        <a href="/out-of-office-tickets-menu-details/{{ $ticket->id }}">
                                            <button><i class="ti-arrow-right" ></i></button>
                                        </a>
                                    </td>
                                    <td>{{ $ticket->user_owner->fullname }}</td>
                                    <td>
                                        <span class="ticket-status {{ $ticket->type_of_leave_data['class'] }}">
                                            {{ $ticket->type_of_leave_data['text'] }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->start_date }}</td>
                                    <td>{{ $ticket->end_date }}</td>
                                    <td>{{ $ticket->reasons_for_leave }}</td>
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
                @endif

            <div class="common-table-container">
                <h2>All Tickets</h2>

                <table class="common-table pending-out-of-office-tickets-table" width="100%" >
                    <tr>
                        <th width="5%"></th>
                        <th width="10%">User Owner</th>
                        <th width="10%">Type of Leave</th>
                        <th width="10%">Start Date</th>
                        <th width="10%">End Date</th>
                        <th width="25%">Reasons for leave</th>
                        <th width="10%">Status</th>
                    </tr>
                
                    <tbody>
                        @foreach ($all_tickets as $ticket)
                            
                            <tr>
                                <td>
                                    <a href="/out-of-office-tickets-menu-details/{{ $ticket->id }}">
                                        <button><i class="ti-arrow-right" ></i></button>
                                    </a>
                                </td>
                                <td>{{ $ticket->user_owner->fullname }}</td>
                                <td>
                                    <span class="ticket-status {{ $ticket->type_of_leave_data['class'] }}">
                                        {{ $ticket->type_of_leave_data['text'] }}
                                    </span>
                                </td>
                                <td>{{ $ticket->start_date }}</td>
                                <td>{{ $ticket->end_date }}</td>
                                <td>{{ $ticket->reasons_for_leave }}</td>
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

        <x-common-ticket-form title="Out of Office Ticket Form" action1="/create-out-of-office-ticket " id="create-out-of-office-ticket-form">
            <label class="ticket-form-body-input">Type of leave</label>
            <select name="type_of_leave" class="ticket-form-body-input">
                <option value="1">Xin nghỉ phép</option>
                <option value="2">Xin đi trễ</option>
                <option value="3">Xin về sớm</option>
                <option value="4">Xin không chấm công vào</option>
                <option value="5">Xin không chấm công ra</option>
                <option value="6">Quên chấm công vào/ra</option>
            </select>

            <label class="ticket-form-body-input">Reason for leave</label>
            <input type="text" class="ticket-form-body-input" placeholder="Nhập lý do" name="reasons_for_leave" required>

            <label class="ticket-form-body-input">Days of leave</label>
            <input type="number" class="ticket-form-body-input" placeholder="Nhập số ngày nghỉ.(Ex: 0.5, 1.0, 1.5). Nhập 0.5 nếu đi trễ/về sớm/không chấm công" step="0.5" name="days_of_leave" required>

            <label class="ticket-form-body-input">Start Date</label>
            <input type="datetime-local" class="ticket-form-body-input" name="start_date" required>

            <label class="ticket-form-body-input">End Date</label>
            <input type="datetime-local" class="ticket-form-body-input" name="end_date" required>

            <label class="ticket-form-body-input">Attach File:</label>
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