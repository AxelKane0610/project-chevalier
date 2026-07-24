<div class="common-table-container">
    <h2>All Tickets</h2>
    <div class="common-table-filter">
        <div class="search-box">
            <i class="ti-search"></i>
            <input type="text" placeholder="Search Receipt, TTEX Bill" id="search-ttex-bill-input">
        </div>

        <h2>Status:</h2>
        <select id="status-filter">
            <option value="">All</option>
            <option value="1">Open - Chưa điều tin</option>
            <option value="2">Completed - Đã điều tin</option>
            <option value="3">Rejected</option>
            
        </select>

    </div>

    <table id="all-ttex-tickets-table" class="common-table" width="100%" >
        <thead>
            <th width="5%"></th>
            <th width="10%">TTEX Bill</th>
            <th width="10%">User Owner</th>
            <th width="20%">Người gửi</th>
            <th width="20%">Người nhận</th>
            <th width="20%">Mô tả hàng hóa</th>
            <th width="10%">Status</th>

        </thead>
    
        <tbody>
            @foreach ($tickets as $ticket)
                
                    <tr>
                        <td>
                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                <button><i class="ti-arrow-right" ></i></button>
                            </a>
                        </td>
                        <td>{{ $ticket->ttex_bill }}</td>
                        <td>{{ $ticket->user_owner->fullname  }}</td>
                        <td>{{ $ticket->sender_info }}</td>
                        <td>{{ $ticket->receiver_info }}</td>
                        <td>{{ $ticket->shipment_description }}</td>
                        
                        <td>
                            <span class="ticket-status {{ $ticket->status_data['class'] }}">
                                {{ $ticket->status_data['text'] }}
                            </span>
                        </td>
                    </tr>
                    
            @endforeach
        </tbody>

        <div class="d-flex justify-content-end">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>

    </table>
    
</div>