<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/invoice-exceptional.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>
        <div id="out-of-office-tickets-menu">

            <x-common-header title="Out of Office">
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

            <div class="out-of-office-tickets-menu-content">

                <form action=""  >
                    <button type="button" class="js-input-required-btn" id="create-out-of-office-ticket-btn" data-target="create-out-of-office-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                </form>
                

                <div class="common-table-container">
                    <h2>Pending Tickets</h2>

                    <table class="common-table pending-invoice-exceptional-tickets-table" width="100%" >
                        <tr>
                            <th width="5%"></th>
                            <th width="20%">User Owner</th>
                            <th width="20%">Start Date</th>
                            <th width="20%">End Date</th>
                            <th width="20%">Reasons for leave</th>
                            <th width="11%">Status</th>
                        </tr>
                    
                        <tbody>
                            @foreach ($tickets as $ticket)
                                
                                    <tr>
                                        <td>
                                            <a href="/out-of-office-tickets-menu-details/{{ $ticket->id }}">
                                                <button><i class="ti-arrow-right" ></i></button>
                                            </a>
                                        </td>
                                        <td>{{ $ticket->user_owner }}</td>
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
            </div>

        </div>

        
    </body>

</html>